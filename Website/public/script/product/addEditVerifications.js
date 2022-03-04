var form=document.querySelector("#product_form")

function verifyProduct(){
    let message = "";
    const nameValue = form.productName.value;
    const stockValue = form.productStock.value;
    const memberPrice = form.memberPrice.value;
    const studentPrice = form.studentPrice.value;

    if (!nameValue.trim()) {
        message += "Nom du produit non renseigné\n";
    }
    let stock = parseInt(stockValue);

    if(stockValue === "" || isNaN(stockValue))
        message += "Stock doit être un nombre.\n ";
    else if (stock < 0)
        message += "Le stock doit être positif.\n ";
    let memberPriceValue=parseFloat(memberPrice);
    let studentPriceValue=parseFloat(studentPrice);

    if(memberPrice === "")
        message += "Le prix des membres ne doit pas être vide.\n ";
    else if (memberPriceValue < 0)
        message += "Le prix des membres doit être positif.\n ";
    else if(isNaN(memberPriceValue)){
        message += "Le prix des membres doit être un nombre. ";    }

    if(studentPrice === "")
        message += "Le prix des étudiants ne doit pas être vide.\n ";
    else if (studentPriceValue < 0)
        message += "Le prix des étudiants doit être positif.\n ";
    else if(isNaN(studentPriceValue)){
        message += "Le prix des étudiants doit être un nombre. ";    }

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