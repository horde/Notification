<?php

declare(strict_types=1);

namespace Horde\Notification\Test;

use Horde_Notification_Listener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_Notification_Listener::class)]
class ListenerTest extends TestCase
{
    public function testMethodHandleHasResultBooleanFalse(): void
    {
        $listener = new Horde_Notification_Listener_Mock();
        $this->assertFalse($listener->handles('test'));
    }

    public function testMethodHandleHasEventClassName(): void
    {
        $listener = new Horde_Notification_Listener_Mock();
        $this->assertEquals('Horde_Notification_Event', $listener->handles('mock'));
    }

    public function testMethodHandleHasEventClassNameIfItMatchesAsteriskExpression(): void
    {
        $listener = new Horde_Notification_Listener_Mock();
        $listener->addType('t*', 'Test_Event');
        $this->assertEquals('Test_Event', $listener->handles('test'));
    }

    public function testMethodGetnameHasResultStringTheNameOfTheListener(): void
    {
        $listener = new Horde_Notification_Listener_Mock();
        $this->assertEquals('mock', $listener->getName());
    }
}

class Horde_Notification_Listener_Mock extends Horde_Notification_Listener
{
    protected $_handles = ['mock' => 'Horde_Notification_Event'];
    protected $_name = 'mock';

    public function notify($events, $options = []): void {}
}
