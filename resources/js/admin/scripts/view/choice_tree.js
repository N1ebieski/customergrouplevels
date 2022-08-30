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
import ChoiceTree from '../../../../../../../admin-dev/themes/new-theme/js/components/form/choice-tree';

$(function() {
    let choiceTree = {};

    $('[id^="customer_group_id_"]').each(function (index) {
        choiceTree[index] = new ChoiceTree('#' + $(this).attr('id'));

        choiceTree[index].enableAutoCheckChildren();
    });
});