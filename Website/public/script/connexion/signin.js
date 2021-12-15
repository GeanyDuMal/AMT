function verifyInput() {
    var form = document.querySelector("#form_signin");
    var message = "";

    var valueName = form.name.value;
    var valueFirstName = form.firstName.value;
    var valueLogin = form.login.value;
    var valuePassword = form.password.value;
    var valueConfirm = form.confirmPassword.value;

    if (valueName == ""){
        message += "Nom non renseigné\n";
    }
    else if(valueName.length() < 3){
        message += "Nom trop court\n";
    }

    if (condition) {
        
    }

    if (condition) {
        
    }

    if (condition) {
        
    }

    if (condition) {
        
    }


    return false;
}