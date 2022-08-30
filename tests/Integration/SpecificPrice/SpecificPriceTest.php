<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2022 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

declare(strict_types=1);

namespace N1ebieski\CustomerGroupLevels\Tests\Integration\SpecificPrice;

use Context;
use N1ebieski\CustomerGroupLevels\Service\CustomerGroupCategory\CustomerGroupCategoryService;
use N1ebieski\CustomerGroupLevels\StaticCache\CustomerGroupCategory\CustomerGroupCategoryStaticCache;
use N1ebieski\CustomerGroupLevels\Tests\Integration\Behaviour\Features\Context\GroupFeatureContext;
use N1ebieski\CustomerGroupLevels\Tests\Integration\Behaviour\Features\Context\ProductFeatureContext;
use N1ebieski\CustomerGroupLevels\Tests\TestCase\IntegrationTestCase;
use SpecificPrice;
use Tests\Integration\Behaviour\Features\Context\CategoryFeatureContext;
use Tests\Integration\Behaviour\Features\Context\CustomerFeatureContext;

class SpecificPriceTest extends IntegrationTestCase
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->context = Context::getContext();
    }

    public function testOverrideIdGroup()
    {
        $groupContext = new GroupFeatureContext();
        $groupContext->createGroup('Test');

        $group = $groupContext->getGroupWithName('Test');

        $email = uniqid() . '@test.com';

        $customerContext = new CustomerFeatureContext();
        $customerContext->createCustomer('Test', $email);

        $customer = $customerContext->getCustomerWithName('Test');
        $customer->addGroups([$group->id]);

        $this->context->customer = $customer;

        $this->assertTrue($customer->id_default_group !== $group->id);

        $categoryContext = new CategoryFeatureContext();
        $categoryContext->createCategory('Test');

        $category = $categoryContext->getCategoryWithName('Test');

        $productContext = new ProductFeatureContext();
        $productContext->thereIsAProductWithNameAndPriceAndQuantity('Test', 50, 0);
        $productContext->productWithNameHasASpecificPriceWithPrice('Test', 'Test', 20, 0, 0, 0, (int) $group->id);

        $product = $productContext->getProductWithName('Test');
        $product->addToCategories([$category->id]);

        $specificPrice = $productContext->getProductsSpecificPriceWithName('Test', 'Test');

        $this->assertFalse($product->getPrice() === (float) $specificPrice->price);

        SpecificPrice::flushCache();
        CustomerGroupCategoryStaticCache::flushCache();

        /**
         * @var CustomerGroupCategoryService
         */
        $customerGroupService = $this->container->get('n1ebieski.customergrouplevels.customergroupcategory_service');

        $customerGroupService->attach((int) $customer->id, (int) $group->id, [(int) $category->id]);

        $this->assertTrue($product->getPrice() === (float) $specificPrice->price);
    }
}
