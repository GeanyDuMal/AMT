/**
 * Delete a Blog with AJAX
 */
$(".list-card").on("click", "#toDelete", function(){

    var nodeBlog = this.parentNode.parentNode.parentNode.parentNode;
    if (confirm("Voulez vous vraiment supprimer ce blog ?")){
        console.log(nodeBlog.parentNode.querySelector("#idBlog").value)
        var id = nodeBlog.querySelector("#idBlog").value;
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