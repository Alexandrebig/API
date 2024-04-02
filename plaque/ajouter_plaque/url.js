// Récupérer l'URL actuelle
var url = window.location.href;

// Vérifier si l'URL contient des paramètres de requête
if (url.indexOf('?') !== -1) {
    // Supprimer les paramètres de requête
    var cleanUrl = url.split('?')[0];

    // Rediriger vers l'URL propre
    window.location.replace(cleanUrl);
} else if (url.indexOf('index.html') !== -1) {
    // Supprimer "index.html" de l'URL
    var cleanUrl = url.replace(/\/index\.html$/, '/');

    // Rediriger vers l'URL propre
    window.location.replace(cleanUrl);
}
