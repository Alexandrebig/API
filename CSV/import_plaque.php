<?php
// Vérifier si un fichier a été envoyé
if(isset($_FILES['plaque_file'])) {
    // Emplacement où le fichier sera stocké temporairement sur le serveur
    $file_tmp = $_FILES['plaque_file']['tmp_name'];

    // Vérifier si le fichier est un fichier CSV
    $file_ext = strtolower(end(explode('.', $_FILES['plaque_file']['name'])));
    if($file_ext === 'csv') {
        // Déplacer le fichier vers un emplacement permanent
        move_uploaded_file($file_tmp, 'uploads/' . $_FILES['plaque_file']['name']);

        // Traiter le fichier CSV pour extraire les données et les insérer dans la base de données
        $file = fopen('uploads/' . $_FILES['plaque_file']['name'], 'r');
        while(($data = fgetcsv($file)) !== false) {
            // Vous pouvez traiter chaque ligne ici et insérer les données dans la base de données
            // Exemple d'insertion de données dans une base de données MySQL
            // $data contient les valeurs de chaque colonne de la ligne du fichier CSV
            // Assurez-vous de filtrer et de valider les données avant de les insérer dans la base de données
            // $data[0] représente la première colonne, $data[1] la deuxième, etc.
            // Exemple : INSERT INTO votre_table (colonne1, colonne2) VALUES ('$data[0]', '$data[1]');
        }
        fclose($file);

        // Indiquer que l'importation s'est terminée avec succès
        echo "Importation des plaques autorisées réussie.";
    } else {
        // Indiquer que le fichier n'est pas un fichier CSV
        echo "Le fichier doit être un fichier CSV.";
    }
} else {
    // Indiquer que aucun fichier n'a été envoyé
    echo "Aucun fichier n'a été envoyé.";
}
?>
