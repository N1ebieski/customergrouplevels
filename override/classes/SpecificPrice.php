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

use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;
use N1ebieski\CustomerGroupLevels\Entity\CustomerGroupCategory\CustomerGroupCategory;
use N1ebieski\CustomerGroupLevels\StaticCache\CustomerGroupCategory\CustomerGroupCategoryStaticCache;

class SpecificPrice extends SpecificPriceCore
{
    /**
     * @return ContainerInterface
     *
     * @throws ContainerNotFoundException
     */
    public static function getContainer(): ContainerInterface
    {
        $finder = new ContainerFinder(Context::getContext());

        return $finder->getContainer();
    }

    /**
     * Returns the specificPrice information related to a given productId and context.
     *
     * @param int $id_product
     * @param int $id_shop
     * @param int $id_currency
     * @param int $id_country
     * @param int $id_group
     * @param int $quantity
     * @param int $id_product_attribute
     * @param int $id_customer
     * @param int $id_cart
     * @param int $real_quantity
     *
     * @return array
     */
    public static function getSpecificPrice(
        $id_product,
        $id_shop,
        $id_currency,
        $id_country,
        $id_group,
        $quantity,
        $id_product_attribute = null,
        $id_customer = 0,
        $id_cart = 0,
        $real_quantity = 0
    ) {
        if ($id_customer !== null) {
            $container = self::getContainer();

            /**
             * @var CustomerGroupCategoryStaticCache
             */
            $customerGroupCategoryStaticCache = $container->get('n1ebieski.customergrouplevels.customergroupcategory_staticcache');

            $customerGroupCategory = $customerGroupCategoryStaticCache->findOneByIdCustomerAndIdProduct(
                (int) $id_customer,
                (int) $id_product
            );

            if ($customerGroupCategory instanceof CustomerGroupCategory) {
                $id_group = $customerGroupCategory->getIdGroup();
            }
        }

        return parent::getSpecificPrice(
            $id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            $quantity,
            $id_product_attribute,
            $id_customer,
            $id_cart,
            $real_quantity
        );
    }
}
