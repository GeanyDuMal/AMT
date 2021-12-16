function verifyInput() {
    var form = document.querySelector("#form_signin");
    var message = "";
    var regexCharacter = "#$%^&*()+=-[]';,./{}|:<>?~";

    var valueName = form.name.value;
    var valueFirstName = form.firstName.value;
    var valueLogin = form.login.value;
    var valuePassword = form.password.value;
    var valueConfirm = form.confirmPassword.value;

    if (valueName == ""){
        message += "Nom non renseigné\n";
    }else if(valueName.length() < 3){
        message += "Nom trop court\n";
    }

    if (valueFirstName == ""){
        message += "Prenom non renseigné\n";
    }else if(valueFirstName.length() < 3){
        message += "Prenom trop court\n";
    }

    if (valueLogin == ""){
        message += "Login non renseigné\n";
    }else if(valueLogin.length() < 5){
        message += "Login trop court\n";
    }

    if (valuePassword == ""){
        message += "Mot de Passe non renseigné\n";
    }else if(valuePassword.length() < 5){
        message += "Mot de Passe trop court\n";
    }else if (regexCharacter.match(valuePassword) == null) {
        message += "Le mots de passe ne contient pas de caractere spécial\n"
    }
    else if (valuePassword != valueConfirm) {
        message += "Les mots de passe ne correspondent pas\n"
    }

/** DEBUG */
   
    console.log(valueName);
    console.log(valueFirstName);
    console.log(valueLogin);
    console.log(valuePassword);
    console.log(valueConfirm);
    console.log(message);

/**END DEBUG */

    if (message != "") {
        alert(message);
        return false;
    }else{
        return true;
    }
    
}