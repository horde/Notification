<?php

declare(strict_types=1);

namespace Horde\Notification\Test\Listener;

use Horde_Notification_Listener_Status;
use Horde_Notification_Event;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_Notification_Listener_Status::class)]
class StatusTest extends TestCase
{
    public function testMethodHandleHasEventClassForHordeMessages(): void
    {
        $listener = new Horde_Notification_Listener_Status();
        $this->assertEquals('Horde_Notification_Event_Status', $listener->handles('status'));
    }

    public function testMethodGetnameHasResultStringStatus(): void
    {
        $listener = new Horde_Notification_Listener_Status();
        $this->assertEquals('status', $listener->getName());
    }

    public function testMethodNotifyHasNoOutputIfTheMessageStackIsEmpty(): void
    {
        $listener = new Horde_Notification_Listener_Status();
        $messages = [];
        $this->expectOutputString('');
        $listener->notify($messages);
    }

    public function testMethodNotifyHasOutputEventMessagesEmbeddedInUlElement(): void
    {
        $listener = new Horde_Notification_Listener_Status();
        $event = new Horde_Notification_Event('test');
        $messages = [$event];
        $this->expectOutputString(
            '<ul class="notices"><li>test</li></ul>'
        );
        $listener->notify($messages);
    }
}
