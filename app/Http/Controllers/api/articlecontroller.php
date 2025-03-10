<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ArticleController extends Controller
{
    /**
     * Affiche la liste paginée des articles.
     */
    public function index()
    {
        try {
            $articles = Article::paginate(10); // Pagination de 10 articles par page
            return response()->json($articles, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération des articles : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Enregistre un nouvel article.
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'context' => 'required|string',
            'instruction' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Création de l'article
        $article = new Article($validated);
        $article->user_id = Auth::id();

        try {
            // Gestion de l'image
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('articles', 'public');
                $article->image = $imagePath;
            }

            // Sauvegarde de l'article
            $article->save();
            return response()->json($article, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'enregistrement de l\'article : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Affiche un article spécifique.
     */
    public function show(string $id)
    {
        try {
            $article = Article::findOrFail($id);
            return response()->json($article);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la récupération de l\'article : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Met à jour un article existant.
     */
    public function update(Request $request, string $id)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        // Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'context' => 'required|string',
            'instruction' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $article = Article::findOrFail($id);

            // Vérifier que l'utilisateur est le propriétaire de l'article
            if ($article->user_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Mise à jour des données
            $article->fill($validated);

            // Gestion de l'image
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image si elle existe
                if ($article->image && Storage::disk('public')->exists($article->image)) {
                    Storage::disk('public')->delete($article->image);
                }
                $image = $request->file('image');
                $imagePath = $image->store('articles', 'public');
                $article->image = $imagePath;
            }

            // Sauvegarde des modifications
            $article->save();

            return response()->json($article);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour de l\'article : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprime un article.
     */
    public function destroy(string $id)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        try {
            $article = Article::findOrFail($id);

            // Vérifier que l'utilisateur est le propriétaire de l'article
            if ($article->user_id !== Auth::id()) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Suppression de l'image associée
            if ($article->image && Storage::disk('public')->exists($article->image)) {
                Storage::disk('public')->delete($article->image);
            }

            // Suppression de l'article
            $article->delete();

            return response()->json(['message' => 'Article supprimé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de l\'article: ' . $e->getMessage()], 500);
        }
    }
}
