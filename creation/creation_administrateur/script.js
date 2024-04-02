function submitForm(event) {
    event.preventDefault();

    let utilisateur = document.getElementById("utilisateur").value;
    let password = document.getElementById("password").value;
    let nom = document.getElementById("nom").value;
    let prenom = document.getElementById("prenom").value;
    let mail = document.getElementById("mail").value;

    let url = "creation_administrateur.php";
    let data = new FormData();
    data.append('utilisateur', utilisateur);
    data.append('password', password);
    data.append('nom', nom);
    data.append('prenom', prenom);
    data.append('mail', mail);

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

document.getElementById("loginForm").addEventListener("submit", submitForm);
