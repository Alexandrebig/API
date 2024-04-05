<?php

// Configuration de la base de données
$servername = "51.210.151.13";
$username = "easyportal";
$password = "Easyportal2024!";
$database = "easyportal2024";

// Connexion à la base de données avec PDO
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Définition du mode d'erreur PDO sur Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, afficher l'erreur
    die(json_encode(array('error' => 'Erreur de connexion à la base de données : ' . $e->getMessage())));
}

// Récupération de l'action à effectuer
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Traitement en fonction de l'action
switch ($action) {
    case 'connexion_administrateur':
        // Récupération des paramètres
        $utilisateur = isset($_GET['utilisateur']) ? $_GET['utilisateur'] : '';
        $password = isset($_GET['password']) ? $_GET['password'] : '';
        // Requête SQL préparée pour vérifier l'utilisateur et le mot de passe
        $query = "SELECT * FROM Administrateur WHERE utilisateur = ? AND password = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$utilisateur, $password]);

        // Vérification du résultat
        if ($stmt->rowCount() > 0) {
            // Utilisateur trouvé, envoi d'une réponse JSON
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = array(
                'utilisateur' => $row['utilisateur'],
                'password' => $row['password']
            );
            echo json_encode($response);
        } else {
            // Utilisateur non trouvé
            echo json_encode(array('message' => 'Identifiants incorrects'));
        }
        break;

    case 'connexion_utilisateur':
        // Récupération des paramètres
        $utilisateur = isset($_GET['utilisateur']) ? $_GET['utilisateur'] : '';
        $password = isset($_GET['password']) ? $_GET['password'] : '';
        // Requête SQL préparée pour vérifier l'utilisateur et le mot de passe
        $query = "SELECT * FROM Utilisateur WHERE utilisateur = ? AND password = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$utilisateur, $password]);

        // Vérification du résultat
        if ($stmt->rowCount() > 0) {
            // Utilisateur trouvé, envoi d'une réponse JSON
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = array(
                'utilisateur' => $row['utilisateur'],
                'password' => $row['password']
            );
            echo json_encode($response);
        } else {
            // Utilisateur non trouvé
            echo json_encode(array('message' => 'Identifiants incorrects'));
        }
        break;

    case 'creation_administrateur':
        // Récupération des paramètres
        $utilisateur = isset($_POST['utilisateur']) ? $_POST['utilisateur'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
        $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
        $mail = isset($_POST['mail']) ? $_POST['mail'] : '';

        // Requête SQL préparée pour insérer un nouvel administrateur
        $query = "INSERT INTO Administrateur (utilisateur, password, nom, prenom, mail) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$utilisateur, $password, $nom, $prenom, $mail]);

        // Vérification du résultat
        if ($stmt->rowCount() > 0) {
            // Renvoi d'une réponse JSON avec les détails de l'administrateur créé
            $response = array(
                'utilisateur' => $utilisateur,
                'password' => $password,
                'nom' => $nom,
                'prenom' => $prenom,
                'mail' => $mail
            );
            echo json_encode($response);
        } else {
            // En cas d'échec de l'insertion
            echo json_encode(array('message' => 'Erreur lors de la création de l\'administrateur'));
        }
        break;

    case 'creation_utilisateur':
        // Récupération des paramètres
        $utilisateur = isset($_POST['utilisateur']) ? $_POST['utilisateur'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
        $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
        $mail = isset($_POST['mail']) ? $_POST['mail'] : '';

        // Requête SQL préparée pour insérer un nouvel utilisateur
        $query = "INSERT INTO Utilisateur (utilisateur, password, nom, prenom, mail) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$utilisateur, $password, $nom, $prenom, $mail]);

        // Vérification du résultat
        if ($stmt->rowCount() > 0) {
            // Renvoi d'une réponse JSON avec les détails de l'utilisateur créé
            $response = array(
                'utilisateur' => $utilisateur,
                'password' => $password,
                'nom' => $nom,
                'prenom' => $prenom,
                'mail' => $mail
            );
            echo json_encode($response);
        } else {
            // En cas d'échec de l'insertion
            echo json_encode(array('message' => 'Erreur lors de la création de l\'utilisateur'));
        }
        break;

    case 'ajouter_plaque':
        $utilisateur = isset($_GET['utilisateur']) ? $_GET['utilisateur'] : '';
        $numeroPlaque = isset($_GET['numeroPlaque']) ? $_GET['numeroPlaque'] : '';

        // Recherche de l'idUtilisateur dans la table Utilisateur
        $queryUser = "SELECT idUtilisateur FROM Utilisateur WHERE utilisateur = ?";
        $stmtUser = $pdo->prepare($queryUser);
        $stmtUser->execute([$utilisateur]);
        $userId = $stmtUser->fetchColumn();

        if ($userId) {
            // L'utilisateur a été trouvé, procéder à l'insertion de la plaque dans la table Plaque
            $queryInsert = "INSERT INTO Plaque (idUtilisateur, numeroPlaque) VALUES (?, ?)";
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->execute([$userId, $numeroPlaque]);

            if ($stmtInsert->rowCount() > 0) {
                // Renvoi d'une réponse JSON avec les détails de la plaque ajoutée
                $response = array(
                    'utilisateur' => $utilisateur,
                    'numeroPlaque' => $numeroPlaque
                );
                echo json_encode($response);
            } else {
                // En cas d'échec de l'ajout de la plaque
                echo json_encode(array('message' => 'Erreur lors de l\'ajout de la plaque'));
            }
        } else {
            // L'utilisateur n'a pas été trouvé
            echo json_encode(array('message' => 'Utilisateur non trouvé'));
        }
        break;

    case 'supprimer_plaque':
        $numeroPlaque = isset($_POST['numeroPlaque']) ? $_POST['numeroPlaque'] : '';

        // Votre code pour supprimer une plaque
        // Exemple de suppression dans la base de données (à adapter selon votre structure de base de données)
        $query = "DELETE FROM Plaque WHERE numeroPlaque = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$numeroPlaque]);

        $response = array(); // Initialisation de la variable $response

        if ($stmt->rowCount() > 0) {
            $response = array('numeroPlaque' => $numeroPlaque);
        } else {
            $response = array('message' => 'Erreur lors de la suppression de la plaque');
        }

        echo json_encode($response);
        break;

    case 'bloquer_plaque':
        $numeroPlaque = isset($_POST['numeroPlaque']) ? $_POST['numeroPlaque'] : '';

        // Votre code pour débloquer une plaque
        // Exemple de mise à jour dans la base de données (à adapter selon votre structure de base de données)
        $query = "UPDATE Plaque SET status_blacklist = 'oui' WHERE numeroPlaque = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$numeroPlaque]);

        $response = array(); // Initialisation de la variable $response

        if ($stmt->rowCount() > 0) {
            $response = array('numeroPlaque' => $numeroPlaque, 'status_blacklist' => 'oui');
        } else {
            $response = array('message' => 'Erreur lors du déblocage de la plaque');
        }

        echo json_encode($response);
        break;

    case 'debloquer_plaque':
        $numeroPlaque = isset($_POST['numeroPlaque']) ? $_POST['numeroPlaque'] : '';

        // Votre code pour débloquer une plaque
        // Exemple de mise à jour dans la base de données (à adapter selon votre structure de base de données)
        $query = "UPDATE Plaque SET status_blacklist = 'non' WHERE numeroPlaque = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$numeroPlaque]);

        $response = array(); // Initialisation de la variable $response

        if ($stmt->rowCount() > 0) {
            $response = array('numeroPlaque' => $numeroPlaque, 'status_blacklist' => 'non');
        } else {
            $response = array('message' => 'Erreur lors du déblocage de la plaque');
        }

        echo json_encode($response);
        break;


}

// Fermeture de la connexion PDO
$pdo = null;

?>
