@extends('layouts.default')

@section('content')
<div class="p-6 bg-white rounded-lg shadow-lg transition duration-300 ease-in-out hover:shadow-2xl">

        <div class="flex items-center mb-4">
            <h1 class="text-3xl font-semibold text-gray-800 mr-4">{{ $article->title }}</h1>
        </div>

        <div class="mb-4">
            <div class="flex">
                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-1/2 h-auto rounded-md mr-4">
            </div>
        </div>

        <div class="mb-4">
            <h2 class="text-xl font-medium text-gray-700">Description</h2>
            <p class="text-gray-600">{{ $article->description }}</p>
        </div>

        <div class="mb-4">
            <h2 class="text-xl font-medium text-gray-700">Contexte</h2>
            <p class="text-gray-600">{{ $article->context }}</p>
        </div>

        <div class="mb-4">
            <h2 class="text-xl font-medium text-gray-700">Instructions</h2>
            <p class="text-gray-600">{{ $article->instruction }}</p>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('articles.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Retour à la liste des articles</a>

            <!-- Icône Options -->
           <!-- Icône Options -->
            <!-- Section des boutons d'option -->
        <div class="flex justify-between items-center mt-auto">
             <div class="btn-group">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-cogs"></i> Options
        </button>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item" href="{{ route('articles.edit', $article->id) }}">
                    <i class="fas fa-edit mr-2"></i> Modifier
                </a>
            </li>
            <li>
                <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-trash mr-2"></i> Supprimer
                    </button>
                </form>
            </li>
            <li>
                <a href="{{ route('articles.pdf', $article->id) }}" class="dropdown-item text-blue-600">
                    <i class="fas fa-file-pdf mr-2"></i> Télécharger PDF
                </a>
            </li>
        </ul>
    </div>
</div>
  </div>
    </div>
@endsection