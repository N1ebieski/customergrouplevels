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

use Context;
use Group;
use PrestaShopDatabaseException;
use PrestaShopException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Tests\Integration\Behaviour\Features\Context\AbstractPrestaShopFeatureContext;

class GroupFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var Group[]
     */
    protected $groups = [];

    /**
     * @var int
     */
    private $defaultLanguageId;

    public function __construct()
    {
        $this->defaultLanguageId = (int) \Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * @param string $groupName
     *
     * @return void
     *
     * @throws ServiceCircularReferenceException
     * @throws InvalidArgumentException
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function createGroup(string $groupName): void
    {
        $idLang = (int) Context::getContext()->language->id;

        $group = new Group();
        /* @phpstan-ignore-next-line */
        $group->name = [$idLang => $groupName];
        $group->price_display_method = Group::PRICE_DISPLAY_METHOD_TAX_EXCL;
        $group->add();

        $this->groups[$groupName] = $group;
    }

    /**
     * @param string $groupName
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function checkGroupWithNameExists(string $groupName): void
    {
        $this->checkFixtureExists($this->groups, 'Group', $groupName);
    }

    /**
     * @param string $groupName
     *
     * @return Group
     */
    public function getGroupWithName(string $groupName): Group
    {
        return $this->groups[$groupName];
    }

    /**
     * @return void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function cleanGroupFixtures(): void
    {
        foreach ($this->groups as $group) {
            $group->delete();
        }

        $this->groups = [];
    }
}
