/**
 * Delete a Post with AJAX
 */
$(".card").on("click", "#toDelete", function(){
    var nodePost = this.closest(".card");

    if (confirm("Voulez vous vraiment supprimer ce post ?")){
        const id = nodePost.querySelector("#idPost").value;

        fetch("/post/delete/" + id, {method: 'DELETE'})
            .then(function (resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Suppresion',
                    text: 'Suppression du Post effectuée avec succès',
                })
                nodePost.remove();
            });
    }
});