<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10); // Récupère tous les utilisateurs
        return view('users.index', compact('users'));
    }
    
    public function show(string $id)
    {
        $users = User::findOrFail($id);
        return view('users.show', compact('users'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id); 
        return view('users.edit', compact('user')); 
    }


    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id); // Récupère l'utilisateur ou renvoie une erreur 404
    
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);
    
        // Mettre à jour les informations de l'utilisateur
        $user->update($validatedData);
    
        // Rediriger avec un message de succès
        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function create()
    {
        return view('users.create'); // Retourne une vue pour créer un utilisateur
    }

    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
    
        // Créer un nouvel utilisateur
        User::create($validatedData);
    
        // Rediriger avec un message de succès
        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id); // Récupère l'utilisateur ou renvoie une erreur 404
        $user->delete(); // Supprime l'utilisateur

        // Redirige avec un message de succès
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }
}
