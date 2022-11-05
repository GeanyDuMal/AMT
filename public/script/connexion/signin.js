function verifyInputSignin() {
    var form = document.querySelector("#form-signin");

    let message = "<ul>";
    const regexCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;


    var valueName = form.name.value;
    var valueFirstName = form.firstName.value;
    var valueLogin = form.login.value;
    var valuePassword = form.password.value;
    var valueConfirm = form.confirmPassword.value;

    form.name.style.borderColor = "red";
    if (valueName === ""){
        message += "<li>Nom non renseigné</li>";
    }else if(valueName.length < 3){
        message += "<li>Nom trop court</li>";
    }else{
        form.name.style.borderColor = "black";
    }

    form.firstName.style.borderColor = "red";
    if (valueFirstName === ""){
        message += "<li>Prenom non renseigné</li>";
    }else if(valueFirstName.length < 3){
        message += "<li>Prenom trop court</li>";
    }else{
        form.firstName.style.borderColor = "black";
    }

    form.login.style.borderColor = "red";
    if (valueLogin === ""){
        message += "<li>Login non renseigné</li>";
    }else if(valueLogin.length < 5){
        message += "<li>Veuillez saisir un login valide</li>";
    }else{
        form.login.style.borderColor = "black";
    }

    form.password.style.borderColor = "red";
    if (valuePassword === ""){
        message += "<li>Mot de passe non renseigné</li>";
    }else if(valuePassword.length < 5){
        message += "<li>Mot de passe trop court</li>";
    }else if (regexCharacter.test(valuePassword) === false) {
        message += "<li>Le mots de passe ne contient pas de caractere spécial</li>"
    }
    else if (valuePassword !== valueConfirm) {
        message += "<li>Les mots de passes ne correspondent pas</li>"
        form.confirmPassword.style.borderColor = "red";
    }else{
        form.password.style.borderColor = "black";
        form.confirmPassword.style.borderColor = "black";
    }

    message += "</ul>"

    if (message !== "<ul></ul>") {
        Swal.fire({
            title: 'Incomplet !',
            html: message,
            icon: 'error',
            confirmButtonText: 'Completer'
          })
        return false;
    }else{
        return true;
    }
}