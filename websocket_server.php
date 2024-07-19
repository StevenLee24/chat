<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use Channel\Client;

$ws_worker = new Worker("websocket://127.0.0.1:8005");

$ws_worker->onWorkerStart = function ($worker) {
    Client::connect('127.0.0.1', 8006);

    Client::on('broadcast', function ($event_data) use ($worker) {

        $worker->connections[0]->send($worker->connections[0]->id);
        // foreach ($worker->connections as $connection) {

        //     $connection->send($connection->id);
        // }
    });
};

$ws_worker->onConnect = function($connection) {
    // 設置每個連接的唯一ID（可以是任意唯一值）
    $connection->id = uniqid();
};


// $ws_worker->onMessage 是一個事件處理器，它處理WebSocket連接接收到的消息事件。
$ws_worker->onMessage = function ($connection, $data) use ($ws_worker) {
    // foreach ($ws_worker->connections as $conn) { $conn->send("Hello, connection {$conn->id}");}
    $a = $data;
    Channel\Client::publish('broadcast', $data);
    // Channel\Client 是一個用於實現跨進程或跨服務器消息傳遞的客戶端對象。
    // 這行代碼使用Channel\Client::publish方法將接收到的消息數據$data發布到名為broadcast的頻道上。
};

$ws_worker->onClose = function ($connection) {
    echo "連線已關閉\n";
};

Worker::runAll();
