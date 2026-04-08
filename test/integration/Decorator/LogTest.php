<?php

declare(strict_types=1);

namespace Horde\Notification\Test\Integration\Decorator;

use Exception;
use Horde_Notification_Event;
use Horde_Notification_Handler_Decorator_Log;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
class LogTest extends TestCase
{
    private $logger;
    private Horde_Notification_Handler_Decorator_Log $log;

    public function setUp(): void
    {
        if (!class_exists('Horde_Log_Logger')) {
            $this->markTestSkipped('The Horde_Log package is not installed.');
        }

        $this->logger = $this->createMock(\Horde_Log_Logger::class);
        $this->log = new Horde_Notification_Handler_Decorator_Log(
            $this->logger
        );
    }

    public function testMethodPushHasPostconditionThattheEventGotLoggedIfTheEventWasAnError(): void
    {
        $exception = new Horde_Notification_Event(new Exception('test'));
        $this->logger->expects($this->once())
            ->method('__call')
            ->with('debug', $this->isType('array'));
        $this->log->push($exception, []);
    }
}
