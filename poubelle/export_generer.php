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

    // Requête pour récupérer toutes les données de la table
    $requete = "SELECT * FROM Plaque";

    // Exécution de la requête
    $resultat = $conn->query($requete);

    // Vérification s'il y a des résultats
    if ($resultat->rowCount() > 0) {
        // Création du fichier CSV et écriture des données
        $fichier_csv = fopen('whitelist-plaque.csv', 'w');

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

        echo "Exportation de la table au format CSV réussie.";
    } else {
        echo "Aucun résultat trouvé dans la table.";
    }

    // Fermeture de la connexion à la base de données
    $conn = null;
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}
?>
