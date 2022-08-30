(()=>{"use strict";
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
const{$:e}=window;class t{constructor(t){return this.$container=e(t),this.$container.on("click",".js-input-wrapper",(t=>{const s=e(t.currentTarget);this.toggleChildTree(s)})),this.$container.on("click",".js-toggle-choice-tree-action",(t=>{const s=e(t.currentTarget);this.toggleTree(s)})),{enableAutoCheckChildren:()=>this.enableAutoCheckChildren(),enableAllInputs:()=>this.enableAllInputs(),disableAllInputs:()=>this.disableAllInputs()}}enableAutoCheckChildren(){this.$container.on("change",'input[type="checkbox"]',(t=>{const s=e(t.currentTarget);s.closest("li").find('ul input[type="checkbox"]').prop("checked",s.is(":checked"))}))}enableAllInputs(){this.$container.find("input").removeAttr("disabled")}disableAllInputs(){this.$container.find("input").attr("disabled","disabled")}toggleChildTree(e){const t=e.closest("li");t.hasClass("expanded")?t.removeClass("expanded").addClass("collapsed"):t.hasClass("collapsed")&&t.removeClass("collapsed").addClass("expanded")}toggleTree(t){const s=t.closest(".js-choice-tree-container"),a=t.data("action"),l={addClass:{expand:"expanded",collapse:"collapsed"},removeClass:{expand:"collapsed",collapse:"expanded"},nextAction:{expand:"collapse",collapse:"expand"},text:{expand:"collapsed-text",collapse:"expanded-text"},icon:{expand:"collapsed-icon",collapse:"expanded-icon"}};s.find("li").each(((t,s)=>{const n=e(s);n.hasClass(l.removeClass[a])&&n.removeClass(l.removeClass[a]).addClass(l.addClass[a])})),t.data("action",l.nextAction[a]),t.find(".material-icons").text(t.data(l.icon[a])),t.find(".js-toggle-text").text(t.data(l.text[a]))}}
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
$((function(){let e={};$('[id^="customer_group_id_"]').each((function(s){e[s]=new t("#"+$(this).attr("id")),e[s].enableAutoCheckChildren()}))}))})();