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

namespace N1ebieski\CustomerGroupLevels\Entity\CustomerGroupCategory;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *    name="customer_group_category",
 *    indexes={
 *       @ORM\Index(name="ids_customer_group_category", columns={"id_customer", "id_group", "id_category"}),
 *       @ORM\Index(name="id_customer", columns={"id_customer"}),
 *       @ORM\Index(name="id_group", columns={"id_group"}),
 *       @ORM\Index(name="id_category", columns={"id_category"})
 *    }
 * )
 * @ORM\Entity(repositoryClass="N1ebieski\CustomerGroupLevels\Repository\CustomerGroupCategory\CustomerGroupCategoryRepository")
 */
class CustomerGroupCategory
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_customer_group_category", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id_customer_group_category;

    /**
     * @var int
     *
     * @ORM\Column(name="id_customer", type="integer", options={"unsigned":true})
     */
    private $id_customer;

    /**
     * @var int
     *
     * @ORM\Column(name="id_group", type="integer", options={"unsigned":true})
     */
    private $id_group;

    /**
     * @var int
     *
     * @ORM\Column(name="id_category", type="integer", options={"unsigned":true})
     */
    private $id_category;

    /**
     * @return static
     */
    public function make()
    {
        return new static();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id_customer_group_category;
    }

    /**
     * @return int
     */
    public function getIdCustomer()
    {
        return $this->id_customer;
    }

    /**
     * @param int $id_customer
     *
     * @return static
     */
    public function setIdCustomer(int $id_customer)
    {
        $this->id_customer = $id_customer;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdGroup()
    {
        return $this->id_group;
    }

    /**
     * @param int $id_group
     *
     * @return static
     */
    public function setIdGroup(int $id_group)
    {
        $this->id_group = $id_group;

        return $this;
    }

    /**
     * @return int
     */
    public function getIdCategory()
    {
        return $this->id_category;
    }

    /**
     * @param int $id_category
     *
     * @return static
     */
    public function setIdCategory(int $id_category)
    {
        $this->id_category = $id_category;

        return $this;
    }
}
