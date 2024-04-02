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

    // Fonction pour envoyer une réponse JSON
    function sendResponse($success, $message) {
        $response = array(
            'success' => $success,
            'message' => $message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Récupérer les données de la requête
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $params = $_GET;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $params = $_POST;
    } else {
        sendResponse(false, 'Méthode de requête non supportée');
    }

    // Vérifier si une action est spécifiée
    if (!isset($params['action'])) {
        sendResponse(false, 'Action non spécifiée');
    }

    // Traitement des différentes actions en fonction du paramètre 'action'
    $action = $params['action'];
    switch ($action) {
        case 'connexion_utilisateur':
            // Traitement de la connexion utilisateur
            if (!isset($params['utilisateur']) || !isset($params['password'])) {
                sendResponse(false, 'Nom d\'utilisateur et mot de passe requis');
            }

            $utilisateur = $params['utilisateur'];
            $password = $params['password'];

            $stmt = $conn->prepare("SELECT * FROM Utilisateur WHERE utilisateur=:utilisateur AND password=:password");
            $stmt->bindParam(':utilisateur', $utilisateur);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                sendResponse(true, 'Authentification réussie en tant qu\'utilisateur');
            } else {
                sendResponse(false, 'Nom d\'utilisateur ou mot de passe incorrect');
            }
            break;

        case 'connexion_administrateur':
            // Traitement de la connexion administrateur
            if (!isset($params['utilisateur']) || !isset($params['password'])) {
                sendResponse(false, 'Nom d\'utilisateur et mot de passe requis');
            }

            $utilisateur = $params['utilisateur'];
            $password = $params['password'];

            $stmt = $conn->prepare("SELECT * FROM Administrateur WHERE utilisateur=:utilisateur AND password=:password");
            $stmt->bindParam(':utilisateur', $utilisateur);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                sendResponse(true, 'Authentification réussie en tant qu\'administrateur');
            } else {
                sendResponse(false, 'Nom d\'utilisateur ou mot de passe incorrect');
            }
            break;

        case 'creation_utilisateur':
            // Traitement de la création d'utilisateur
            if (!isset($params['utilisateur']) || !isset($params['password']) || !isset($params['nom']) || !isset($params['prenom']) || !isset($params['mail']) || !isset($params['status'])) {
                sendResponse(false, 'Tous les champs sont requis pour créer un utilisateur');
            }

            $utilisateur = $params['utilisateur'];
            $password = $params['password'];
            $nom = $params['nom'];
            $prenom = $params['prenom'];
            $mail = $params['mail'];
            $status = $params['status'];

            $stmt = $conn->prepare("INSERT INTO Utilisateur (utilisateur, password, nom, prenom, mail, status) VALUES (:utilisateur, :password, :nom, :prenom, :mail, :status)");
            $stmt->bindParam(':utilisateur', $utilisateur);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':mail', $mail);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            sendResponse(true, 'Compte utilisateur créé avec succès');
            break;

        case 'creation_administrateur':
            // Traitement de la création d'administrateur
            if (!isset($params['utilisateur']) || !isset($params['password']) || !isset($params['nom']) || !isset($params['prenom']) || !isset($params['mail'])) {
                sendResponse(false, 'Tous les champs sont requis pour créer un administrateur');
            }

            $utilisateur = $params['utilisateur'];
            $password = $params['password'];
            $nom = $params['nom'];
            $prenom = $params['prenom'];
            $mail = $params['mail'];

            $stmt = $conn->prepare("INSERT INTO Administrateur (utilisateur, password, nom, prenom, mail) VALUES (:utilisateur, :password, :nom, :prenom, :mail)");
            $stmt->bindParam(':utilisateur', $utilisateur);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();

            sendResponse(true, 'Compte administrateur créé avec succès');
            break;

        default:
            sendResponse(false, 'Action non reconnue');
    }
} catch(PDOException $e) {
    // Erreur de connexion à la base de données
    sendResponse(false, 'Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>
