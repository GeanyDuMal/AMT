var clientType = document.querySelector("#clientType");
var assosRoles = document.querySelector("#roles");
var form = document.querySelector("#form_Client");

/**
 * hide the combobox of association roles if it's not a member
 */
window.onload = function() {
    if(clientType.value !== "Association"){
        assosRoles.style.visibility = "hidden";
    }
}

/**
 * same thing as above but when the admin change it's value
 */
clientType.onchange = function (){
    var selectedOption = this[this.selectedIndex];
    var selectedText = selectedOption.text;
    assosRoles.style.visibility = (selectedText === "Etudiant" ? "hidden" : "visible");
}

/**
 * To verify if the data entered is valid or not
 * @returns {string}
 */
function communVerify(){
    let message = "";
    const valueName = form.name.value;
    const valueFirstName = form.firstName.value;
    const valueLogin = form.login.value;
    const valueBalance = form.balance.value;
    const valueFidelityPoint = form.fidelityPoint.value;

    if (!valueName.trim()) {
        message += "Nom non renseigné\n";
    } else if (valueName.length < 3) {
        message += "Nom trop court\n";
    }

    if (!valueFirstName.trim()) {
        message += "Prenom non renseigné\n";
    } else if (valueFirstName.length < 3) {
        message += "Prenom trop court\n";
    }
    if (!valueLogin.trim()) {
        message += "Login non renseigné\n";
    } else if (valueLogin.length < 5) {
        message += "Login trop court\n";
    }

    if(isNaN(valueBalance)){
        message += "Le solde doit être un nombre. ";
    }else{
        balance = parseFloat(valueBalance);

        if(valueBalance === "")
            message += "Le solde ne doit pas être vide.\n ";
        else if (balance < 0)
            message += "Le solde doit être positif.\n ";
    }


    if(isNaN(valueFidelityPoint)){
        message += "Le nombre de points de fidélité doit être un nombre. ";
    }else{
        fidelityPoint = parseFloat(valueFidelityPoint);

        if(valueFidelityPoint === "")
            message += "Le nombre de point de fidélité ne doit pas être vide.\n ";
        else if (fidelityPoint < 0)
            message += "Le nombre de point de fidélité doit être positif.\n ";
    }

  return message;
}

function verifyEdit(){
    var message = "";
    message = communVerify();
    if (message !== "") {
        Swal.fire({
            title: 'Incomplet !',
            html: '<pre>' + message + '</pre>',
            icon: 'error',
            confirmButtonText: 'Completer'
        })
        return false;
    } else {
        return true;
    }
}

function verifyAdd() {
    var message = "";
    message=communVerify();
    var regexCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
    var valuePassword = form.password.value;
    var valueConfirm = form.confirmPassword.value;

    if (!valuePassword.trim()) {
        message += "Mot de Passe non renseigné\n";
    } else if (valuePassword.length < 5) {
        message += "Mot de Passe trop court\n";
    } else if (regexCharacter.test(valuePassword) === false) {
        message += "Le mots de passe ne contient pas de caractere spécial\n";
    }
    else if (valuePassword !== valueConfirm) {
        message += "Les mots de passe ne correspondent pas\n";
    }
    if (message !== "") {
        Swal.fire({
            title: 'Incomplet !',
            html: '<pre>' + message + '</pre>',
            icon: 'error',
            confirmButtonText: 'Completer'
        })
        return false;
    } else {
        return true;
    }
}
