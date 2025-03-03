<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DriverNotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:chauffeur');
    }

    public function index()
    {
        try {
            $notifications = Auth::user()
                ->notifications()
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($notification) {
                    $data = $notification->data;

                    // Formater la date scheduled_at si elle existe
                    if (isset($data['scheduled_at'])) {
                        try {
                            $data['scheduled_at'] = Carbon::parse($data['scheduled_at'])->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            Log::warning('Erreur lors du formatage de scheduled_at: ' . $e->getMessage());
                            $data['scheduled_at'] = null;
                        }
                    }

                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'read_at' => $notification->read_at ? Carbon::parse($notification->read_at)->format('Y-m-d H:i:s') : null,
                        'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                        'message' => $data['message'] ?? '',
                        'passenger_name' => $data['passenger_name'] ?? null,
                        'scheduled_at' => $data['scheduled_at'] ?? null,
                        'pickup_address' => $data['pickup_address'] ?? null,
                        'destination_address' => $data['destination_address'] ?? null,
                        'ride_id' => $data['ride_id'] ?? null,
                        'status' => $data['status'] ?? null
                    ];
                });

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du chargement des notifications'
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            Auth::user()->unreadNotifications->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications ont été marquées comme lues'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage des notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du marquage des notifications'
            ], 500);
        }
    }

    public function markAsRead($id)
    {
        try {
            $notification = Auth::user()
                ->notifications()
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de la notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du marquage de la notification'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = Auth::user()
                ->notifications()
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression de la notification'
            ], 500);
        }
    }
}

