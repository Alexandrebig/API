// Récupérer l'URL actuelle
var url = window.location.href;

// Supprimer les paramètres de requête
var cleanUrl = url.split('?')[0];

// Rediriger vers l'URL propre
window.location.replace(cleanUrl);

window.location.href = "http://localhost:63342/API/connexion_administrateur/";
