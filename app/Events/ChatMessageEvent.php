<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(){
        return view('chat.index');
    }

    public function store(Request $request){
        // Validate the request
        $validated = $request->validate([
            'nickname' => 'required|string|max:50',
            'message' => 'required|string|max:500',
        ]);

        // Broadcast the chat message event
        event(new ChatMessageEvent($request->nickname, $request->message));

        return response()->json([
            'success' => true,
            'message' => 'Message envoyé avec succès',
        ]);
    }
}

