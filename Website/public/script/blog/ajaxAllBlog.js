/**
 * Delete a Blog with AJAX
 */
$(".row").on("click", "#toDelete", function(){
    var nodeBlog = this.parentNode.parentNode.parentNode;
    if (confirm("Voulez vous vraiment supprimer ce blog ?")){
        console.log(nodeBlog);
        var id = this.parentNode.querySelector("#idBlog").innerHTML;
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