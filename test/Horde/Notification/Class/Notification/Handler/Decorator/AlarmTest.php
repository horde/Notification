<?php
/**
 * Test the alarm notification handler class.
 *
 * @category Horde
 * @package  Notification
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Notification;
use Horde_Test_Case;

/**
 * Test the alarm notification handler class.
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

class AlarmTest extends Horde_Test_Case
{
    public function setUp(): void
    {
        $this->markTestIncomplete('Currently broken');
        if (!class_exists('Horde_Alarm')) {
            $this->markTestSkipped('The Horde_Alarm package is not installed!');
        }

        $this->alarm = $this->getMockForAbstractClass('Horde_Alarm');
        $this->alarm_handler = new Horde_Notification_Handler_Decorator_Alarm(
            $this->alarm, null
        );
    }

    public function testMethodNotifyHasPostconditionThatTheAlarmSystemGotNotifiedIfTheStatusListenerShouldBeNotified()
    {
        $this->alarm->expects($this->once())
            ->method('notify')
            ->with('');
        $this->alarm_handler->notify(array('listeners' => array('status')));
    }

}
