/**
 * delete a product using jquery-ajax
 */
$(".product_block").on("click", "#toDelete", function() {

    var tr = $(this).closest(".product_block");
    if (confirm('Voulez-vous supprimer ce produit ? ')) {
        const id=$(this).data("id");

        console.log(id);

        fetch('/product/delete/'+ id, { method: 'DELETE'} )

            .then(function (resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Nice',
                    text: 'Suppression avec succès',
                })
                tr.remove();
            });
    }
});