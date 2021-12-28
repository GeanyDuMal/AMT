var form=document.querySelector("#product_form")

function verifyProduct(){
    let message = "";
    const nameValue = form.productName.value;
    const stockValue = form.productStock.value;

    if (!nameValue.trim()) {
        message += "Nom du produit non renseigné\n";
    }
    let stock =parseInt(stockValue);
    if(stock!==0 && !stock && stockValue !== "")
        message += "Stock doit être un nombre. ";
    else if (stock < 0)
        message += "Le stock doit être positif. ";
    if (message !== "") {
        Swal.fire({
            title: 'Incomplet !',
            html: '<pre>' + message + '</pre>',
            icon: 'error',
            confirmButtonText: 'Completer'
        })
        return false;
    } else {
        return true;
    }
}