<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(){
        $data = User::whereNot('id', Auth::user()->id)->paginate(10);
        $user_rooms = RoomUsers::where('user_id', Auth::user()->id)->pluck('room_id');
        $groups = Room::whereIn('id', $user_rooms)->where('type', 'group')->get();
        return view('users', ['data' => $data, 'groups' => $groups]);
    }
}
