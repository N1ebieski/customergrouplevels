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

namespace N1ebieski\CustomerGroupLevels\Form\Type\Admin\Customer;

use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['groups'] as $group) {
            $name = "group_id_{$group['id_group']}_category_ids";

            $builder->add($name, CategoryChoiceTreeType::class, [
                'label' => $this->translator->trans(
                    'Default customer group %customer% by categories',
                    ['%customer%' => $group['name']],
                    'Modules.Customergrouplevels.Categorytype'
                ),
                'multiple' => true,
                'required' => false,
                'constraints' => [
                    new Assert\Type([
                        'type' => 'array',
                    ]),
                    new Assert\All([
                        new Assert\Type([
                            'type' => 'string',
                        ]),
                    ]),
                ],
            ]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     *
     * @throws AccessException
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'groups' => [],
        ]);
    }
}
