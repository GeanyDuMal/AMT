function deleteClient(){
    const tr=document.querySelector("#data");
    const btn=tr.querySelector("#toDelete");
    if (confirm('Voulez-vous supprimer l\'utilisateur ? ')) {
        const id=btn.getAttribute("data-id");
        alert(id);
            fetch('/admin/client/delete/'+id, {method: 'DELETE'})
                .then(function (resp) {
                    tr.parentElement.removeChild(tr);
                });

    }
}