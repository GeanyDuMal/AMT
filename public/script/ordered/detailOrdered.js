function confirmRefund() {
    return (confirm("Voulez vous vraiment rembourser cette commande ? " +
        "Tout les achats liés à cette commande seront remboursés et les produits seront restockés"));
}

function confirmCancel() {
    return (confirm("Voulez vous vraiment annuler cette commande ? " +
        "Le prix de la commande ne sera pas remboursé et les produits ne seront pas restockés"));
}