function deleteClient(){
    const tr=document.querySelector("#data");
    const btn=tr.querySelector("#toDelete");
    if (confirm('Voulez-vous supprimer l\'utilisateur ? ')) {
        const id=btn.getAttribute("data-id");
            fetch('/admin/client/delete/'+id, {method: 'DELETE'})
                .then(function (resp) {
                    tr.parentElement.removeChild(tr);
                });

    }
}
$(document).ready( function () {
    $('#client_table').DataTable(
        {
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            }
        }
    );
} );