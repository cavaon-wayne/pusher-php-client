<?php

namespace Cavaon\Pusher\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Event implements ShouldBroadcastNow
{
    use SerializesModels;

    private $channel;
    private $event;
    private $data;

    /**
     * Event constructor.
     * @param string|array $channel
     * @param string $event
     * @param array|mixed $data
     */
    public function __construct($channel, $event, $data = [])
    {
        $this->prepareChannel($channel);
        $this->event = $event;
        $this->data = $data;
    }

    protected function prepareChannel($channel)
    {
        if (is_string($channel)) {
            $channel = [$channel];
        }
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return $this->channel;
    }

    public function broadcastAs()
    {
        return $this->event;
    }

    public function broadcastWith()
    {
        return $this->data;
    }

}