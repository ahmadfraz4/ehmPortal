<?php

namespace App\Http\Controllers;

use App\Events\GroupCreated;
use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Room;
use App\Models\RoomUsers;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
  
    public function openChat(Request $req){
        $current_user = Auth::user()->id;
        $other_user_id = $req->id;

        

        if($req->chat_type == 'chat'){

            // finding all rooms of user current
            $rooms = RoomUsers::where('user_id', $current_user)->pluck('room_id');

            // finding if user comming in that room array 

            $existingRoom = RoomUsers::whereIn('room_id', $rooms)->where(['user_id' => $other_user_id])->select('room_id')->first();

        
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
                $chats = Chat::where('room_id', $existingRoom['room_id'])->whereHas('room', function ($query) {
                    $query->where('type', 'chat');
                })->with(['senderUser', 'receiverUser'])->get();
                return response()->json(['success' => true, 'room_id' => $existingRoom['room_id'], 'message' => ['chat' => $chats]]);
            }
        }else{
            $room = Room::find($req->id);
            $chats = Chat::where(['room_id' => $req->id])->whereHas('room', function($query){
                $query->where('type', 'group');
            })->with(['senderUser'])->get();
            return response()->json(['success' => true, 'room_id' => $req->id, 'message' => ['chat' => $chats]]);
        }

    }
    public function sendChat(Request $req){
          $req->validate([
                'room_id' => 'required|exists:room,id',
                'message' => 'required|string|max:5000'
            ]);

            // ✅ 2. Get the authenticated user
            $senderId = Auth::id();

            Log::info('Receiver ID:', [$req->receiver_id]);


            // ✅ 3. Create the chat message
            $chat = Chat::create([
                'room_id'  => $req->room_id,
                'sender'   => $senderId,
                'receiver' => $req->receiver_id ?: null,
                'message'  => $req->message,
            ]);

            // ✅ 4. Broadcast the message (optional if you’re using WebSockets)
            // event(new MessageSent($chat));
             broadcast(new MessageSent($chat))->toOthers();

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

    public function createGroup(Request $req){
        $rules = [
            'groupname' => 'required|string|min:2|max:15',
            'users' => 'required|array|min:1', // must be an array with at least 2 users
            'users.*' => 'exists:users,id',    // optional: validate each user ID if needed
        ];


        $validate = Validator::make($req->all(), $rules);
        if ($validate->fails()) {
            return redirect()->back()
                ->withInput()
                ->withErrors($validate);
        }
        // dd($validate->fails());

        $newRoom = Room::create([
            'type' => 'group', 'group_name' => $req->groupname
        ]);
        $room_id = $newRoom->id;
        foreach($req->users as $user_id){
            RoomUsers::insert(['room_id' => $room_id, 'user_id' => $user_id]);
        }

        RoomUsers::insert(['room_id' => $room_id, 'user_id' => Auth::user()->id]);
        
        broadcast(new GroupCreated($newRoom, $req->users))->toOthers();


        return redirect()->back()->with([
            'success' => true,
            'room_id' => $room_id,
            'group' => true,
        ]);
    }

    public function leaveGroup(Request $req){
        $rules = [
            'room_id' => 'required'
        ];
        $is_validate = Validator::make($req->all(), $rules);
        if($is_validate->failed()){
            return response()->json(['success' => false, 'message' => 'Room id is required']);
        }

        if (RoomUsers::where(['room_id' => $req->room_id, 'user_id' => Auth::id()])->exists()) {
            RoomUsers::where(['room_id' => $req->room_id, 'user_id' => Auth::id()])->delete();
            $chat = Chat::create([
                'message' => Auth::user()->name . ' left the group.',
                'room_id' => $req->room_id
            ]);
            broadcast(new MessageSent($chat))->toOthers();
            return response()->json(['success' => true, 'message' => 'Group Leave Successfully']);
        }else{
            return response()->json(['success' => false, 'message' => 'Room not exist for this user']);
        }
    }
}
