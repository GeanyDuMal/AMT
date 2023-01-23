let form = document.querySelector('#password_forgot_form')

function verifyInput() {
    let message = "<ul>";

    var valueName = form.clientName.value;
    var valueFirstName = form.clientFirstName.value;
    var valueLogin = form.clientLogin.value;

    form.clientName.style.borderColor = "red";
    if (valueName === ""){
        message += "<li>Nom non renseigné</li>";
    }else {
        form.clientName.style.borderColor = "black";
    }

    form.clientFirstName.style.borderColor = "red";
    if (valueFirstName === ""){
        message += "<li>Nom non renseigné</li>";
    }else {
        form.clientFirstName.style.borderColor = "black";
    }

    form.clientLogin.style.borderColor = "red";
    if (valueLogin === ""){
        message += "<li>Nom non renseigné</li>";
    }else {
        form.clientLogin.style.borderColor = "black";
    }

    if (message !== "<ul>") {
        Swal.fire({
            title: 'Incomplet !',
            html: "Merci de completer correctement tout les champs afin de vous connecter",
            icon: 'error',
            confirmButtonText: 'Completer'
        })
        return false;
    }else{
        return true;
    }
}