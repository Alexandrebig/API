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

// Récupérer le chemin de l'URL
$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
$path = $url_components['path'];

// Vérifier le chemin de l'URL
$valid_path = "/Dashboard/GestionPlaque/blacklist";

if ($path !== $valid_path) {
    // Chemin d'URL non valide
    $response['success'] = false;
    $response['message'] = 'Chemin d\'URL non valide';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Vérifier si les paramètres sont fournis dans la requête
if (isset($_GET['plaque']) && isset($_GET['username'])) {
    // Récupérer les paramètres de la requête
    $plaque = $_GET['plaque'];
    $username = $_GET['username'];

    // Échapper les données pour éviter les injections SQL (sécurité importante !)
    $plaque = mysqli_real_escape_string($conn, $plaque);
    $username = mysqli_real_escape_string($conn, $username);

    // Exemple : Supprimer la plaque de la table de gestion des plaques
    $sql_delete = "DELETE FROM gestion_plaque WHERE plaque1 = '$plaque' OR plaque2 = '$plaque'";
    $result_delete = $conn->query($sql_delete);

    // Exemple : Ajouter la plaque à la liste noire
    $sql_blacklist = "INSERT INTO blacklist (plaque, username) VALUES ('$plaque', '$username')";
    $result_blacklist = $conn->query($sql_blacklist);

    // Préparation de la réponse JSON
    $response = array();

    if ($result_delete && $result_blacklist) {
        // Suppression de la plaque de la gestion des plaques et ajout à la liste noire réussis
        $response['success'] = true;
        $response['message'] = 'Plaque ajoutée à la liste noire et supprimée de la gestion des plaques avec succès.';
    } else {
        // Échec de la suppression de la plaque ou de l'ajout à la liste noire
        $response['success'] = false;
        $response['message'] = 'Erreur lors de la suppression de la plaque ou de l\'ajout à la liste noire.';
    }
} else {
    // Paramètres manquants dans la requête
    $response['success'] = false;
    $response['message'] = 'Paramètres manquants dans la requête.';
}

// Fermer la connexion à la base de données
$conn->close();

// Renvoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
