var form=document.querySelector("#form-post")

function verifyPost(){
    let message = "";
    const titleValue = form.postTitle.value;
    const descriptionValue = form.postDescription.value;
    let pictureStateValue = ""
    if (form.pictureState) {
        pictureStateValue = form.pictureState.value;
    }

    if (!titleValue.trim()) {
        message += "Titre du post non renseigné\n";
    }

    if(!descriptionValue.trim()){
        message += "Description du post non renseignée\n ";
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
form.pictureState.onchange = function (){
    var selectedOption = this[this.selectedIndex];
    form.querySelector("#imageArea").style.visibility = (selectedOption.value === "edit" ? "visible" : "hidden");
}
