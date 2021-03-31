<?php
/**
 * Test the basic event class.
 *
 * @category Horde
 * @package  Notification
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Notification;
use \Notification;
use Horde_Test_Case;
use \Horde_Notification_Event;

/**
 * Test the basic event class.
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
class EventTest extends Horde_Test_Case
{
    public function testMethodConstructHasPostconditionThatTheGivenMessageWasSavedIfItWasNotNull()
    {
        $event = new Horde_Notification_Event('test');
        $this->assertEquals('test', $event->message);
    }

    public function testMethodGetmessageHasResultStringTheStoredMessage()
    {
        $event = new Horde_Notification_Event('');
        $event->message = 'test';
        $this->assertEquals('test', $event->message);
    }

    public function testMethodGetmessageHasResultStringEmptyIfNoMessageWasStored()
    {
        $event = new Horde_Notification_Event('');
        $this->assertEquals('', $event->message);
    }
}
