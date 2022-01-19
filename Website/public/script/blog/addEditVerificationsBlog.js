var form=document.querySelector("#blog_form")

function verifyPost(){
    let message = "";
    const titleValue = form.postTitle.value;
    const descriptionValue = form.postDescription.value;
    if (!titleValue.trim()) {
        message += "Titre du post non renseigné\n";
    }
    if(!descriptionValue.trim())
        message += "Description du post non renseignée\n ";

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