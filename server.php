<?php

use Workerman\Worker;
use Workerman\Lib\Timer;
use Workerman\Channel\Channel;

// Autoload Composer dependencies
require_once __DIR__ . '/vendor/autoload.php';

// Create a new Worker instance to handle websocket connections
$ws_worker = new Worker("websocket://127.0.0.1:8080");

// Set the number of processes to handle multiple connections
$ws_worker->count = 4;

// Array to keep track of clients
$clients = [];

// Handle new connections
$ws_worker->onMessage = function($connection, $data) use (&$clients) {
    $data = json_decode($data, true);

    if (isset($data['action']) && $data['action'] === 'join') {
        // Store client information
        $clients[$data['username']] = $connection;
        $connection->username = $data['username'];

        // Notify all clients about the new connection
        foreach ($clients as $client) {
            if ($client !== $connection) {
                $client->send(json_encode([
                    'action' => 'notification',
                    'message' => "{$data['username']} has joined the chat."
                ]));
            }
        }
    } elseif (isset($data['action']) && $data['action'] === 'message') {
        // Broadcast the message to the intended recipient
        if (isset($clients[$data['recipient']])) {
            $clients[$data['recipient']]->send(json_encode([
                'action' => 'message',
                'sender' => $connection->username,
                'message' => $data['message']
            ]));
        }
    }
};

// Handle connection closing
$ws_worker->onClose = function($connection) use (&$clients) {
    $username = $connection->username ?? null;

    if ($username) {
        unset($clients[$username]);

        // Notify all clients about the disconnection
        foreach ($clients as $client) {
            $client->send(json_encode([
                'action' => 'notification',
                'message' => "{$username} has left the chat."
            ]));
        }
    }
};

// Run the worker
Worker::runAll();
