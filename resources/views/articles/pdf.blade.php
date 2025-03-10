@extends('layouts.default')

@section('content')
    <div class="container mx-auto p-6">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold">{{ $article->title }}</h1>
        </div>

        <div class="mb-4 flex items-center space-x-4">
            <!-- Image de l'article -->
            @if (!empty($data['image_url']))
                <img src="{{ $data['image_url'] }}" alt="{{ $article->title }}" class="w-1/2 h-auto rounded-lg shadow-md">
            @else
                <p class="text-gray-600 italic">Pas d'image disponible</p>
            @endif  
        
            <!-- QR Code généré pour l'article -->
            <div class="qr_code p-2 bg-gray-100 rounded-lg shadow-md">
                {!! $data['qr_code'] !!}
            </div>
        </div>
        

        <div class="mb-4">
            <h2 class="text-xl font-medium">Contexte</h2>
            <p class="text-gray-600">{{ $article->context }}</p>
        </div>

        <div class="mb-4">
            <h2 class="text-xl font-medium">Instructions</h2>
            <p class="text-gray-600">{{ $article->instruction }}</p>
        </div>

        <div class="mt-4 text-center">
            <!-- Nouveau bouton pour télécharger le PDF avec une icône -->
            <button id="download-pdf" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-4 hover:bg-blue-600 flex items-center justify-center">
                <!-- Icône PDF avec Font Awesome -->
                <i class="fas fa-file-pdf mr-2"></i> Télécharger en PDF
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    // Quand le bouton de téléchargement PDF est cliqué
    document.getElementById('download-pdf').addEventListener('click', function () {
        const element = document.querySelector('.container');  // Récupérer tout le contenu de la page uniquement la div de l'article en particulier

        // Options de configuration pour html2pdf.js
        const options = {
            margin:       10,
            filename:     'article-{{ $article->id }}.pdf',
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Générer et télécharger le PDF
        html2pdf().from(element).set(options).save();
    });
</script>
@endpush


