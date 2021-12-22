var form = document.querySelector("#form_signin");

// Style
var childs = form.querySelectorAll("div");
childs.forEach(child => {
    child.style.marginTop = "10px";
    child.classList.add("flex_vertical")
});


function verifyInputSignin() {

    let message = "";
    const regexCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;


    var valueName = form.name.value;
    var valueFirstName = form.firstName.value;
    var valueLogin = form.login.value;
    var valuePassword = form.password.value;
    var valueConfirm = form.confirmPassword.value;

    form.name.style.borderColor = "red";
    if (valueName === ""){
        message += "Nom non renseigné, ";
    }else if(valueName.length < 3){
        message += "Nom trop court, ";
    }else{
        form.name.style.borderColor = "black";
    }

    form.firstName.style.borderColor = "red";
    if (valueFirstName === ""){
        message += "Prenom non renseigné, ";
    }else if(valueFirstName.length < 3){
        message += "Prenom trop court, ";
    }else{
        form.firstName.style.borderColor = "black";
    }

    form.login.style.borderColor = "red";
    if (valueLogin === ""){
        message += "Login non renseigné, ";
    }else if(valueLogin.length < 5){
        message += "Login trop court, ";
    }else{
        form.login.style.borderColor = "black";
    }

    form.password.style.borderColor = "red";
    if (valuePassword === ""){
        message += "Mot de Passe non renseigné ";
    }else if(valuePassword.length < 5){
        message += "Mot de Passe trop court ";
    }else if (regexCharacter.test(valuePassword) === false) {
        message += "Le mots de passe ne contient pas de caractere spécial "
    }
    else if (valuePassword !== valueConfirm) {
        message += "Les mots de passe ne correspondent pas "
        form.confirmPassword.style.borderColor = "red";
    }else{
        form.password.style.borderColor = "black";
        form.confirmPassword.style.borderColor = "black";
    }

    if (message !== "") {
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