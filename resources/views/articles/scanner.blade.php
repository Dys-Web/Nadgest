@extends('layouts.default')

@section('content')

    <h1 style="text-align: center;">Scanner votre QR Code</h1>
    <div id="qr-reader" class="w-96 h-96 m-auto"></div>
    <div id="qr-reader-results" style="text-align: center; margin-top: 20px;"></div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Vérifie si une chaîne est une URL
            function isValidURL(str) {
                return str.startsWith("http://") || str.startsWith("https://");
            }

            // Fonction de callback lors de la détection du QR code
            function onScanSuccess(decodedText, decodedResult) {
                console.log("QR Code détecté :", decodedText);

                // Vérification : Est-ce une URL ?
                if (isValidURL(decodedText)) {
                    window.location.href = decodedText; // Redirection automatique
                } else {
                    document.getElementById('qr-reader-results').innerText = "QR Code invalide.";
                }
            }

            // Fonction de callback lors d'une erreur
            function onScanError(errorMessage) {
                console.error(`Erreur de scan : ${errorMessage}`);
                // Affiche l'erreur seulement si le scan a commencé mais échoue
                document.getElementById('qr-reader-results').innerText = "Erreur lors du scan.";
            }

            // Initialisation du scanner
            const html5QrcodeScanner = new Html5Qrcode("qr-reader");

            // Vérification des caméras disponibles
            Html5Qrcode.getCameras().then(devices => {
                console.log("Caméras détectées :", devices);

                // Vérifier si des caméras sont disponibles
                if (!devices || devices.length === 0) {
                    console.error("Aucune caméra détectée.");
                    document.getElementById('qr-reader-results').innerText = "Aucune caméra détectée.";
                    return; // Arrête le processus ici
                }

                // Sélectionne la première caméra détectée
                const cameraId = devices[0].id;

                // Vérification supplémentaire : Est-ce que cameraId est valide ?
                if (!cameraId) {
                    console.error("Problème avec la caméra sélectionnée.");
                    document.getElementById('qr-reader-results').innerText = "Problème avec la caméra.";
                    return; // Arrête le processus ici
                }

                // Démarrer le scanner après vérifications
                html5QrcodeScanner.start(
                    cameraId,
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    onScanSuccess,
                    onScanError // Remplace la callback d'erreur par la nouvelle fonction
                ).then(() => {
                    console.log("Scanner démarré avec succès.");
                }).catch(err => {
                    console.error(`Erreur lors du démarrage du scanner : ${err}`);
                    document.getElementById('qr-reader-results').innerText = "Impossible de démarrer le scanner.";
                });

            }).catch(err => {
                console.error("Impossible d'accéder aux caméras :", err);
                document.getElementById('qr-reader-results').innerText = "Accès à la caméra refusé.";
            });
        });
    </script>
    @endpush

@endsection
