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

    // Récupérer les données de l'URL ou du corps de la requête
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $url = $_SERVER['REQUEST_URI'];
        $url_components = parse_url($url);
        parse_str($url_components['query'], $params);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $utilisateur = $_POST['utilisateur'];
        $password = $_POST['password'];
        $params = array('utilisateur' => $utilisateur, 'password' => $password);
    } else {
        // Méthode de requête non supportée
        $response['success'] = false;
        $response['message'] = 'Méthode de requête non supportée';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Vérifier le chemin de l'URL
    $valid_path = "connexion_utilisateur.php";

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $path = $_SERVER['PHP_SELF'];
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $path = $_SERVER['SCRIPT_NAME'];
    }

    if (basename($path) !== basename($valid_path)) {
        // Chemin d'URL non valide
        $response['success'] = false;
        $response['message'] = 'Chemin d\'URL non valide';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Récupérer le nom d'utilisateur et le mot de passe
    $utilisateur = $params['utilisateur'];
    $password = $params['password'];

    // Préparer la requête SQL pour éviter les injections SQL
    $stmt = $conn->prepare("SELECT * FROM Utilisateur WHERE utilisateur=:utilisateur AND password=:password");
    $stmt->bindParam(':utilisateur', $utilisateur);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Préparation de la réponse JSON
    $response = array();

    if ($result) {
        // Authentification réussie en tant qu'utilisateur
        $response['success'] = true;
        $response['message'] = 'Authentification réussie en tant qu\'utilisateur';
    } else {
        // Authentification échouée
        $response['success'] = false;
        $response['message'] = 'Nom d\'utilisateur ou mot de passe incorrect';
    }

    // Renvoyer la réponse au format JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} catch(PDOException $e) {
    // Erreur de connexion à la base de données
    $response['success'] = false;
    $response['message'] = 'Erreur de connexion à la base de données : ' . $e->getMessage();
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
