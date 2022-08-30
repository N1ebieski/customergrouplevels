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

namespace N1ebieski\CustomerGroupLevels\Tests\Integration\Admin\Customer;

use Configuration;
use Context;
use Customer;
use Doctrine\DBAL\ConnectionException;
use DOMException;
use Exception;
use LogicException;
use N1ebieski\CustomerGroupLevels\Entity\CustomerGroupCategory\CustomerGroupCategory;
use N1ebieski\CustomerGroupLevels\Repository\CustomerGroupCategory\CustomerGroupCategoryRepository;
use N1ebieski\CustomerGroupLevels\Service\CustomerGroupCategory\CustomerGroupCategoryService;
use N1ebieski\CustomerGroupLevels\Tests\Integration\Behaviour\Features\Context\GroupFeatureContext;
use N1ebieski\CustomerGroupLevels\Tests\TestCase\IntegrationTestCase;
use PHPUnit\Framework\ExpectationFailedException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerDeleteMethod;
use PrestaShopDatabaseException;
use PrestaShopException;
use RuntimeException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Tests\Integration\Behaviour\Features\Context\CategoryFeatureContext;
use Tests\Integration\Behaviour\Features\Context\CustomerFeatureContext;

class CustomerTest extends IntegrationTestCase
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var CsrfTokenManager
     */
    protected $tokenManager;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenManager = $this->container->get('security.csrf.token_manager');

        $this->router = $this->container->get('router');

        $this->context = Context::getContext();
    }

    public function testCreate(): void
    {
        $groupContext = new GroupFeatureContext();
        $groupContext->createGroup('Test');

        $group = $groupContext->getGroupWithName('Test');

        $this->client->request(
            'GET',
            $this->router->generate('admin_customers_create')
        );

        $response = $this->client->getResponse();

        $this->assertStringContainsString(
            'name="customer[group_id_' . $group->id . '_category_ids][]"',
            $response->getContent()
        );
    }

    public function testStore(): void
    {
        $groupContext = new GroupFeatureContext();
        $groupContext->createGroup('Test');

        $group = $groupContext->getGroupWithName('Test');

        $categoryContext = new CategoryFeatureContext();
        $categoryContext->createCategory('Test');

        $category = $categoryContext->getCategoryWithName('Test');

        $token = $this->tokenManager->getToken('customer');

        $email = uniqid() . '@test.com';

        $this->client->request(
            'POST',
            $this->router->generate('admin_customers_create'),
            [
                'customer' => [
                    'first_name' => 'Test',
                    'last_name' => 'Test',
                    'email' => $email,
                    'password' => uniqid(),
                    'group_ids' => [Configuration::get('PS_CUSTOMER_GROUP')],
                    'default_group_id' => Configuration::get('PS_CUSTOMER_GROUP'),
                    'group_id_' . $group->id . '_category_ids' => [$category->id],
                    '_token' => $token->getValue(),
                ],
            ]
        );

        $customerId = Customer::customerExists($email, true);

        /** @var CustomerGroupCategoryRepository */
        $customerGroupCategoryRepository = $this->container->get('n1ebieski.customergrouplevels.customergroupcategory_repository');

        $customerGroupCategory = $customerGroupCategoryRepository->findOneBy([
            'id_customer' => $customerId,
            'id_group' => $group->id,
            'id_category' => $category->id,
        ]);

        $this->assertTrue($customerGroupCategory instanceof CustomerGroupCategory);
    }

    public function testEdit(): void
    {
        $groupContext = new GroupFeatureContext();
        $groupContext->createGroup('Test');

        $group = $groupContext->getGroupWithName('Test');

        $email = uniqid() . '@test.com';

        $customerContext = new CustomerFeatureContext();
        $customerContext->createCustomer('Test', $email);

        $customer = $customerContext->getCustomerWithName('Test');

        $this->client->request(
            'GET',
            $this->router->generate('admin_customers_edit', [
                'customerId' => $customer->id,
            ])
        );

        $response = $this->client->getResponse();

        $this->assertStringContainsString(
            'name="customer[group_id_' . $group->id . '_category_ids][]"',
            $response->getContent()
        );
    }

    public function testUpdate(): void
    {
        $groupContext = new GroupFeatureContext();
        $groupContext->createGroup('Test');

        $group = $groupContext->getGroupWithName('Test');

        $email = uniqid() . '@test.com';

        $customerContext = new CustomerFeatureContext();
        $customerContext->createCustomer('Test', $email);

        $customer = $customerContext->getCustomerWithName('Test');

        $categoryContext = new CategoryFeatureContext();
        $categoryContext->createCategory('Test');

        $category = $categoryContext->getCategoryWithName('Test');

        $token = $this->tokenManager->getToken('customer');

        $this->client->request(
            'POST',
            $this->router->generate('admin_customers_edit', [
                'customerId' => $customer->id,
            ]),
            [
                'customer' => [
                    'first_name' => $customer->firstname,
                    'last_name' => $customer->lastname,
                    'email' => $customer->email,
                    'group_ids' => $customer->getGroups(),
                    'default_group_id' => $customer->id_default_group,
                    'group_id_' . $group->id . '_category_ids' => [$category->id],
                    '_token' => $token->getValue(),
                ],
            ]
        );

        /** @var CustomerGroupCategoryRepository */
        $customerGroupCategoryRepository = $this->container->get('n1ebieski.customergrouplevels.customergroupcategory_repository');

        $customerGroupCategory = $customerGroupCategoryRepository->findOneBy([
            'id_customer' => $customer->id,
            'id_group' => $group->id,
            'id_category' => $category->id,
        ]);

        $this->assertTrue($customerGroupCategory instanceof CustomerGroupCategory);
    }

    /**
     * @return array
     */
    public function actionDeleteProvider(): array
    {
        return [
            [
                'action' => 'admin_customers_delete',
                'form' => 'delete_customers',
                'field' => 'customers_to_delete',
                'entity' => 'customer',
                'field_method' => 'delete_method',
                'method' => CustomerDeleteMethod::ALLOW_CUSTOMER_REGISTRATION,
            ],
            // [
            //     'action' => 'admin_groups_delete',
            //     'method' => null,
            //     'form' => 'delete_groups'
            // ],
            [
                'action' => 'admin_categories_delete',
                'form' => 'delete_categories',
                'field' => 'categories_to_delete',
                'entity' => 'category',
                'fieldMethod' => 'delete_mode',
                'method' => CategoryDeleteMode::REMOVE_ASSOCIATED_PRODUCTS,
            ],
        ];
    }

    /**
     * @dataProvider actionDeleteProvider
     *
     * @param string $action
     * @param string $form
     * @param string $field
     * @param string $entity
     * @param string $fieldMethod
     * @param string $method
     *
     * @return void
     *
     * @throws ServiceCircularReferenceException
     * @throws InvalidArgumentException
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws ServiceNotFoundException
     * @throws ConnectionException
     * @throws Exception
     * @throws RuntimeException
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     * @throws TokenNotFoundException
     * @throws RouteNotFoundException
     * @throws MissingMandatoryParametersException
     * @throws InvalidParameterException
     * @throws LogicException
     * @throws DOMException
     */
    public function testDelete(
        string $action,
        string $form,
        string $field,
        string $entity,
        string $fieldMethod = null,
        string $method = null
    ): void {
        $groupContext = new GroupFeatureContext();
        $groupContext->createGroup('Test');

        $group = $groupContext->getGroupWithName('Test');

        $email = uniqid() . '@test.com';

        $customerContext = new CustomerFeatureContext();
        $customerContext->createCustomer('Test', $email);

        $customer = $customerContext->getCustomerWithName('Test');
        $customer->addGroups([$group->id]);

        $categoryContext = new CategoryFeatureContext();
        $categoryContext->createCategory('Test');

        $category = $categoryContext->getCategoryWithName('Test');

        /**
         * @var CustomerGroupCategoryService
         */
        $customerGroupService = $this->container->get('n1ebieski.customergrouplevels.customergroupcategory_service');

        $customerGroupService->attach((int) $customer->id, (int) $group->id, [(int) $category->id]);

        /** @var CustomerGroupCategoryRepository */
        $customerGroupCategoryRepository = $this->container->get('n1ebieski.customergrouplevels.customergroupcategory_repository');

        $customerGroupCategory = $customerGroupCategoryRepository->findOneBy([
            'id_customer' => $customer->id,
            'id_group' => $group->id,
            'id_category' => $category->id,
        ]);

        $this->assertTrue($customerGroupCategory instanceof CustomerGroupCategory);

        $token = $this->tokenManager->getToken($form);

        $this->client->request(
            'POST',
            $this->router->generate($action),
            [
                $form => array_merge([
                    $field => [${$entity}->id],
                    '_token' => $token->getValue(),
                ], $method !== null ? [
                    $fieldMethod => $method,
                ] : []),
            ]
        );

        $customerGroupCategory = $customerGroupCategoryRepository->findOneBy([
            'id_customer' => $customer->id,
            'id_group' => $group->id,
            'id_category' => $category->id,
        ]);

        $this->assertTrue($customerGroupCategory === null);
    }
}
