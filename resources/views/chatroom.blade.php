<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style type="text/css">
        #send{
            width:200px;
            line-height:50px;
            padding:20px;
            border:2px blue solid;
            margin-right:10px;
            float:left;
            display:inline;
        }
        #receive{
            width:200px;
            line-height:50px;
            padding:20px;
            border:2px green solid;
            float:left;
            display:inline;
        }
    </style>
</head>
<body>
    <div>
        <input type="text" id="message" placeholder="請輸入訊息" autofocus>
        <input type="submit" value="發出訊息" onclick="send_messasge(); ">
    </div>
    <hr>
    <div id="send" class="send">
        您發出的訊息：<hr>        
    </div>
    <div id="receive" class="receive">
        接收到的訊息：<hr>
    </div>
    <script>
        var wsServer = 'ws://127.0.0.1:8005';
        var websocket = new WebSocket(wsServer);
        websocket.onopen = function (event) {
            append_element('receive', '成功連接到 WebSocket 服務');
        };        
        websocket.onclose = function (event) {
            append_element('receive', '關閉連接 WebSocket 服務');
        };        
        websocket.onmessage = function (event) {
            append_element('receive', event.data);
        };        
        websocket.onerror = function (event, error) {
            append_element('receive', event.data);
        };
        const send_messasge = function (){
            var message = document.getElementById("message").value;
            document.getElementById("message").value="";
            var msg = message;
            append_element('send', msg);
            websocket.send(msg);
        };
        const append_element = function (ele_id, data){
            var parent = document.getElementById(ele_id);
            var p = document.createElement("p");
            p.innerText = "> " + data;
            parent.appendChild(p);
        };
    </script>
</body>
</html>