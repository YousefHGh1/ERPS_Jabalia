<?php

namespace App\Events;

use App\Models\Request as RequestModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;

    public function __construct(RequestModel $request)
    {
        $this->request = $request;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('requests');
    }
}
