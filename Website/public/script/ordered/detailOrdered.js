function confirmDelete() {
    let id;
    if (confirm("Voulez vous vraiment supprimer cette commande ?" +
        "Tout les achats lié a cette commandes seront supprimés"))
    {
        id = document.querySelector("#idCommande").innerHTML
        fetch('/ordered/menu/delete/' + id, {method: 'DELETE'}).then(r => {
            window.location.href = "/ordered/menu/Suppression%20Réussie"
        });
    }



}