/**
 * delete a product using jquery-ajax
 */
$("#products").on("click", "#toDelete", function() {
    var tr= $(this).closest("#product_block");
    if (confirm('Voulez-vous supprimer ce produit ? ')) {
        const id=$(this).data("id");
        fetch('/product/delete/'+id, { method: 'DELETE'} )
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