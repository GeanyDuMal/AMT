/**
 * Delete a Blog with AJAX
 */
$(".card").on("click", "#toDelete", function(){
    var nodeBlog = this.closest(".card");

    if (confirm("Voulez vous vraiment supprimer ce blog ?")){
        const id = nodeBlog.querySelector("#idBlog").value;

        fetch("/blog/delete/" + id, {method: 'DELETE'})
            .then(function (resp) {
                Swal.fire({
                    icon: 'success',
                    title: 'Suppresion',
                    text: 'Suppression du Post effectuée avec succès',
                })
                nodeBlog.remove();
            });
    }
});