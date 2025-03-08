<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ride;
use App\Events\NewMessageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Get messages for a specific ride
     */
    public function getMessages($rideId)
    {
        // Check if user has access to this ride
        $ride = Ride::findOrFail($rideId);

        if (Auth::id() !== $ride->passenger_id && Auth::id() !== $ride->driver_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get messages
        $messages = Message::where('ride_id', $rideId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ride_id' => 'required|exists:rides,id',
            'recipient_id' => 'required|exists:users,id',
            'recipient_type' => 'required|in:driver,passenger',
            'message' => 'required|string|max:1000',
        ]);

        // Check if user has access to this ride
        $ride = Ride::findOrFail($request->ride_id);

        if (Auth::id() !== $ride->passenger_id && Auth::id() !== $ride->driver_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Determine sender type
        $senderType = Auth::id() === $ride->passenger_id ? 'passenger' : 'driver';

        // Create message
        $message = Message::create([
            'ride_id' => $request->ride_id,
            'sender_id' => Auth::id(),
            'sender_type' => $senderType,
            'recipient_id' => $request->recipient_id,
            'recipient_type' => $request->recipient_type,
            'message' => $request->message,
        ]);

        // Broadcast event
        event(new NewMessageEvent($message));

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }
}

