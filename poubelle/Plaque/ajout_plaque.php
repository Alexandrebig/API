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

// Vérifier si les paramètres sont fournis dans la requête
if (isset($_GET['NumeroPlaque']) && isset($_GET['username'])) {
    // Récupérer les paramètres de la requête
    $NumeroPlaque = $_GET['NumeroPlaque'];
    $username = $_GET['username'];

    // Échapper les données pour éviter les injections SQL (sécurité importante !)
    $NumeroPlaque = mysqli_real_escape_string($conn, $NumeroPlaque);
    $username = mysqli_real_escape_string($conn, $username);

    // Vérifier si la plaque2 est vide, alors n'ajouter que la NumeroPlaque
    if (empty($NumeroPlaque)) {
        // Exemple : Insérer les données dans la base de données
        exit("Erreur: Le programme a rencontré une erreur et s'est arrêté.");
    } else {
        // Exemple : Insérer les données dans la base de données avec les deux plaques
        $sql = "INSERT INTO Plaque (NumeroPlaque, username) VALUES ('$NumeroPlaque', '$username')";
    }

    $result = $conn->query($sql);

    // Préparation de la réponse JSON
    $response = array();

    if ($result) {
        // Ajout de plaque réussi
        $response['success'] = true;
        $response['message'] = 'Plaques ajoutées avec succès.';
    } else {
        // Échec de l'ajout de plaque
        $response['success'] = false;
        $response['message'] = 'Erreur lors de l\'ajout des plaques.';
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
