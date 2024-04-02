<?php
// Paramètres de connexion à la base de données
$servername = "51.210.151.13";
$username = "easyportal";
$password = "Easyportal2024!";
$database = "easyportal2024";

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Vérifier si l'URL est fournie dans la requête
if (isset($_GET['url'])) {
    // Récupérer l'URL du fichier CSV depuis la requête
    $csv_url = $_GET['url'];

    // Vérifier que l'URL est valide (vous pouvez ajouter des validations supplémentaires)
    if (filter_var($csv_url, FILTER_VALIDATE_URL)) {
        // Télécharger le fichier CSV depuis l'URL
        $csv_content = file_get_contents($csv_url);

        // Vérifier si le téléchargement a réussi
        if ($csv_content !== false) {
            // Vous pouvez traiter le contenu du fichier CSV ici selon vos besoins
            // Par exemple, l'afficher, le sauvegarder localement, etc.

            // Exemple : Afficher le contenu
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="exported_data.csv"');
            echo $csv_content;
            exit;
        } else {
            $response['success'] = false;
            $response['message'] = 'Échec du téléchargement du fichier CSV depuis l\'URL.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'URL du fichier CSV non valide.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'L\'URL du fichier CSV est manquante dans la requête.';
}

// Fermer la connexion à la base de données
$conn->close();

// Renvoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
