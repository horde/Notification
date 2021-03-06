<?php
/**
 * Test the logging notification handler class.
 *
 * @category Horde
 * @package  Notification
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

/**
 * Test the logging notification handler class.
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

class Horde_Notification_Class_Notification_Handler_Decorator_LogTest
extends Horde_Test_Case
{
    public function setUp()
    {
        if (!class_exists('Horde_Log_Logger')) {
            $this->markTestSkipped('The Horde_Log package is not installed!');
        }

        $this->logger = $this->getMock('Horde_Log_Logger');
        $this->log = new Horde_Notification_Handler_Decorator_Log(
            $this->logger
        );
    }

    public function testMethodPushHasPostconditionThattheEventGotLoggedIfTheEventWasAnError()
    {
        $exception = new Horde_Notification_Event(new Exception('test'));
        $this->logger->expects($this->once())
            ->method('__call')
            ->with('debug', $this->isType('array'));
        $this->log->push($exception, array());
    }

}
