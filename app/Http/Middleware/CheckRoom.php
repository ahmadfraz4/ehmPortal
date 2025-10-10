<?php

namespace App\Http\Middleware;

use App\Models\Room;
use App\Models\RoomUsers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoom
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // checking if other person have room 
        $current_user = Auth::user()->id;
        $other_user_id = $request->id;

        // finding all rooms of user current
        $rooms = RoomUsers::where('user_id', $current_user)->pluck('room_id');

        // finding if user comming in that room array 

        $existingRoom = RoomUsers::whereIn('room_id', $rooms)->where('user_id', $other_user_id)->select('room_id')->first();

       
        if (!$existingRoom) {
            $newRoom = Room::create([
                'type' => 'chat',
            ]);
            $room_id = $newRoom->id;

            // RoomUsers::insertMany(['room_id' => $room_id, 'user_id' => $current_user], ['room_id' => $room_id, 'user_id' => $other_user_id]);
            RoomUsers::insert([
                ['room_id' => $room_id, 'user_id' => $current_user],
                ['room_id' => $room_id, 'user_id' => $other_user_id],
            ]);

            return redirect()->route('room.chat', $room_id);
            
        }else{
            return redirect()->route('room.chat', $existingRoom['room_id']);
        }

        return $next($request);
    }
}
