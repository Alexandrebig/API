function submitForm(event) {
    event.preventDefault();

    let utilisateur = document.getElementById("utilisateur").value;
    let password = document.getElementById("password").value;
    let url = "connexion_utilisateur.php?utilisateur=" + encodeURIComponent(utilisateur) + "&password=" + encodeURIComponent(password);

    let xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.setRequestHeader("Content-Type", "application/json");
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
    xhr.send();
}

document.getElementById("loginForm").addEventListener("submit", submitForm);
