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

namespace N1ebieski\CustomerGroupLevels\StaticCache\CustomerGroupCategory;

use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use N1ebieski\CustomerGroupLevels\Entity\CustomerGroupCategory\CustomerGroupCategory;
use N1ebieski\CustomerGroupLevels\Repository\CustomerGroupCategory\CustomerGroupCategoryRepository;

class CustomerGroupCategoryStaticCache
{
    /**
     * @var array
     */
    protected static $cache = [];

    /**
     * @var CustomerGroupCategoryRepository
     */
    protected $customerGroupCategoryRepository;

    /**
     * Constructor.
     *
     * @param CustomerGroupCategoryRepository $customerGroupCategoryRepository
     */
    public function __construct(CustomerGroupCategoryRepository $customerGroupCategoryRepository)
    {
        $this->customerGroupCategoryRepository = $customerGroupCategoryRepository;
    }

    /**
     * @return bool
     */
    public static function flushCache(): bool
    {
        self::$cache = [];

        return true;
    }

    /**
     * @param int $id_customer
     * @param int $id_product
     *
     * @return CustomerGroupCategory|null
     *
     * @throws InvalidArgumentException
     * @throws ORMException
     */
    public function findOneByIdCustomerAndIdProduct(int $id_customer, int $id_product): ?CustomerGroupCategory
    {
        $key = "findOneByIdCustomerAndIdProduct.{$id_customer}.{$id_product}";

        if (array_key_exists($key, static::$cache)) {
            return static::$cache[$key];
        }

        static::$cache[$key] = $this->customerGroupCategoryRepository->findOneByIdCustomerAndIdProduct(
            $id_customer,
            $id_product
        );

        return static::$cache[$key];
    }
}
