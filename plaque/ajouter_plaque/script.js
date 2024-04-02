function submitForm(event) {
    event.preventDefault();

    let numeroPlaque = document.getElementById("numeroPlaque").value;
    let utilisateur = document.getElementById("utilisateur").value;

    let url = "ajouter_plaque.php"; // Modifier le nom du fichier PHP si nécessaire
    let data = new FormData();
    data.append('numeroPlaque', numeroPlaque);
    data.append('utilisateur', utilisateur);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            let responseDiv = document.getElementById("response");
            responseDiv.style.display = "block";
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                responseDiv.className = "success";
            } else {
                responseDiv.className = "error";
            }
            responseDiv.innerHTML = response.message;
        }
    };
    xhr.send(data);
}

document.getElementById("plaqueForm").addEventListener("submit", submitForm);
