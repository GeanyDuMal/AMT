var types=document.querySelector("#clientType");
window.onload = function() {
    if(document.querySelector('#clientType').value!=="Membre"){
        document.querySelector("#roles").style.visibility="hidden";
    }

}
types.onchange=function (){
    var selectedOption = this[this.selectedIndex];
    var selectedText = selectedOption.text;
    var roles=document.querySelector("#roles");
    roles.style.visibility =selectedText==="Etudiant"? "hidden":"visible";
}
var form = document.querySelector("#form_Client");
function verifier() {
    var message = "";
    var regexCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
    var valueName = form.name.value;
    var valueFirstName = form.fname.value;
    var valueLogin = form.login.value;
    var valuePassword = form.pwd.value;
    var valueConfirm = form.confPwd.value;
    var valueBalance = form.balance.value;
    if (!valueName.trim()) {
        message += "Nom non renseigné,";
    } else if (valueName.length < 3) {
        message += "Nom trop court, ";
    }

    if (!valueFirstName.trim()) {
        message += "Prenom non renseigné, ";
    } else if (valueFirstName.length < 3) {
        message += "Prenom trop court, ";
    }
    if (!valueLogin.trim()) {
        message += "Login non renseigné, ";
    } else if (valueLogin.length < 5) {
        message += "Login trop court, ";
    }

    if (!valuePassword.trim()) {
        message += "Mot de Passe non renseigné ";
    } else if (valuePassword.length < 5) {
        message += "Mot de Passe trop court ";
    } else if (regexCharacter.test(valuePassword) === false) {
        message += "Le mots de passe ne contient pas de caractere spécial "
    }
    else if (valuePassword !== valueConfirm) {
        message += "Les mots de passe ne correspondent pas "
    }

    balance = parseFloat(valueBalance);
    if (balance!==0 && !balance && valueBalance !== "") {
        message += "Balance doit etre un nombre. ";
    } else if (balance < 0) {
        message += "Le balance doit etre >=0. ";
    }
    if (message !== "") {
        Swal.fire({
            title: 'Incomplet !',
            text: message,
            icon: 'error',
            confirmButtonText: 'Completer'
        })
        return false;
    } else {
        return true;
    }
}
