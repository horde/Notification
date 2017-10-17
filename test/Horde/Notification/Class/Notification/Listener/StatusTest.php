<?php
/**
 * Test the status listener class.
 *
 * @category Horde
 * @package  Notification
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */

/**
 * Test the status listener class.
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
class Horde_Notification_Class_Notification_Listener_StatusTest extends Horde_Test_Case
{
    public function testMethodHandleHasEventClassForHordeMessages()
    {
        $listener = new Horde_Notification_Listener_Status();
        $this->assertEquals('Horde_Notification_Event_Status', $listener->handles('status'));
    }

    public function testMethodGetnameHasResultStringStatus()
    {
        $listener = new Horde_Notification_Listener_Status();
        $this->assertEquals('status', $listener->getName());
    }

    public function testMethodNotifyHasNoOutputIfTheMessageStackIsEmpty()
    {
        $listener = new Horde_Notification_Listener_Status();
        $messages = array();
        $listener->notify($messages);
    }

    public function testMethodNotifyHasOutputEventMessagesEmbeddedInUlElement()
    {
        $listener = new Horde_Notification_Listener_Status();
        $event = new Horde_Notification_Event('test');
        $messages = array($event);
        $this->expectOutputString(
            '<ul class="notices"><li>test</li></ul>'
        );
        $listener->notify($messages);
    }

}
