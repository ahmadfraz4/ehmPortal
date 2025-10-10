<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Room;
use App\Models\RoomUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
  
    public function openChat(Request $req){
        $current_user = Auth::user()->id;
        $other_user_id = $req->id;

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

            // return redirect()->route('room.chat', $room_id);
            return response()->json(['success' => true, 'room_id' => $room_id, 'message' => ['chat' => '']]);

        }else{
            $chats = Chat::where('room_id', $existingRoom['room_id'])->with(['senderUser', 'receiverUser'])->get();
            // return redirect()->route('room.chat', $existingRoom['room_id']);
            return response()->json(['success' => true, 'room_id' => $existingRoom['room_id'], 'message' => ['chat' => $chats]]);
        }

    }
    public function sendChat(Request $req){
          $req->validate([
                'room_id' => 'required|exists:room,id',
                'message' => 'required|string|max:5000',
            ]);

            // ✅ 2. Get the authenticated user
            $senderId = Auth::id();

            // ✅ 3. Create the chat message
            $chat = Chat::create([
                'room_id'  => $req->room_id,
                'sender'   => $senderId,
                'receiver' => $req->receiver_id,
                'message'  => $req->message,
            ]);

            // ✅ 4. Broadcast the message (optional if you’re using WebSockets)
            event(new MessageSent($chat));

            // ✅ 5. Return response to AJAX
            return response()->json([
                'status'  => 'success',
                'message' => 'Message sent successfully',
                'data'    => $chat,
            ]);
    }
    public function chat($id){
        echo $id;
        return view('chats');
    }
}
