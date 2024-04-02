<?php
// Paramètres de connexion à la base de données
$servername = "51.210.151.13";
$username = "easyportal";
$password = "Easyportal2024!";
$database = "easyportal2024";

try {
    // Création de la connexion PDO
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Définir le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier la méthode de requête
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $numeroPlaque = $_POST['numeroPlaque'];
        $utilisateur = $_POST['utilisateur'];

        // Requête pour obtenir l'ID de l'utilisateur en fonction de son nom d'utilisateur
        $stmt_user = $conn->prepare("SELECT idUtilisateur FROM Utilisateur WHERE utilisateur = :utilisateur");
        $stmt_user->bindParam(':utilisateur', $utilisateur);
        $stmt_user->execute();
        $row = $stmt_user->fetch(PDO::FETCH_ASSOC);
        $idUtilisateur = $row['idUtilisateur'];

        // Préparer la requête SQL pour insérer la plaque
        $stmt_plaque = $conn->prepare("INSERT INTO Plaque (numeroPlaque, idUtilisateur) VALUES (:numeroPlaque, :idUtilisateur)");
        $stmt_plaque->bindParam(':numeroPlaque', $numeroPlaque);
        $stmt_plaque->bindParam(':idUtilisateur', $idUtilisateur);
        $stmt_plaque->execute();

        // Récupérer l'ID de la plaque insérée
        $plaqueId = $conn->lastInsertId();

        // Préparation de la réponse JSON
        $response['success'] = true;
        $response['message'] = 'Plaque immatriculation ajoutée avec succès';
        $response['plaqueId'] = $plaqueId;

        // Renvoyer la réponse au format JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // Méthode de requête non supportée
        $response['success'] = false;
        $response['message'] = 'Méthode de requête non supportée';
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} catch(PDOException $e) {
    // Erreur de connexion à la base de données
    $response['success'] = false;
    $response['message'] = 'Erreur de connexion à la base de données : ' . $e->getMessage();
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
