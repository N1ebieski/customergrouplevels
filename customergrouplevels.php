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

use N1ebieski\CustomerGroupLevels\Entity\CustomerGroupCategory\CustomerGroupCategory;
use N1ebieski\CustomerGroupLevels\Form\Type\Admin\Customer\CategoryType;
use N1ebieski\CustomerGroupLevels\Repository\CustomerGroupCategory\CustomerGroupCategoryRepository;
use N1ebieski\CustomerGroupLevels\Service\CustomerGroupCategory\CustomerGroupCategoryService;
use Symfony\Component\Form\FormBuilderInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class CustomerGroupLevels extends Module
{
    public function __construct()
    {
        $this->name = 'customergrouplevels';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Mariusz Wysokiński';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.8',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Customer Group Levels', [], 'Modules.Customergrouplevels.Customergrouplevels');
        $this->description = $this->trans('Prestashop module for assigning group levels to users depending on product categories', [], 'Modules.Customergrouplevels.Customergrouplevels');
        $this->description_full = $this->trans('Prestashop module for assigning group levels to users depending on product categories. It allows you to set different prices for products to various users depending on their groups in specific categories', [], 'Modules.Customergrouplevels.Customergrouplevels');
    }

    /**
     * @return bool
     */
    public function isUsingNewTranslationSystem(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function install(): bool
    {
        $result = Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'customer_group_category` (
                `id_customer_group_category` int NOT NULL AUTO_INCREMENT,
                `id_customer` int unsigned NOT NULL,
                `id_group` int unsigned NOT NULL,
                `id_category` int unsigned NOT NULL,
                PRIMARY KEY (`id_customer_group_category`),
                KEY `ids_customer_group_category` (`id_customer`,`id_group`,`id_category`),
                KEY `id_customer` (`id_customer`),
                KEY `id_group` (`id_group`),
                KEY `id_category` (`id_category`),
                CONSTRAINT `FK_customer_group_category_category` FOREIGN KEY (`id_category`) REFERENCES `category` (`id_category`) ON DELETE CASCADE ON UPDATE RESTRICT,
                CONSTRAINT `FK_customer_group_category_customer` FOREIGN KEY (`id_customer`) REFERENCES `customer` (`id_customer`) ON DELETE CASCADE ON UPDATE RESTRICT,
                CONSTRAINT `FK_customer_group_category_group` FOREIGN KEY (`id_group`) REFERENCES `group` (`id_group`) ON DELETE CASCADE ON UPDATE RESTRICT
            ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;'
        );

        return $result && parent::install()
            && $this->registerHook('actionAdminControllerSetMedia')
            && $this->registerHook('actionCustomerFormBuilderModifier')
            && $this->registerHook('actionAfterCreateCustomerFormHandler')
            && $this->registerHook('actionAfterUpdateCustomerFormHandler');
    }

    /**
     * @return bool
     */
    public function uninstall(): bool
    {
        $result = Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'customer_group_category`;');

        return $result && parent::uninstall();
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addJS($this->_path . 'public/js/admin/scripts.js');
    }

    public function hookActionCustomerFormBuilderModifier(array $params)
    {
        /**
         * @var Cookie
         */
        $cookie = $params['cookie'];

        $groups = Group::getGroups($cookie->id_lang);

        /**
         * @var CustomerGroupCategoryRepository
         */
        $customerGroupCategoryRepository = $this->get('n1ebieski.customergrouplevels.customergroupcategory_repository');

        if (array_key_exists('id', $params)) {
            $customerGroupCategories = $customerGroupCategoryRepository->findBy(['id_customer' => $params['id']]);

            foreach ($groups as $group) {
                $params['data']["group_id_{$group['id_group']}_category_ids"] =
                    array_map(function (CustomerGroupCategory $customerGroupCategory) use ($group) {
                        if ($customerGroupCategory->getIdGroup() == $group['id_group']) {
                            return $customerGroupCategory->getIdCategory();
                        }
                    }, $customerGroupCategories);
            }
        }

        /**
         * @var FormBuilderInterface
         */
        $formBuilder = $params['form_builder'];

        /**
         * @var CategoryType
         */
        $categoryType = $this->get('n1ebieski.customergrouplevels.admin_customer_category_type');

        $categoryType->buildForm($formBuilder->setData($params['data']), [
            'groups' => $groups,
        ]);
    }

    public function hookActionAfterCreateCustomerFormHandler(array $params)
    {
        /**
         * @var Cookie
         */
        $cookie = $params['cookie'];

        /**
         * @var CustomerGroupCategoryService
         */
        $customerGroupCategoryService = $this->get('n1ebieski.customergrouplevels.customergroupcategory_service');

        $groups = Group::getGroups($cookie->id_lang);

        foreach ($groups as $group) {
            $categoryIds = $params['form_data']["group_id_{$group['id_group']}_category_ids"];

            if (is_array($categoryIds) && !empty($categoryIds)) {
                $categories = Category::getCategories(
                    false,
                    true,
                    false,
                    'AND `c`.`id_category` IN (' . pSQL(implode(',', $categoryIds)) . ')'
                );

                $categoryIds = array_map(fn (array $category) => (int) $category['id_category'], $categories);
            }

            $customerGroupCategoryService->attach(
                (int) $params['id'],
                (int) $group['id_group'],
                $categoryIds ?? []
            );
        }
    }

    public function hookActionAfterUpdateCustomerFormHandler(array $params)
    {
        /**
         * @var Cookie
         */
        $cookie = $params['cookie'];

        /**
         * @var CustomerGroupCategoryService
         */
        $customerGroupCategoryService = $this->get('n1ebieski.customergrouplevels.customergroupcategory_service');

        $groups = Group::getGroups($cookie->id_lang);

        foreach ($groups as $group) {
            $categoryIds = $params['form_data']["group_id_{$group['id_group']}_category_ids"];

            if (is_array($categoryIds) && !empty($categoryIds)) {
                $categories = Category::getCategories(
                    false,
                    true,
                    false,
                    'AND `c`.`id_category` IN (' . pSQL(implode(',', $categoryIds)) . ')'
                );

                $categoryIds = array_map(fn (array $category) => (int) $category['id_category'], $categories);
            }

            $customerGroupCategoryService->sync(
                (int) $params['id'],
                (int) $group['id_group'],
                $categoryIds ?? []
            );
        }
    }
}
