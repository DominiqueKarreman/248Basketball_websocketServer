<?php

namespace App\Websockets\SocketHandler;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Repositories\PostRepository;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class UpdatePostSocketHandler extends BaseSocketHandler implements MessageComponentInterface
{


    function onMessage(ConnectionInterface $from, MessageInterface $msg)
    {

        $payload = json_decode($msg->getPayload());
        // dump($msg->getPayload(), $payload);

        // Find the post by ID

        $message = json_decode($msg->getPayload(), true)['message'];
        // Convert the updated post to a JSON string
        $response = json_encode([
            'event' => 'asdasd',
            'data' => $message,
        ]);

        // Send the response to all connected clients
        foreach ($this->clients as $client) {
            // dump($client->user);
            dump(json_decode($msg->getPayload(), true));
            if ($client->user->id == json_decode($msg->getPayload(), true)['from'] || json_decode($msg->getPayload(), true)['to'] == $client->user->id) {
                $client->send($response);
            }
            // $client->send($response);
        }

        // $from->send($response);


    }

}