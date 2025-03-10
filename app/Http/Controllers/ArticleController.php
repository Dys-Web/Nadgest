<?php

namespace App\Http\Controllers;

use App\Models\Article;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;



class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::all();
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(!Auth::check()){
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer un article.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'context' => 'required|string',
            'instruction' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Créer un nouvel article avec les données du formulaire
    $article = new Article($validated);

    // Vérifier si un fichier image a été téléchargé
    if ($request->hasFile('image')) {
        // Récupérer le fichier image
        $image = $request->file('image');
        
        // Générer un nom unique pour l'image
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        
        // Sauvegarder l'image dans le dossier public/images via le disque public
        $imagePath = $image->store('articles', 'public');
        
        // Enregistrer le chemin relatif de l'image
        $article->image = $imagePath;
    }

    $article->user_id = Auth::id();
    // Sauvegarder l'article dans la base de données
    $article->save();

    // Rediriger avec un message de succès
    return redirect()->route('articles.index')->with('success', 'Article ajouté avec succès.');
    }

    /**
     * Affiche un article spécifique.
     */
    public function show(string $id)
    {
        $article = Article::findOrFail($id);
        return view('articles.show', compact('article'));
    }

    /**
     * Affiche le formulaire de modification d'un article.
     */
    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit', compact('article'));
    }

    /**
     * Met à jour un article existant
     */
    public function update(Request $request, string $id)
    {
        // Validation des données du formulaire
       $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'context' => 'required|string',
            'instruction' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $article = Article::findOrFail($id);
        $article->fill($request->all());

        if ($request->hasFile('image')){
            $article->image = $request->file('image')->store('articles', 'public');
        }

        $article->save();

        return redirect()->route('articles.index')->with('success', 'Article modifié avec succes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return redirect()->route('articles.index')->with('success', 'Article supprimé avec succes.');
    }

    /**
     * Génère une signature pour le QR Code.
     */
    public function sign($text,$key){
        return hash_hmac('sha256', $text, $key);
    }

    /**
     * Génère la vue PDF d'un article avec son QR Code.
     */
    public function PDFView(string $id)
    {
        // Recherche de l'article avec son ID
        $article = Article::findOrFail($id);
    
        // Génération de l'URL unique de l'article
        $serverIp = '192.168.1.10'; // Mets ici ton IPv4 trouvée avec ipconfig
        $uniqid = "http://{$serverIp}:8000/articles/{$article->id}";
    
        // Clé secrète pour la signature (exemple)
        $key = 'secret';
        $signature = $this->sign($uniqid, $key);
    
        // Contenu du QR Code sous forme de JSON
        $qr_content = json_encode(['id' => $uniqid, 'signature' => $signature]);
    
        // Génération du QR Code
        $qr_code = QrCode::size(300)->generate($uniqid);
    
        // Création d'un tableau pour stocker les données du QR Code et de l'image
        $data = [
            'qr_code' => $qr_code,
            'image_url' => asset('storage/' . $article->image)
        ];
    
        // Retour de la vue avec l'article et les données du QR Code
        return view('articles.pdf', compact('article', 'data'));
    }

    public function scanner()
    {
        return view('articles.scanner');
    }
    
}
