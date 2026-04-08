<?php

declare(strict_types=1);

class Horde_Notification_Listener_Dummy extends Horde_Notification_Listener
{
    public $events;
    public $params;

    public function __construct($params)
    {
        $this->params = $params;
        $this->_name = 'dummy';
        $this->_handles = [
            'dummy' => 'Horde_Notification_Event',
            'status' => 'Horde_Notification_Event',
        ];
    }

    public function notify($events, $options = []): void
    {
        $this->events = $events;
    }
}
