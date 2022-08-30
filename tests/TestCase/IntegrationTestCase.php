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

namespace N1ebieski\CustomerGroupLevels\Tests\TestCase;

use Tests\TestCase\SymfonyIntegrationTestCase;

abstract class IntegrationTestCase extends SymfonyIntegrationTestCase
{
    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../../../../config/config.inc.php';
    }
}
