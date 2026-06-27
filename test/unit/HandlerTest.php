<?php

declare(strict_types=1);

namespace Horde\Notification\Test;

use Exception;
use Horde_Notification_Event;
use Horde_Notification_Handler;
use Horde_Notification_Handler_Decorator_Base;
use Horde_Notification_Listener;
use Horde_Notification_Storage_Interface;
use Horde_Notification_Storage_Object;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Horde_Exception;
use Horde_Notification_Listener_Audio;

#[CoversClass(Horde_Notification_Handler::class)]
class HandlerTest extends TestCase
{
    private Horde_Notification_Storage_Object $storage;
    private Horde_Notification_Handler $handler;

    public function setUp(): void
    {
        $this->storage = new Horde_Notification_Storage_Object();
        $this->handler = new Horde_Notification_Handler($this->storage);
    }

    public function testMethodAttachHasResultNotificationlistener(): void
    {
        $this->assertInstanceOf(
            Horde_Notification_Listener::class,
            $this->handler->attach('audio')
        );
    }

    public function testMethodAttachHasResultNotificationlistenerTheSameListenerAsBeforeIfThisListenerHasAlreadyBeenAttached(): void
    {
        $listener = $this->handler->attach('audio');
        $this->assertSame($listener, $this->handler->attach('audio'));
    }

    public function testMethodAttachHasResultNotificationlistenerClassAsSpecifiedInParameterClass(): void
    {
        $this->assertInstanceOf(
            Horde_Notification_Listener_Audio::class,
            $this->handler->attach(
                'MyAudio',
                [],
                Horde_Notification_Listener_Audio::class
            )
        );
    }

    public function testMethodAttachHasPostconditionThatTheListenerGotInitializedWithTheProvidedParmeters(): void
    {
        $listener = $this->handler->attach('dummy', ['test']);
        $this->assertEquals(['test'], $listener->params);
    }

    public function testMethodAttachHasPostconditionThatTheListenerStackGotInitializedAsArray(): void
    {
        $this->handler->attach('audio');
        $this->assertEquals([], $this->storage->notifications['audio']);
    }

    public function testMethodAttachThrowsExceptionIfTheListenerTypeIsUnknown(): void
    {
        $this->expectException(Horde_Exception::class);
        $this->handler->attach('MyAudio');
        $this->fail('No exception!');
    }

    public function testMethodDetachHasPostconditionThatTheListenerStackGotUnset(): void
    {
        $this->handler->attach('audio');
        $this->handler->detach('audio');
        $this->assertFalse(isset($this->storage->notifications['audio']));
    }

    public function testMethodDetachThrowsExceptionIfTheListenerIsUnset(): void
    {
        $this->expectException(Horde_Exception::class);
        $this->handler->detach('MyAudio');
        $this->fail('No exception!');
    }

    public function testMethodClearHasPostconditionThatTheStorageOfTheSpecifiedListenerWasCleared(): void
    {
        $storage = $this->createMock(Horde_Notification_Storage_Interface::class);
        $storage->method('exists')
            ->willReturn(false);
        $storage->expects($this->once())
            ->method('clear')
            ->with('dummy');
        $handler = new Horde_Notification_Handler($storage);

        $handler->attach('dummy');
        $handler->clear('dummy');
    }

    public function testMethodClearHasPostconditionThatAllUnattachedEventsHaveBeenClearedFromStorageIfNoListenerWasSpecified(): void
    {
        $storage = $this->createMock(Horde_Notification_Storage_Interface::class);
        $storage->expects($this->once())
            ->method('clear')
            ->with('_unattached');
        $handler = new Horde_Notification_Handler($storage);
        $handler->clear();
    }

    public function testMethodGetHasResultNullIfTheSpecifiedListenerIsNotAttached(): void
    {
        $this->assertNull($this->handler->get('not attached'));
    }

    public function testMethodAddtypeHasPostconditionThatTheSpecifiedListenerHandlesTheGivenMessageType(): void
    {
        $this->handler->attach('dummy');
        $this->handler->addType('dummy', 'newtype', 'NewType');
        $this->assertEquals('NewType', $this->handler->getListener('dummy')->handles('newtype'));
    }

    public function testMethodAdddecoratorHasPostconditionThatTheGivenDecoratorWasAddedToTheHandlerAndReceivesPushCalls(): void
    {
        $decorator = $this->createMock(Horde_Notification_Handler_Decorator_Base::class);
        $decorator->expects($this->once())
            ->method('push')
            ->with($this->isInstanceOf(Horde_Notification_Event::class));
        $event = new Horde_Notification_Event('test');
        $this->handler->attach('audio');
        $this->handler->addDecorator($decorator);
        $this->handler->push($event, 'audio');
    }

    public function testMethodAdddecoratorHasPostconditionThatTheGivenDecoratorWasAddedToTheHandlerAndReceivesNotifyCalls(): void
    {
        $decorator = $this->createMock(Horde_Notification_Handler_Decorator_Base::class);
        $decorator->expects($this->once())
            ->method('notify');
        $this->handler->attach('audio');
        $this->handler->addDecorator($decorator);
        $this->handler->notify();
    }

    public function testMethodPushHasPostconditionThatTheEventGotSavedInAllAttachedListenerStacksHandlingTheEvent(): void
    {
        $this->handler->attach('audio');
        $this->handler->push('test', 'audio', [], ['immediate' => true]);
        $result = array_shift($this->storage->notifications['audio']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(Horde_Notification_Event::class, $result);
        $this->assertEquals([], $result->flags);
        $this->assertEquals('audio', $result->type);
    }

    public function testMethodPushHasPostconditionThatAnExceptionGetsMarkedAsTypeStatusIfTheTypeWasUnset(): void
    {
        $this->handler->attach('dummy');
        $this->handler->push(new Exception('test'), null, [], ['immediate' => true]);
        $result = array_shift($this->storage->notifications['dummy']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(Horde_Notification_Event::class, $result);
        $this->assertEquals([], $result->flags);
        $this->assertEquals('status', $result->type);
    }

    public function testMethodPushHasPostconditionThatEventsWithoutTypeGetMarkedAsTypeStatus(): void
    {
        $this->handler->attach('dummy');
        $this->handler->push('test', null, [], ['immediate' => true]);
        $result = array_shift($this->storage->notifications['dummy']);
        $this->assertNotNull($result);
        $this->assertInstanceOf(Horde_Notification_Event::class, $result);
        $this->assertEquals([], $result->flags);
        $this->assertEquals('status', $result->type);
    }

    public function testMethodNotifyHasPostconditionThatAllListenersWereNotified(): void
    {
        $dummy = $this->handler->attach('dummy');
        $this->handler->push('test', 'dummy');
        $this->handler->notify();
        $result = array_shift($dummy->events);
        $this->assertNotNull($result);
        $this->assertInstanceOf(Horde_Notification_Event::class, $result);
        $this->assertEquals([], $result->flags);
        $this->assertEquals('dummy', $result->type);
    }

    public function testMethodNotifyHasPostconditionThatTheSpecifiedListenersWereNotified(): void
    {
        $dummy = $this->handler->attach('dummy');
        $this->handler->push('test', 'dummy');
        $this->handler->notify(['listeners' => 'dummy']);
        $result = array_shift($dummy->events);
        $this->assertNotNull($result);
        $this->assertInstanceOf(Horde_Notification_Event::class, $result);
        $this->assertEquals([], $result->flags);
        $this->assertEquals('dummy', $result->type);
    }

    public function testMethodCountHasResultTheTotalNumberOfEventsInTheStack(): void
    {
        $this->handler->attach('audio');
        $this->handler->attach('dummy');
        $this->handler->push('test', 'audio');
        $this->handler->push('test', 'dummy');
        $this->assertEquals(2, $this->handler->count());
    }

    public function testMethodCountHasResultTheEventNumberForASpecificListenerIfTheListenerHasBeenSpecified(): void
    {
        $this->handler->attach('audio');
        $this->handler->attach('dummy');
        $this->handler->push('test', 'audio');
        $this->assertEquals(1, $this->handler->count('audio'));
    }
}
