/**
 * Delete an Order with AJAX
 */
$("#ordered_table").on("click", "#toDelete", function(){
    var tr = this.parentNode.parentNode;
    if (confirm("Voulez vous vraiment supprimer cette commande ?" +
        "Tout les achats lié a cette commandes seront supprimés")){
        var id = tr.querySelector("#idCell").innerHTML;
        console.log(id);
        fetch('/ordered/menu/delete/' + id, {method: 'DELETE'})
            .then(function (resp) {
                console.log(resp)
                Swal.fire({
                    icon: 'success',
                    title: 'Suppresion',
                    text: 'Suppression de la commande effectuée avec succès',
                })
                tr.remove();
            });
    }
});


