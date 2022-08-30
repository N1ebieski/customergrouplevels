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

namespace N1ebieski\CustomerGroupLevels\Tests\Integration\Behaviour\Features\Context;

use Exception;
use PrestaShopDatabaseException;
use PrestaShopException;
use SpecificPrice;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Tests\Integration\Behaviour\Features\Context\ProductFeatureContext as BaseProductFeatureContext;

class ProductFeatureContext extends BaseProductFeatureContext
{
    /**
     * @param string $productName
     * @param string $specificPriceName
     *
     * @return SpecificPrice
     */
    public function getProductsSpecificPriceWithName(string $productName, string $specificPriceName): SpecificPrice
    {
        return $this->specificPrices[$productName][$specificPriceName];
    }

    /**
     * @param string $productName
     * @param string $specificPriceName
     * @param float $specificPricePrice
     * @param int $idShop
     * @param int $idCurrency
     * @param int $idCountry
     * @param int $idGroup
     * @param int $idCustomer
     *
     * @return void
     *
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws PrestaShopException
     * @throws ServiceCircularReferenceException
     * @throws ServiceCircularReferenceException
     * @throws ServiceCircularReferenceException
     * @throws ServiceCircularReferenceException
     * @throws ServiceCircularReferenceException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws ServiceNotFoundException
     * @throws ServiceNotFoundException
     * @throws ServiceNotFoundException
     * @throws ServiceNotFoundException
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     */
    public function productWithNameHasASpecificPriceWithPrice(
        string $productName,
        string $specificPriceName,
        float $specificPricePrice,
        int $idShop = 0,
        int $idCurrency = 0,
        int $idCountry = 0,
        int $idGroup = 0,
        int $idCustomer = 0
    ): void {
        if (isset($this->specificPrices[$productName][$specificPriceName])) {
            throw new \Exception('Product named "' . $productName . '" has already a specific price named "' . $specificPriceName . '"');
        }
        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = $this->getProductWithName($productName)->id;
        $specificPrice->price = $specificPricePrice;
        $specificPrice->reduction = 0;
        $specificPrice->reduction_type = 'amount';
        $specificPrice->from_quantity = 1;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        // set required values (no specific rules applied, the price is for everyone)
        $specificPrice->id_shop = $idShop;
        $specificPrice->id_currency = $idCurrency;
        $specificPrice->id_country = $idCountry;
        $specificPrice->id_group = $idGroup;
        $specificPrice->id_customer = $idCustomer;
        $specificPrice->add();
        $this->specificPrices[$productName][$specificPriceName] = $specificPrice;
    }
}
