/**
 * delete a client/member using jquery-ajax
 */
$("#client_table").on("click", "#toDelete", function() {
   var tr= $(this).closest("tr");
    if (confirm('Voulez-vous supprimer l\'utilisateur ? ')) {
        const id=$(this).data("id");
        fetch('/admin/client/delete/'+id, {method: 'DELETE'})
            .then(function (resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Nice',
                    text: 'Suppression avec succèes',
                })
                tr.remove();
            });
    }
});

/**
 * To apply the plugin DataTables (he makes the table so cool :3 )
 * pagination
 * search bar
 * sorting per column
 */
$(document).ready( function () {
    $('#client_table').DataTable(
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
            responsive: true
        }
    );
} );