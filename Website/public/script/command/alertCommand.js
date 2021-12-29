const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
})


function warningDelete(){
    swalWithBootstrapButtons.fire({
        title: 'Supprilmer la commande',
        text: "Etes vous sur de vouloir supprimer la commande",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Suopprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            swalWithBootstrapButtons.fire(
                'Supprimé!',
                'La commande a été supprimé',
                'success'
            )
        }
    })
}