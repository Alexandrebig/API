<?php

// Paramètres de connexion à la base de données
$servername = "51.210.151.13";
$username = "easyportal";
$password = "Easyportal2024!";
$database = "easyportal2024";

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
?>
