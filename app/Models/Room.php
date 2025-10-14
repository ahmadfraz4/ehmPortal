<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'room';
    protected $fillable = [
        'type', 'group_name'
    ];

    public function chats(){
        return $this->hasMany(Chat::class);
    }

    
}
