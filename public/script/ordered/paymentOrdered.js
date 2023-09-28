function preventPayment() {
    return confirm("Attention, toute commande effectuée ne pourras etre remboursée, etes vous sûr des elements que vous avez saisi ?")
}

function disableButtonPaymentOrdered() {
    let buttons = document.querySelectorAll('.btn')
    let divOrderedPending = document.querySelector('#pendingOrdered')
    buttons.forEach(button => {
        button.style.display = "none";
    })
    divOrderedPending.style.display = "block";
}