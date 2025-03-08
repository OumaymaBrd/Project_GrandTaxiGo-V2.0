<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:chauffeur,passager',
            'phone' => 'required|string|max:20'
        ]);

        // Générer un code de connexion aléatoire
        $connectionCode = rand(100000, 999999);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'connection_code' => $connectionCode // Assurez-vous d'ajouter cette colonne à votre table users
        ]);

        // Envoyer un email de bienvenue
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
            Log::info('Email de bienvenue envoyé', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Échec de l\'envoi de l\'email de bienvenue', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        Auth::login($user);

        // Marquer comme première visite
        session(['first_visit' => true]);

        return redirect()->route('dashboard')
            ->with('success', 'Compte créé avec succès! Un email de bienvenue a été envoyé à votre adresse email.');
    }
}

