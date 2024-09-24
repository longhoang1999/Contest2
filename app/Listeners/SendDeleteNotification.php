<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\DeleteNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SendDeleteNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(object $event)
    {
        if($event->userSend != '' && $event->userReceiver != ''){
            $data = [
                'message'   => $event->message,
                'userSend'  => $event->userSend,
                'userReceiver'  => $event->userReceiver,
                'url'   => $event->url,
                'create_at' => Carbon::now(),
                'type'  => $event->type
            ];
            DB::table('notification')->insert($data);
        }
    }
}