function verifyInputEditPassword() {
    var form = document.querySelector("#form_edit_password");

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