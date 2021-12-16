function verifyInput() {

    var form = document.querySelector("#form_signin");
    var message = "";
    var regexCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;


    var valueName = form.name.value;
    var valueFirstName = form.firstName.value;
    var valueLogin = form.login.value;
    var valuePassword = form.password.value;
    var valueConfirm = form.confirmPassword.value;

    if (valueName == ""){
        message += "Nom non renseigné ";
    }else if(valueName.length < 3){
        message += "Nom trop court ";
    }

    if (valueFirstName == ""){
        message += "Prenom non renseigné ";
    }else if(valueFirstName.length < 3){
        message += "Prenom trop court ";
    }

    if (valueLogin == ""){
        message += "Login non renseigné ";
    }else if(valueLogin.length < 5){
        message += "Login trop court ";
    }

    if (valuePassword == ""){
        message += "Mot de Passe non renseigné ";
    }else if(valuePassword.length < 5){
        message += "Mot de Passe trop court ";
    }else if (regexCharacter.test(valuePassword) == false) {
        message += "Le mots de passe ne contient pas de caractere spécial "
    }
    else if (valuePassword != valueConfirm) {
        message += "Les mots de passe ne correspondent pas "
    }

    if (message != "") {
        Swal.fire({
            title: 'Incomplet !',
            text: message,
            icon: 'error',
            confirmButtonText: 'Completer'
          })
        return false;
    }else{
        return true;
    }
}