/**
 * Delete a Product with AJAX
 */
$(".card").on("click", "#toDelete", function() {
    var nodeProduct = this.closest(".card");

    if (confirm('Voulez-vous supprimer ce produit ? ')) {
        const id = nodeProduct.querySelector("#idProduct").value;

        fetch('/product/delete/'+ id, { method: 'DELETE'} )
            .then(function (resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Supprimé',
                    text: 'Suppression effectué avec succès',
                })
                nodeProduct.remove();
            });
    }
});