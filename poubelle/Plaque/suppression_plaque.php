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

// Vérifier si le paramètre est fourni dans la requête
if (isset($_GET['NumeroPlaque'])) {
    // Récupérer le paramètre de la requête
    $NumeroPlaque = $_GET['NumeroPlaque'];

    // Échapper les données pour éviter les injections SQL (sécurité importante !)
    $NumeroPlaque = mysqli_real_escape_string($conn, $NumeroPlaque);

    // Exemple : Supprimer les données de la base de données
    $sql = "DELETE FROM Plaque WHERE NumeroPlaque = '$NumeroPlaque'";
    $result = $conn->query($sql);

    // Préparation de la réponse JSON
    $response = array();

    if ($result) {
        // Suppression réussie
        $response['success'] = true;
        $response['message'] = 'Plaque supprimée avec succès.';
    } else {
        // Échec de la suppression
        $response['success'] = false;
        $response['message'] = 'Erreur lors de la suppression de la plaque.';
    }
} else {
    // Paramètre manquant dans la requête
    $response['success'] = false;
    $response['message'] = 'Paramètre manquant dans la requête.';
}

// Fermer la connexion à la base de données
$conn->close();

// Renvoyer la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>

