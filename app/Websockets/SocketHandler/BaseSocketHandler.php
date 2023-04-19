<?php

namespace App\Websockets\SocketHandler;

use App\Models\User;
use Ratchet\ConnectionInterface;
use Tymon\JWTAuth\Facades\JWTAuth;
use BeyondCode\LaravelWebSockets\Apps\App;
use Ratchet\WebSocket\MessageComponentInterface;
use BeyondCode\LaravelWebSockets\QueryParameters;
use BeyondCode\LaravelWebSockets\WebSockets\Exceptions\UnknownAppKey;
use Ratchet\WebSocket\WebSocketDecorator;

abstract class BaseSocketHandler implements MessageComponentInterface
{
    protected $clients;
    protected $user;
    protected $connectionWithUser;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        
    }
    protected function verifyAppKey(ConnectionInterface $connection)
    {
        $appKey = QueryParameters::create($connection->httpRequest)->get('appKey');

        if (!$app = App::findByKey($appKey)) {
            throw new UnknownAppKey($appKey);
        }

        $connection->app = $app;

        return $this;
    }

    protected function generateSocketId(ConnectionInterface $connection)
    {
        $socketId = sprintf('%d.%d', random_int(1, 1000000000), random_int(1, 1000000000));

        $connection->socketId = $socketId;
        $this->connectionWithUser = $connection;

        return $this;
    }

    function onOpen(ConnectionInterface $conn)
    {
        $headers = $conn->httpRequest->getUri();
        // dump($headers);
        $this->verifyAppKey($conn)->generateSocketId($conn);
        $AuthIdentifier = QueryParameters::create($conn->httpRequest)->get('AuthIdentifier');
        $AuthKey = QueryParameters::create($conn->httpRequest)->get('AuthKey');
        $users  = QueryParameters::create($conn->httpRequest)->get('users');
        
        $decodedIdentifier = base64_decode($AuthIdentifier);
        $usersArray = explode('_', $users);
        // dump($AuthIdentifier, $decodedIdentifier, $usersArray);
        $user = User::where('geboorte_datum', $decodedIdentifier)->first();
        
        if (!$user) {
            dump('user not found');
            $conn->close(); // close the connection
            return; // exit the function
        }
       
        dump($AuthKey, $usersArray, !in_array($AuthKey, $usersArray));
        if (!in_array($AuthKey, $usersArray)) {
            $conn->close(); // close the connection
            return; // exit the function
        }
        

        // dump($user, );
        $user->online = "Online";
        $user->save();

        $this->user = $user;
        dump($user->online);
        $conn->user = $user;
        $this->clients->attach($conn);
    }

    function onClose(ConnectionInterface $conn)
    {
        dump('closed');
        $this->user->online = "Last online: " . date('d-m-Y H:i:s');
        $this->user->save();    
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        dump($e);
        dump('onerror');
    }
}