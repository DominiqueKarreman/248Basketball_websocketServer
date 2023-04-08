<?php

namespace App\Websockets\SocketHandler;

use Exception;

use App\Models\ChatMessage;
use Ratchet\ConnectionInterface;
use App\Http\Resources\PostResource;
use App\Repositories\PostRepository;
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
        if (json_decode($msg->getPayload(), true)['typing'] == true) {
            
            foreach ($this->clients as $client) {
                // dump($client->user);
                dump(json_decode($msg->getPayload(), true));

                if ($client->user->id == json_decode($msg->getPayload(), true)['from'] || json_decode($msg->getPayload(), true)['to'] == $client->user->id) {
                    $client->send(json_encode(['typing' => true]));
                }
                // $client->send($response);
            }
            return;
        }
        if (json_decode($msg->getPayload(), true)['typing'] == false) {
            
            foreach ($this->clients as $client) {
                // dump($client->user);
                dump(json_decode($msg->getPayload(), true));

                if ($client->user->id == json_decode($msg->getPayload(), true)['from'] || json_decode($msg->getPayload(), true)['to'] == $client->user->id) {
                    $client->send(json_encode(['typing' => false]));
                }
                // $client->send($response);
            }
            return;
        }
        // Convert the updated post to a JSON string
        $chatMessage = new ChatMessage();
        $chatMessage->message = json_decode($msg->getPayload(), true)['message'];
        $chatMessage->from_user = json_decode($msg->getPayload(), true)['from'];
        $chatMessage->to_user = json_decode($msg->getPayload(), true)['to'];
        $chatMessage->is_read = 0;
        $chatMessage->sent_at = now();
        $chatMessage->save();

        $response = json_encode($chatMessage);

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