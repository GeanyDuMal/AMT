var form = document.querySelector(".block_button_payment");

if (form.querySelectorAll("button").length === 1){
    var button =  form.querySelector("button");
    button.style.width = "100%";
    button.style.padding = "10px";
}

function preventPayment() {
    return confirm("Attention, toute commande effectuée ne pourras etre remboursée, etes vous sûr des elements que vous " +
        "avez saisi ?")
}