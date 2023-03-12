var form=document.querySelector("#form-blog")

function verifyPost(){
    let message = "";
    const titleValue = form.postTitle.value;
    const descriptionValue = form.postDescription.value;
    const imageLinkValue = form.imageLink.value;
    let imageLinkStateValue = ""
    if (form.imageLinkState) {
        imageLinkStateValue = form.imageLinkState.value;
    }

    if (!titleValue.trim()) {
        message += "Titre du post non renseigné\n";
    }

    if(!descriptionValue.trim()){
        message += "Description du post non renseignée\n ";
    }


    if (form.imageLinkState && imageLinkStateValue === "edit"){
        if (!imageLinkValue.trim()){
            message += "Lien non renseigné\n ";
        } else if (!imageLinkValue.startsWith('http')){
            message += "Lien ne correspondant pas à un lien classique \n(http / https)\n ";
        }
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

/**
 * Change the visibility of the area to set the imageLink
 */
form.imageLinkState.onchange = function (){
    var selectedOption = this[this.selectedIndex];
    form.querySelector("#imageLinkArea").style.visibility = (selectedOption.value === "edit" ? "visible" : "hidden");
}
