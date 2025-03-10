<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

//  Page d'accueil (redirige vers login)
Route::get('/', function () {
    return view('auth.login');
});

//  Routes accessibles uniquement aux invités (non connectés)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Routes accessibles uniquement aux utilisateurs connectés
Route::middleware(['auth', 'verified'])->group(function () {
    
    //  Tableau de bord (redirige vers la liste des articles)
    Route::get('/dashboard', function () {
        return redirect()->route('articles.index');
    })->name('dashboard');

    //  Routes pour les articles
    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/create', [ArticleController::class, 'create'])->name('articles.create');
        Route::post('/', [ArticleController::class, 'store'])->name('articles.store');
        Route::get('/{id}', [ArticleController::class, 'show'])->name('articles.show');
        Route::get('/{id}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/{id}', [ArticleController::class, 'update'])->name('articles.update');
        Route::delete('/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');
        Route::get('/pdf/{id}', [ArticleController::class, 'PDFView'])->name('articles.pdf');
    });

    //  Routes pour le profil utilisateur
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Déconnexion
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
// Route scanner accessible sans authentification
Route::get('/scanner', [ArticleController::class, 'scanner'])->name('articles.scanner');

// Routes d'authentification (gérées par Laravel Breeze)
require __DIR__.'/auth.php';
