function confirmDelete() {
    return (confirm("Voulez vous vraiment supprimer cette commande ? " +
        "Tout les achats liés à cette commande seront supprimés"));
}

function confirmCancel() {
    return (confirm("Voulez vous vraiment annuler cette commande ? " +
        "Tout les achats liés à cette commande seront remboursés et les produits seront restockés"));
}