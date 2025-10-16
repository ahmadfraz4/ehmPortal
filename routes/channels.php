<?php

use App\Models\RoomUsers;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{room_id}', function ($user, $room_id) {
    
    // $room_user = RoomUsers::where(['room_id' => $room_id, 'user_id' => $user->id])->get();
    // return $room_user->user_id == $user->id;

    return RoomUsers::where('room_id', $room_id)
    ->where('user_id', $user->id)
    ->exists();

    
    
    // return true; // allow all users to listen for now

});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
