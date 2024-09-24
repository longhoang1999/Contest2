<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreateNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userSend;
    public $userReceiver;
    public $url;
    public $type;


    public function __construct($message, $userSend, $userReceiver, $url, $type)
    {
        $this->message = $message;
        $this->userSend = $userSend;
        $this->userReceiver = $userReceiver;
        $this->url = $url;
        $this->type = $type;

    }

}