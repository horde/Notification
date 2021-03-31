<?php

namespace Horde\Notification;
use Horde_Notification_Listener;

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
