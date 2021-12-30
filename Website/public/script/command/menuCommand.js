/**
 * To apply the plugin DataTables (he makes the table so cool :3 )
 * pagination
 * search bar
 * sorting per column
 */
$(document).ready( function () {
    $('#order_table').DataTable(
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            }
        }
    );
} );

/**
 * Permet de gerer les alertes lorsque l'on clique sur le bouton supprimer
 * A mettre ensuite d'un un ajax.JS
 */
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
})


function warningDelete(){
    swalWithBootstrapButtons.fire({
        title: 'Supprimer la commande',
        text: "Etes vous sur de vouloir supprimer la commande",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Supprimer',
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
    return result;
}