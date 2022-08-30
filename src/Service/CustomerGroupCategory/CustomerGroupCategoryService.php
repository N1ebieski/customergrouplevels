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

namespace N1ebieski\CustomerGroupLevels\Service\CustomerGroupCategory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManager;
use Exception;
use N1ebieski\CustomerGroupLevels\Entity\CustomerGroupCategory\CustomerGroupCategory;

class CustomerGroupCategoryService
{
    /**
     * @var CustomerGroupCategory
     */
    protected $customerGroupCategory;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @param EntityManager $entityManager
     * @param CustomerGroupCategory $customerGroupCategory
     *
     * @return void
     */
    public function __construct(EntityManager $entityManager, CustomerGroupCategory $customerGroupCategory)
    {
        $this->entityManager = $entityManager;
        $this->db = $entityManager->getConnection();

        $this->customerGroupCategory = $customerGroupCategory;
    }

    /**
     * @param int $id_customer
     * @param int $id_group
     * @param array|null $ids_category
     *
     * @return int
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function sync(int $id_customer, int $id_group, array $ids_category = null): int
    {
        $affected = 0;

        $this->db->beginTransaction();

        try {
            $this->clear($id_customer, $id_group);

            if ($ids_category !== null) {
                $affected = $this->attach($id_customer, $id_group, $ids_category);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();

            throw $e;
        }

        return $affected;
    }

    /**
     * @param int $id_customer
     * @param int $id_group
     *
     * @return int
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function clear(int $id_customer, int $id_group): int
    {
        $this->db->beginTransaction();

        try {
            $queryBuilder = $this->entityManager->createQueryBuilder();

            $queryBuilder->delete(get_class($this->customerGroupCategory), 'cgc')
                ->where('cgc.id_customer = :id_customer')
                ->andWhere('cgc.id_group = :id_group')
                ->setParameters([
                    'id_customer' => $id_customer,
                    'id_group' => $id_group,
                ]);

            $affected = $queryBuilder->getQuery()->execute();

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();

            throw $e;
        }

        return $affected;
    }

    /**
     * @param int $id_customer
     * @param int $id_group
     * @param array $ids_category
     *
     * @return int
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function attach(int $id_customer, int $id_group, array $ids_category): int
    {
        $affected = 0;

        $this->db->beginTransaction();

        try {
            foreach ($ids_category as $id_category) {
                $this->create([
                    'id_customer' => $id_customer,
                    'id_group' => $id_group,
                    'id_category' => $id_category,
                ]);

                ++$affected;
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();

            throw $e;
        }

        return $affected;
    }

    /**
     * @param array $attributes
     *
     * @return CustomerGroupCategory
     *
     * @throws ConnectionException
     * @throws Exception
     */
    public function create(array $attributes): CustomerGroupCategory
    {
        $this->db->beginTransaction();

        try {
            $customerGroupCategory = $this->customerGroupCategory->make();

            $customerGroupCategory->setIdCustomer($attributes['id_customer']);
            $customerGroupCategory->setIdGroup($attributes['id_group']);
            $customerGroupCategory->setIdCategory($attributes['id_category']);

            $this->entityManager->persist($customerGroupCategory);

            $this->entityManager->flush();

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();

            throw $e;
        }

        return $customerGroupCategory;
    }
}
