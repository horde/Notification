<?php

declare(strict_types=1);

namespace Horde\Notification\Test;

use Horde_Notification;
use Horde_Notification_Handler;
use Horde_Notification_Storage_Object;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Horde_Notification_Storage_Session;

#[CoversClass(Horde_Notification::class)]
#[CoversClass(Horde_Notification_Handler::class)]
class NotificationTest extends TestCase
{
    public function tearDown(): void
    {
        unset($_SESSION);
    }

    public function testMethodSingletonReturnsAlwaysTheSameInstanceForTheSameStackName(): void
    {
        $notification1 = Horde_Notification::singleton('test');
        $notification2 = Horde_Notification::singleton('test');
        $this->assertSame($notification1, $notification2);
    }

    public function testMethodConstructHasPostconditionThatTheSessionStackGotInitializedAsArray(): void
    {
        $notification = Horde_Notification_Instance::newInstance('test');
        $this->assertEquals([], $_SESSION['test']);
    }
}

class Horde_Notification_Instance extends Horde_Notification
{
    public static function newInstance(string $stack): Horde_Notification_Handler
    {
        $storage = new Horde_Notification_Storage_Session($stack);
        return new Horde_Notification_Handler($storage);
    }
}
