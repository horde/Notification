<?php

declare(strict_types=1);

namespace Horde\Notification\Test\Integration\Decorator;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Horde_Alarm;
use Horde_Notification_Handler_Decorator_Alarm;

#[CoversNothing]
class AlarmTest extends TestCase
{
    private $alarm;
    private $alarm_handler;

    public function setUp(): void
    {
        $this->markTestIncomplete('Currently broken');
        if (!class_exists('Horde_Alarm')) {
            $this->markTestSkipped('The Horde_Alarm package is not installed.');
        }

        $this->alarm = $this->getMockForAbstractClass(Horde_Alarm::class);
        $this->alarm_handler = new Horde_Notification_Handler_Decorator_Alarm(
            $this->alarm,
            null
        );
    }

    public function testMethodNotifyHasPostconditionThatTheAlarmSystemGotNotifiedIfTheStatusListenerShouldBeNotified(): void
    {
        $this->alarm->expects($this->once())
            ->method('notify')
            ->with('');
        $this->alarm_handler->notify(['listeners' => ['status']]);
    }
}
