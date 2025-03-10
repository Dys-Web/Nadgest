@extends('layouts.default')

@section('title', 'Liste des Articles')

@section('content')
    <h1>Liste des articles</h1>
    <a href="{{ route('articles.create') }}" class="btn btn-primary mb-3">Créer un nouvel article</a>

    <!-- Conteneur des articles (ils seront alignés verticalement sur tous les écrans) -->
    <div class="flex flex-col">
        <!-- Boucle pour afficher tous les articles -->
        @foreach($articles as $article)
            <!-- Article avec effet de survol -->
            <div class="flex mb-6 w-full p-4">
                <div class="flex bg-white rounded-lg shadow-md overflow-hidden w-full 
                            transition-transform duration-500 ease-in-out hover:translate-x-5 
                            hover:shadow-xl hover:bg-gray-100">
                    <!-- Section Image -->
                    <div class="w-48 h-48 bg-gray-300 flex-shrink-0">
                        <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover rounded-l-lg">
                    </div>

                    <!-- Section Contenu -->
                    <div class="p-4 flex flex-col justify-between w-full">
                        <h5 class="text-lg font-bold mb-2">{{ $article->title }}</h5>
                        <p class="text-gray-700 text-sm mb-4">{{ Str::limit($article->description, 100) }}...</p>

                        <!-- Section des boutons d'option -->
                        <div class="flex justify-between items-center mt-auto">
                            <div class="btn-group">
                                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cogs"></i> Options
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('articles.show', $article->id) }}">
                                        <i class="fas fa-eye"></i> Lire
                                    </a></li>
                                    @if ($article->user_id === auth()->user()->id)
                                        <li><a class="dropdown-item" href="{{ route('articles.edit', $article->id) }}">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a></li>
                                        <li>
                                            <form action="{{ route('articles.destroy', $article->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
