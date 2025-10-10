<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomUsers extends Model
{
    public function chats(){
        $this->hasMany(Chat::class, 'room_id');
    }
    
}
