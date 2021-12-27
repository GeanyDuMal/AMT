function deleteClient(){
    $(this).closest("tr").remove();
    const btn=tr.querySelector("#toDelete");

}
$("#client_table").on("click", "#toDelete", function() {
   var tr= $(this).closest("tr");
    if (confirm('Voulez-vous supprimer l\'utilisateur ? ')) {
        const id=$(this).data("id");
        fetch('/admin/client/delete/'+id, {method: 'DELETE'})
            .then(function (resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Noice',
                    text: 'Suppression avec succée',
                })
                tr.remove();
            });
    }
});
$(document).ready( function () {
    $('#client_table').DataTable(
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            }
        }
    );
} );