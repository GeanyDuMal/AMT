/**
 * Delete an Order with AJAX
 */
$("#order_table").on("click", "#toDelete", function(){
    var tr = this.parentNode.parentNode;
    if (confirm("Voulez vous vraiment supprimer cette commande ?" +
        "Tout les achats lié a cette commandes seront supprimés")){
        var id = tr.querySelector("#idCell").innerHTML;
        console.log(id);
        fetch('/command/menu/delete/' + id, {method: 'DELETE'})
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

/**
 * Delete an Order with AJAX
 */
$("#order_table").on("click", "#askToDelete", function(){
    var tr = this.parentNode.parentNode;
    if (confirm("Voulez vous vraiment supprimer cette commande ?" +
        "Tout les achats lié a cette commandes seront supprimés")){
        var id = tr.querySelector("#idCell").innerHTML;
        console.log(id);
        fetch('/command/menu/mailling/' + id)
            .then(function (resp) {
                Swal.fire({
                    icon: 'info',
                    title: 'Demande de suppresion',
                    text: 'La suppression de la commande ' + id + " a été envoyé au Président et Tresorier",
                })
            });
    }
});