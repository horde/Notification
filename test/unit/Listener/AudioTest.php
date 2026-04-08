<?php

declare(strict_types=1);

namespace Horde\Notification\Test\Listener;

use Horde_Notification_Listener_Audio;
use Horde_Notification_Event;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_Notification_Listener_Audio::class)]
class AudioTest extends TestCase
{
    public function testMethodHandleHasEventClassForAudioMessages(): void
    {
        $listener = new Horde_Notification_Listener_Audio();
        $this->assertEquals('Horde_Notification_Event', $listener->handles('audio'));
    }

    public function testMethodGetnameHasResultStringAudio(): void
    {
        $listener = new Horde_Notification_Listener_Audio();
        $this->assertEquals('audio', $listener->getName());
    }

    public function testMethodNotifyHasOutputEventMessage(): void
    {
        $listener = new Horde_Notification_Listener_Audio();
        $event = new Horde_Notification_Event('test');
        $messages = [$event];
        $this->expectOutputString(
            '<embed src="test" width="0" height="0" autostart="true" />'
        );
        $listener->notify($messages);
    }
}
