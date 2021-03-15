<?php
/**
 * Test the basic notification handler class.
 *
 * @category Horde
 * @package  Notification
 * @author   Gunnar Wrobel <wrobel@pardus.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 */
namespace Horde\Notification;
use \Notification;
use \Horde_Notification_Listener;
use Horde_Test_Case;
use \Horde_Notification_Storage_Session;
use \Horde_Notification_Handler;
use \Horde_Notification_Event;

/**
 * Test the basic notification handler class.
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

class HandlerTest extends Horde_Test_Case
{
    public function setUp(): void
    {
        $this->storage = new Horde_Notification_Storage_Session('test');
        $this->handler = new Horde_Notification_Handler($this->storage);
    }

    public function tearDown(): void
    {
        unset($_SESSION);
    }

    public function testMethodAttachHasResultNotificationlistener()
    {
        $this->assertInstanceOf(
            'Horde_Notification_Listener_Audio',
            $this->handler->attach('audio')
        );
    }

    public function testMethodAttachHasResultNotificationlistenerTheSameListenerAsBeforeIfThisListenerHasAlreadyBeenAttached()
    {
        $listener = $this->handler->attach('audio');
        $this->assertSame($listener, $this->handler->attach('audio'));
    }

    public function testMethodAttachHasResultNotificationlistenerClassAsSpecifiedInParameterClass()
    {
        $this->assertInstanceOf(
            'Horde_Notification_Listener_Audio',
            $this->handler->attach(
                'MyAudio', array(), 'Horde_Notification_Listener_Audio'
            )
        );
    }

    public function testMethodAttachHasPostconditionThatTheListenerGotInitializedWithTheProvidedParmeters()
    {
        $this->expectException('Horde_Exception');
        $listener = $this->handler->attach('dummy', array('test'));
        $this->assertEquals(array('test'), $listener->params);
    }

    public function testMethodAttachHasPostconditionThatTheListenerStackGotInitializedAsArray()
    {
        $this->handler->attach('audio');
        $this->assertEquals(array(), $_SESSION['test']['audio']);
    }

    public function testMethodAttachThrowsExceptionIfTheListenerTypeIsUnknown()
    {
        $this->expectException('Horde_Exception');
        $this->handler->attach('MyAudio');
        $this->fail('No exception!');
    }

    public function testMethodDetachHasPostconditionThatTheListenerStackGotUnset()
    {
        $this->handler->attach('audio');
        $this->handler->detach('audio');
        $this->assertFalse(isset($_SESSION['test']['audio']));
    }

    public function testMethodDetachThrowsExceptionIfTheListenerIsUnset()
    {
        $this->expectException('Horde_Exception');
        $this->handler->detach('MyAudio');
        $this->fail('No exception!');
    }

    public function testMethodClearHasPostconditionThatTheStorageOfTheSpecifiedListenerWasCleared()
    {
        $this->expectException('Horde_Exception');
        $storage = $this->getMockBuilder('Horde_Notification_Storage_Interface')->getMock();
        $storage->expects($this->once())
            ->method('clear')
            ->with('dummy');
        $handler = new Horde_Notification_Handler($storage);
        $handler->attach('dummy');
        $handler->clear('dummy');
    }

    public function testMethodClearHasPostconditionThatAllUnattachedEventsHaveBeenClearedFromStorageIfNoListenerWasSpecified()
    {
        $storage = $this->getMockBuilder('Horde_Notification_Storage_Interface')->getMock();
        $storage->expects($this->once())
            ->method('clear')
            ->with('_unattached');
        $handler = new Horde_Notification_Handler($storage);
        $handler->clear();
    }

    public function testMethodGetHasResultNullIfTheSpecifiedListenerIsNotAttached()
    {
        $this->assertNull($this->handler->get('not attached'));
    }

    public function testMethodAddtypeHasPostconditionThatTheSpecifiedListenerHandlesTheGivenMessageType()
    {
        $this->expectException('Horde_Exception');
        $this->handler->attach('dummy');
        $this->handler->addType('dummy', 'newtype', 'NewType');
        $this->assertEquals('NewType', $this->handler->getListener('dummy')->handles('newtype'));
    }

    public function testMethodAdddecoratorHasPostconditionThatTheGivenDecoratorWasAddedToTheHandlerAndReceivesPushCalls()
    {
        $decorator = $this->getMockBuilder('Horde_Notification_Handler_Decorator_Base')->getMock();
        $decorator->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf('Horde_Notification_Event'));
        $event = new Horde_Notification_Event('test');
        $this->handler->attach('audio');
        $this->handler->addDecorator($decorator);
        $this->handler->push($event, 'audio');
    }

    public function testMethodAdddecoratorHasPostconditionThatTheGivenDecoratorWasAddedToTheHandlerAndReceivesNotifyCalls()
    {
        $decorator = $this->getMockBuilder('Horde_Notification_Handler_Decorator_Base')->getMock();
        $decorator->expects($this->once())
            ->method('notify');
        $this->handler->attach('audio');
        $this->handler->addDecorator($decorator);
        $this->handler->notify();
    }

    public function testMethodPushHasPostconditionThatTheEventGotSavedInAllAttachedListenerStacksHandlingTheEvent()
    {
        $event = new Horde_Notification_Event('test');
        $this->handler->attach('audio');
        $this->handler->push('test', 'audio', array(), array('immediate' => true));
        $result = array_shift($_SESSION['test']['audio']);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Horde_Notification_Event', $result);
        $this->assertEquals(array(), $result->flags);
        $this->assertEquals('audio', $result->type);
    }

    public function testMethodPushHasPostconditionThatAnExceptionGetsMarkedAsTypeStatusIfTheTypeWasUnset()
    {
        $this->expectException('Horde_Exception');
        $this->handler->attach('dummy');
        $this->handler->push(new Exception('test'), null, array(), array('immediate' => true));
        $result = array_shift($_SESSION['test']['dummy']);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Horde_Notification_Event', $result);
        $this->assertEquals(array(), $result->flags);
        $this->assertEquals('status', $result->type);
    }

    public function testMethodPushHasPostconditionThatEventsWithoutTypeGetMarkedAsTypeStatus()
    {
        $this->expectException('Horde_Exception');
        $this->handler->attach('dummy');
        $this->handler->push('test', null, array(), array('immediate' => true));
        $result = array_shift($_SESSION['test']['dummy']);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Horde_Notification_Event', $result);
        $this->assertEquals(array(), $result->flags);
        $this->assertEquals('status', $result->type);
    }

    public function testMethodNotifyHasPostconditionThatAllListenersWereNotified()
    {
        $this->expectException('Horde_Exception');
        $dummy = $this->handler->attach('dummy');
        $this->handler->push('test', 'dummy');
        $this->handler->notify();
        $result = array_shift($dummy->events);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Horde_Notification_Event', $result);
        $this->assertEquals(array(), $result->flags);
        $this->assertEquals('dummy', $result->type);
    }

    public function testMethodNotifyHasPostconditionThatTheSpecifiedListenersWereNotified()
    {
        $this->expectException('Horde_Exception');
        $dummy = $this->handler->attach('dummy');
        $this->handler->push('test', 'dummy');
        $this->handler->notify(array('listeners' => 'dummy'));
        $result = array_shift($dummy->events);
        $this->assertNotNull($result);
        $this->assertInstanceOf('Horde_Notification_Event', $result);
        $this->assertEquals(array(), $result->flags);
        $this->assertEquals('dummy', $result->type);
    }

    public function testMethodCountHasResultTheTotalNumberOfEventsInTheStack()
    {
        $this->expectException('Horde_Exception');
        $this->handler->attach('audio');
        $this->handler->attach('dummy');
        $this->handler->push('test', 'audio');
        $this->handler->push('test', 'dummy');
        $this->assertEquals(2, $this->handler->count());
    }

    public function testMethodCountHasResultTheEventNumberForASpecificListenerIfTheListenerHasBeenSpecified()
    {
        $this->expectException('Horde_Exception');
        $this->handler->attach('audio');
        $this->handler->attach('dummy');
        $this->handler->push('test', 'audio');
        $this->assertEquals(1, $this->handler->count('audio'));
    }

}

class Horde_Notification_Listener_Dummy extends Horde_Notification_Listener
{
    public $events;
    public $params;

    public function __construct($params)
    {
        $this->params = $params;
        $this->_name = 'dummy';
        $this->_handles = array(
            'dummy' => 'Horde_Notification_Event',
            'status' => 'Horde_Notification_Event'
        );
    }

    public function notify($events, $options = array())
    {
        $this->events = $events;
    }

}
