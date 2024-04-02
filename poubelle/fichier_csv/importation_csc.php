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
            // Par exemple, l'analyser et insérer les données dans la base de données

            // Exemple : Analyser et insérer les données dans la base de données
            $lines = explode("\n", $csv_content);

            foreach ($lines as $line) {
                $data = str_getcsv($line, ',');
                // Insérer les données dans la base de données (vous devez adapter cette partie selon votre structure de base de données)
                $sql = "INSERT INTO Log (colonne1, colonne2, ...) VALUES ('$data[0]', '$data[1]', ...)";
                $conn->query($sql);
            }

            $response['success'] = true;
            $response['message'] = 'Importation du fichier CSV réussie.';
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
