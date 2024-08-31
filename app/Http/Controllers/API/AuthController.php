<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Méthode pour l'inscription des utilisateurs
    public function register(Request $request)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string',
            'birth' => 'nullable|date',
            'number' => 'nullable|string|max:20',
            'status' => 'nullable|string',
        ]);

        // Retourne les erreurs de validation si la validation échoue
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Création d'un nouvel utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hachage du mot de passe
            'role' => $request->role,
            'birth' => $request->birth,
            'number' => $request->number,
            'status' => $request->status,
        ]);

        // Création d'un token d'authentification pour l'utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;

        // Retourne les informations de l'utilisateur et le token
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // Méthode pour la connexion des utilisateurs
    public function login(Request $request)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Retourne les erreurs de validation si la validation échoue
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Vérifie les identifiants de connexion
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Récupère l'utilisateur
        $user = User::where('email', $request->email)->firstOrFail();
        
        // Supprime tous les tokens existants de l'utilisateur
        $user->tokens()->delete();
        // Crée un nouveau token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Met à jour la date de dernière connexion
        $user->update(['last_login_at' => now()]);

        // Retourne les informations de l'utilisateur et le nouveau token
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // Méthode pour la déconnexion des utilisateurs
    public function logout(Request $request)
    {
        // Supprime le token d'accès actuel de l'utilisateur
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Méthode pour récupérer les informations de l'utilisateur connecté
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}