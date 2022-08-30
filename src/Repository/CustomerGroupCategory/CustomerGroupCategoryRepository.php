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

namespace N1ebieski\CustomerGroupLevels\Repository\CustomerGroupCategory;

use InvalidArgumentException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use N1ebieski\CustomerGroupLevels\Entity\CustomerGroupCategory\CustomerGroupCategory;

class CustomerGroupCategoryRepository extends EntityRepository
{
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
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->_entityName, 'cgc');

        $query = $this->_em->createNativeQuery(
            'SELECT cgc.* FROM ' . _DB_PREFIX_ . 'customer_group_category cgc 
            INNER JOIN category_product cp 
                ON cp.id_category = cgc.id_category 
            INNER JOIN specific_price sp
                ON sp.id_group = cgc.id_group
            WHERE cp.id_product = :id_product 
            AND cgc.id_customer = :id_customer 
            ORDER BY cgc.id_group DESC 
            LIMIT 1',
            $rsm
        );

        $query->setParameters([
            'id_product' => $id_product,
            'id_customer' => $id_customer,
        ]);

        $result = $query->getResult();

        if (empty($result)) {
            return null;
        }

        return $result[0];
    }
}
