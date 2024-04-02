<?php
$servername = "51.210.151.13";
$username = "easyportal";
$password = "Easyportal2024!";
$database = "easyportal2024";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Définir le mode d'erreur PDO sur Exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function sendResponse($success, $message) {
        $response = array(
            'success' => $success,
            'message' => $message
        );
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $params = $_GET;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $params = $_POST;
    } else {
        sendResponse(false, 'Méthode de requête non supportée');
    }

    if (!isset($params['action'])) {
        sendResponse(false, 'Action non spécifiée');
    }

    $action = $params['action'];
    switch ($action) {
        case 'creation_utilisateur':

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

        case 'connexion_utilisateur':

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

        case 'ajouter_plaque':

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                $numeroPlaque = isset($_POST['numeroPlaque']) ? $_POST['numeroPlaque'] : null;
                $utilisateur = isset($_POST['utilisateur']) ? $_POST['utilisateur'] : null;

                // Vérifier si les données requises sont fournies
                if (!$numeroPlaque || !$utilisateur) {
                    sendResponse(false, 'Veuillez fournir le numéro de plaque et le nom d\'utilisateur');
                }

                try {
                    // Requête pour obtenir l'ID de l'utilisateur en fonction de son nom d'utilisateur
                    $stmt_user = $conn->prepare("SELECT idUtilisateur FROM Utilisateur WHERE utilisateur = :utilisateur");
                    $stmt_user->bindParam(':utilisateur', $utilisateur);
                    $stmt_user->execute();
                    $row = $stmt_user->fetch(PDO::FETCH_ASSOC);

                    // Vérifier si l'utilisateur existe
                    if (!$row) {
                        sendResponse(false, 'Utilisateur non trouvé');
                    }

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
                    sendResponse(true, $response);
                } catch (PDOException $e) {
                    // Erreur PDO
                    sendResponse(false, 'Erreur lors de l\'ajout de la plaque : ' . $e->getMessage());
                }
            } else {
                sendResponse(false, 'La méthode de requête doit être POST');
            }
            break;

        case 'supprimer_plaque':
// Vérifier la méthode de requête
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                $numeroPlaque = isset($_POST['numeroPlaque']) ? $_POST['numeroPlaque'] : null;

                // Vérifier si les données requises sont fournies
                if (!$numeroPlaque) {
                    sendResponse(false, 'Veuillez fournir le numéro de plaque');
                }

                try {
                    // Requête pour supprimer la plaque
                    $stmt = $conn->prepare("DELETE FROM Plaque WHERE numeroPlaque = :numeroPlaque");
                    $stmt->bindParam(':numeroPlaque', $numeroPlaque);
                    $stmt->execute();

                    // Préparation de la réponse JSON
                    $response['success'] = true;
                    $response['message'] = 'Plaque immatriculation supprimée avec succès';

                    // Renvoyer la réponse au format JSON
                    sendResponse(true, $response);
                } catch (PDOException $e) {
                    // Erreur PDO
                    sendResponse(false, 'Erreur lors de la suppression de la plaque : ' . $e->getMessage());
                }
            } else {
                sendResponse(false, 'La méthode de requête doit être POST');
            }
            break;

        case 'bloquer_plaque':
            // Vérifier la méthode de requête
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                $numeroPlaque = isset($_POST['numeroPlaque']) ? $_POST['numeroPlaque'] : null;

                // Vérifier si les données requises sont fournies
                if (!$numeroPlaque) {
                    sendResponse(false, 'Veuillez fournir le numéro de plaque');
                }

                try {
                    // Requête pour mettre à jour la colonne "status_blacklist"
                    $stmt = $conn->prepare("UPDATE Plaque SET status_blacklist = 'oui' WHERE numeroPlaque = :numeroPlaque");
                    $stmt->bindParam(':numeroPlaque', $numeroPlaque);
                    $stmt->execute();

                    // Préparation de la réponse JSON
                    $response['success'] = true;
                    $response['message'] = 'Plaque immatriculation bloquée avec succès';

                    // Renvoyer la réponse au format JSON
                    sendResponse(true, $response);
                } catch (PDOException $e) {
                    // Erreur PDO
                    sendResponse(false, 'Erreur lors du blocage de la plaque : ' . $e->getMessage());
                }
            } else {
                sendResponse(false, 'La méthode de requête doit être POST');
            }
            break;

        case 'debloquer_plaque':
            // Vérifier la méthode de requête
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer les données du formulaire
                $numeroPlaque = isset($_POST['numeroPlaque']) ? $_POST['numeroPlaque'] : null;

                // Vérifier si les données requises sont fournies
                if (!$numeroPlaque) {
                    sendResponse(false, 'Veuillez fournir le numéro de plaque');
                }

                try {
                    // Requête pour mettre à jour la colonne "status_blacklist"
                    $stmt = $conn->prepare("UPDATE Plaque SET status_blacklist = 'non' WHERE numeroPlaque = :numeroPlaque");
                    $stmt->bindParam(':numeroPlaque', $numeroPlaque);
                    $stmt->execute();

                    // Préparation de la réponse JSON
                    $response['success'] = true;
                    $response['message'] = 'Plaque immatriculation débloquée avec succès';

                    // Renvoyer la réponse au format JSON
                    sendResponse(true, $response);
                } catch (PDOException $e) {
                    // Erreur PDO
                    sendResponse(false, 'Erreur lors du déblocage de la plaque : ' . $e->getMessage());
                }
            } else {
                sendResponse(false, 'La méthode de requête doit être POST');
            }
            break;


        case 'afficher_administrateur':

            try {
                // Connexion à la base de données avec PDO
                $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
                // Définir le mode d'erreur PDO sur Exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Préparation de la requête SQL
                $sql = "SELECT * FROM Administrateur"; // Remplacez 'nom_table' par le nom réel de votre table
                $stmt = $conn->prepare($sql);

                // Exécution de la requête
                $stmt->execute();

                // Récupération des données sous forme de tableau associatif
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Conversion du tableau en format JSON et envoi
                echo json_encode($rows);
            } catch (PDOException $e) {
                // En cas d'erreur de connexion ou d'exécution de la requête
                echo "Erreur : " . $e->getMessage();
            }
            break;


        case 'supprimer_administrateur':
            // Vérifier la méthode de requête
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Récupérer le nom d'utilisateur de l'administrateur
                $utilisateur = isset($_POST['utilisateur']) ? $_POST['utilisateur'] : null;

                // Vérifier si le nom d'utilisateur est fourni
                if (!$utilisateur) {
                    sendResponse(false, 'Veuillez fournir le nom d\'utilisateur de l\'administrateur à supprimer');
                }

                try {
                    // Requête pour obtenir l'identifiant de l'administrateur en fonction de son nom d'utilisateur
                    $stmt_user = $conn->prepare("SELECT idAdministrateur FROM Administrateur WHERE utilisateur = :utilisateur");
                    $stmt_user->bindParam(':utilisateur', $utilisateur);
                    $stmt_user->execute();
                    $row = $stmt_user->fetch(PDO::FETCH_ASSOC);

                    // Vérifier si l'administrateur existe
                    if (!$row) {
                        sendResponse(false, 'Administrateur non trouvé');
                    }

                    $idAdministrateur = $row['idAdministrateur'];

                    // Requête pour supprimer l'administrateur de la base de données
                    $stmt_delete = $conn->prepare("DELETE FROM Administrateur WHERE idAdministrateur = :idAdministrateur");
                    $stmt_delete->bindParam(':idAdministrateur', $idAdministrateur);
                    $stmt_delete->execute();

                    // Préparation de la réponse JSON
                    $response['success'] = true;
                    $response['message'] = 'Administrateur supprimé avec succès';

                    // Renvoyer la réponse au format JSON
                    sendResponse(true, $response);
                } catch (PDOException $e) {
                    // Erreur PDO
                    sendResponse(false, 'Erreur lors de la suppression de l\'administrateur : ' . $e->getMessage());
                }
            } else {
                sendResponse(false, 'La méthode de requête doit être POST');
            }
            break;

        case 'ouverture_portail':
            try {
                if (!isset($params['plaque'])) {
                    sendResponse(false, 'Plaque d\'immatriculation non spécifiée');
                }

                $plaque = $params['plaque'];

                $stmt = $conn->prepare("SELECT * FROM Plaque WHERE numeroPlaque = :plaque");
                $stmt->bindParam(':plaque', $plaque);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    // Plaque autorisée
                    sendResponse(true, 'OK');
                } else {
                    // Plaque non autorisée
                    sendResponse(false, 'Plaque non autorisée, accès refusé');
                }
            } catch (PDOException $e) {
                // Erreur PDO
                sendResponse(false, 'Erreur lors de la vérification de la plaque : ' . $e->getMessage());
            }
            break;

        case 'export_csv':
            try {
                // Requête pour récupérer toutes les données de la table
                $requete = "SELECT * FROM Plaque";

                // Exécution de la requête
                $resultat = $conn->query($requete);

                // Vérification s'il y a des résultats
                if ($resultat->rowCount() > 0) {
                    // Forcer le téléchargement du fichier CSV
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="whitelist-plaque.csv"');

                    // Création du fichier CSV et écriture des données directement en sortie
                    $fichier_csv = fopen('php://output', 'w');

                    // Écriture de l'en-tête CSV avec le nom des colonnes de la table
                    $entete = array();
                    $row = $resultat->fetch(PDO::FETCH_ASSOC);
                    foreach ($row as $key => $value) {
                        $entete[] = $key;
                    }
                    fputcsv($fichier_csv, $entete);

                    // Réinitialisation du curseur pour récupérer toutes les lignes
                    $resultat->execute();

                    // Écriture des données de chaque ligne
                    while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                        fputcsv($fichier_csv, $ligne);
                    }

                    // Fermeture du fichier CSV
                    fclose($fichier_csv);
                    exit;
                } else {
                    sendResponse(false, 'Aucun résultat trouvé dans la table.');
                }
            } catch(PDOException $e) {
                sendResponse(false, 'Erreur de connexion à la base de données: ' . $e->getMessage());
            }
            break;

        case 'export_non_autorise_csv':
            try {
                // Requête pour récupérer les données de la table Plaque où status_blacklist est égal à "oui"
                $requete = "SELECT * FROM Plaque WHERE status_blacklist = 'oui'";

                // Exécution de la requête
                $resultat = $conn->query($requete);

                // Vérification s'il y a des résultats
                if ($resultat->rowCount() > 0) {
                    // Forcer le téléchargement du fichier CSV
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="blacklist-plaque.csv"');

                    // Création du fichier CSV et écriture des données directement en sortie
                    $fichier_csv = fopen('php://output', 'w');

                    // Écriture de l'en-tête CSV avec le nom des colonnes de la table
                    $entete = array();
                    $row = $resultat->fetch(PDO::FETCH_ASSOC);
                    foreach ($row as $key => $value) {
                        $entete[] = $key;
                    }
                    fputcsv($fichier_csv, $entete);

                    // Réinitialisation du curseur pour récupérer toutes les lignes
                    $resultat->execute();

                    // Écriture des données de chaque ligne
                    while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)) {
                        fputcsv($fichier_csv, $ligne);
                    }

                    // Fermeture du fichier CSV
                    fclose($fichier_csv);
                    exit;
                } else {
                    sendResponse(false, 'Aucune plaque non autorisée trouvée dans la table.');
                }
            } catch(PDOException $e) {
                sendResponse(false, 'Erreur de connexion à la base de données: ' . $e->getMessage());
            }
            break;


        default:
            sendResponse(false, 'Action non reconnue');
    }
} catch(PDOException $e) {
    // Erreur de connexion à la base de données
    sendResponse(false, 'Erreur de connexion à la base de données : ' . $e->getMessage());
}
?>
