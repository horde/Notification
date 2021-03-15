<?php
/**
 * Test the notification class.
 *
 * @category Horde
 * @package  Notification
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Notification;
use Horde_Test_Case;
use Horde_Notification;

/**
 * Test the notification class.
 *
 * Copyright 2009-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category Horde
 * @package  Notification
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

class NotificationTest extends Horde_Test_Case
{
    public function tearDown(): void
    {
        unset($_SESSION);
    }

    public function testMethodSingletonReturnsAlwaysTheSameInstanceForTheSameStackName()
    {
        $notification1 = Horde_Notification::singleton('test');
        $notification2 = Horde_Notification::singleton('test');
        $this->assertSame($notification1, $notification2);
    }

    public function testMethodConstructHasPostconditionThatTheSessionStackGotInitializedAsArray()
    {
        $notification = Horde_Notification_Instance::newInstance('test');
        $this->assertEquals(array(), $_SESSION['test']);
    }
}

class Horde_Notification_Instance extends Horde_Notification
{
    public static function newInstance($stack)
    {
        $storage = new Horde_Notification_Storage_Session($stack);
        return new Horde_Notification_Handler($storage);
    }
}
