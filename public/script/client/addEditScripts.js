let clientType = document.querySelector("#clientType");
let assosRoles = document.querySelector("#roles");
let form = document.querySelector("#form-client");

/**
 * hide the combobox of association roles if it's not a member
 */
window.onload = function () {
    if (clientType.value !== "Association") {
        assosRoles.style.visibility = "hidden";
    }
}

/**
 * same thing as above but when the admin change it's value
 */
clientType.onchange = function () {
    let selectedOption = this[this.selectedIndex];
    let selectedText = selectedOption.text;
    assosRoles.style.visibility = (selectedText === "Association" ? "visible" : "hidden");
}

/**
 * To verify if the data entered is valid or not
 * @returns {string}
 */
function verifyFields() {
    const valueName = form.name.value;
    const valueFirstName = form.firstName.value;
    const valueLogin = form.login.value;
    const valueBalance = form.balance.value;
    let valueFidelityPoint = null
    let message = "";

    if (form.fidelityPoint) {
        valueFidelityPoint = form.fidelityPoint.value;
    }

    if (!valueName.trim()) {
        message += "Nom non renseigné\n";
    } else if (valueName.length < 3) {
        message += "Nom trop court\n";
    }

    if (!valueFirstName.trim()) {
        message += "Prénom non renseigné\n";
    } else if (valueFirstName.length < 3) {
        message += "Prénom trop court\n";
    }
    if (!valueLogin.trim()) {
        message += "Login non renseigné\n";
    } else if (valueLogin.length < 5) {
        message += "Login trop court\n";
    }

    if (isNaN(valueBalance)) {
        message += "Le solde doit être un nombre. ";
    } else {
        let balance = parseFloat(valueBalance);

        if (valueBalance === "")
            message += "Le solde ne doit pas être vide.\n ";
        else if (balance < 0)
            message += "Le solde doit être positif.\n ";
    }


    if (valueFidelityPoint) {
        if (isNaN(valueFidelityPoint)) {
            message += "Les points de fidélités doivent être un nombre. ";
        } else {
            let fidelityPoint = parseFloat(valueFidelityPoint);

            if (valueFidelityPoint === "")
                message += "Les points de fidélités ne doivent pas être vide.\n ";
            else if (fidelityPoint < 0)
                message += "Les points de fidélités doivent être positif.\n ";
        }
    }

    return message;
}

function verifyEdit() {
    let message = "";
    message = verifyFields();

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
    let message = "";
    message = verifyFields();

    let regexCharacter = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
    let valuePassword = form.password.value;
    let valueConfirm = form.confirmPassword.value;

    if (!valuePassword.trim()) {
        message += "Mot de Passe non renseigné\n";
    } else if (valuePassword.length < 5) {
        message += "Mot de Passe trop court\n";
    } else if (regexCharacter.test(valuePassword) === false) {
        message += "Le mots de passe ne contient pas de caractère spécial\n";
    } else if (valuePassword !== valueConfirm) {
        message += "Les mots de passes ne correspondent pas\n";
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