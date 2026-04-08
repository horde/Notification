<?php

declare(strict_types=1);

namespace Horde\Notification\Test;

use Horde_Notification_Event;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_Notification_Event::class)]
class EventTest extends TestCase
{
    public function testMethodConstructHasPostconditionThatTheGivenMessageWasSavedIfItWasNotNull(): void
    {
        $event = new Horde_Notification_Event('test');
        $this->assertEquals('test', $event->message);
    }

    public function testMethodGetmessageHasResultStringTheStoredMessage(): void
    {
        $event = new Horde_Notification_Event('');
        $event->message = 'test';
        $this->assertEquals('test', $event->message);
    }

    public function testMethodGetmessageHasResultStringEmptyIfNoMessageWasStored(): void
    {
        $event = new Horde_Notification_Event('');
        $this->assertEquals('', $event->message);
    }
}
