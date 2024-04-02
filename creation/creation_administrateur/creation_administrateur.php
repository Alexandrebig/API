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
        $utilisateur = $_POST['utilisateur'];
        $password = $_POST['password'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $mail = $_POST['mail'];

        // Préparer la requête SQL pour éviter les injections SQL
        $stmt = $conn->prepare("INSERT INTO Administrateur (utilisateur, password, nom, prenom, mail) VALUES (:utilisateur, :password, :nom, :prenom, :mail)");
        $stmt->bindParam(':utilisateur', $utilisateur);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();

        // Préparation de la réponse JSON
        $response['success'] = true;
        $response['message'] = 'Compte administrateur créé avec succès';

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
