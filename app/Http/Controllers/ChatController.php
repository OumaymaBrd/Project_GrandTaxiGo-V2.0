<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Models\Ride;
use App\Models\Message;
use App\Events\NewMessageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Show the chat page for a specific ride
     */
    public function showRideChat($rideId)
    {
        // Find the ride
        $ride = Ride::findOrFail($rideId);

        // Check if user has access to this ride
        if (Auth::id() !== $ride->passenger_id && Auth::id() !== $ride->driver_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à accéder à cette conversation.');
        }

        // Determine if the user is a passenger or driver
        $userType = Auth::id() === $ride->passenger_id ? 'passenger' : 'driver';

        // Get the other user (recipient)
        $recipient = $userType === 'passenger' ? $ride->driver : $ride->passenger;

        // Get previous messages
        $messages = Message::where('ride_id', $rideId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.ride', compact('ride', 'userType', 'recipient', 'messages'));
    }
}

