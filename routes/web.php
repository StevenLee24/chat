<?php

use Illuminate\Support\Facades\Route;

Route::get('/chat-room', function(){
    return view('chatroom');
});

Route::get('/chat-room2', function(){
    return view('chatroom2');
});
