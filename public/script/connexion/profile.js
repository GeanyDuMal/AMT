var form = document.querySelector("#form_edit_password");

function verifyInputEditPassword() {
    let message = "<ul>";
    const regexCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;


    var valueOldPassword = form.oldPassword.value;
    var valueNewPassword = form.newPassword.value;
    var valueConfirm = form.confirmPassword.value;

    
    form.oldPassword.style.borderColor = "red";
    if (valueOldPassword === ""){
        message += "<li>Ancien mot de passe non renseigné</li>";
    }else if(valueOldPassword.length < 5){
        message += "<li>Ancien mot de passe trop court</li>";
    }else if (regexCharacter.test(valueOldPassword) === false) {
        message += "<li>Le mots de passe ne contient pas de caractere spécial</li>"
    }else{
      form.oldPassword.style.borderColor = "black";
    }

    form.newPassword.style.borderColor = "red";
    if (valueNewPassword === ""){
        message += "<li>Nouveau mot de passe non renseigné</li>";
    }else if(valueNewPassword.length < 5){
        message += "<li>Nouveau mot de passe trop court</li>";
    }else if (regexCharacter.test(valueNewPassword) === false) {
        message += "<li>Le mots de passe ne contient pas de caractere spécial</li>"
    }
    else if (valueNewPassword !== valueConfirm) {
        message += "<li>Les mots de passe ne correspondent pas</li>"
        form.confirmPassword.style.borderColor = "red";
    }else{
        form.newPassword.style.borderColor = "black";
        form.confirmPassword.style.borderColor = "black";
    }

    form.newPassword.style.borderColor = "red";
    form.oldPassword.style.borderColor = "red";
    if (valueOldPassword === valueNewPassword){
        message += "<li>Le nouveau mot de passe n'est pas différent de l'ancien</li>"
    }

    message += "</ul>"

    var emptyMessage = message === "<ul></ul>";

    if (!emptyMessage) {
        Swal.fire({
            title: 'Incomplet !',
            html: message,
            icon: 'error',
            confirmButtonText: 'Completer'
          })
    }
    return emptyMessage;
}

function openCloseEditForm() {
    form.classList.toggle("open_menu");
}

function warningSuppressionAccount(){
    var message = "Vous êtes sur le point de supprimer votre compte. \n" +
        "Votre action est irreversible et supprimera toutes information liées à votre compte !"

    var messageBis = "Merci de confirmer une seconde fois la supression de votre compte."

    return (confirm(message) && confirm(messageBis));
}