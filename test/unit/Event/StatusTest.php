<?php

declare(strict_types=1);

namespace Horde\Notification\Test\Event;

use Horde_Notification_Event_Status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Horde_Notification_Event_Status::class)]
class StatusTest extends TestCase
{
    public function testMethodTostringHasResultTheTextOfTheEvent(): void
    {
        $event = new Horde_Notification_Event_Status('<b>test</b>');
        $event->charset = 'ISO-8859-1';
        $this->assertEquals('&lt;b&gt;test&lt;/b&gt;', (string) $event);
    }

    public function testMethodTostringHasUnescapedResultIfContentRawFlagIsSet(): void
    {
        $event = new Horde_Notification_Event_Status('<b>test</b>', null, ['content.raw']);
        $this->assertEquals('<b>test</b>', (string) $event);
    }
}
