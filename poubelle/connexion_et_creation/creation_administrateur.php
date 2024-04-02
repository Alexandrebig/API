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

// Récupérer les données de l'URL
$url = $_SERVER['REQUEST_URI'];
$url_components = parse_url($url);
parse_str($url_components['query'], $params);

// Vérifier le chemin de l'URL
$path = $url_components['path'];
$valid_path = "/Dashboard/GestionUser/add_user";

// Vérifier le chemin de l'URL
if ($path !== $valid_path) {
    // Chemin d'URL non valide
    $response['success'] = false;
    $response['message'] = 'Chemin d\'URL non valide';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Récupérer les données de l'URL
$utilisateur = $params['utilisateur'];
$password = $params['password'];

// Échapper les données pour éviter les injections SQL (sécurité importante !)
$utilisateur = mysqli_real_escape_string($conn, $utilisateur);
$password = mysqli_real_escape_string($conn, $password);

// Requête SQL pour ajouter un nouvel utilisateur
$sql = "INSERT INTO Administrateur (utilisateur, password) VALUES ('$utilisateur', '$password')";
$result = $conn->query($sql);

// Préparation de la réponse JSON
$response = array();

if ($result) {
    // Ajout d'utilisateur réussi
    $response['success'] = true;
    $response['message'] = 'Utilisateur ajouté avec succès';
} else {
    // Échec de l'ajout d'utilisateur
    $response['success'] = false;
    $response['message'] = 'Erreur lors de l\'ajout de l\'utilisateur';
}

// Fermer la connexion à la base de données
$conn->close();

// Renvoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

