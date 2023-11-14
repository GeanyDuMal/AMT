/**
 * Permet de bloquer les compteurs
 * avec 0 pour minimum
 * avec la valeur dispo max a l'initialisation de la page
 *    prevent si un utilisateur modifie cette valeur manuellement
 */
let blocksProduct = document.querySelectorAll(".card");
let inputClientSuggest = document.querySelector("#input-client-suggest")
let listSuggest = document.querySelector("#persons-corresponding-list");

blocksProduct.forEach(block => {
    var btnUp = block.querySelector(".btn-plus");
    var btnDown = block.querySelector(".btn-minus");
    var inputQuantityOrdered = block.querySelector(".quantity-ordered");
    var quantityAvailable = parseInt(block.querySelector(".quantity-product-available").innerHTML);

    btnUp.addEventListener("click", () => {
        let valueCompteur = parseInt(inputQuantityOrdered.value)

        if (quantityAvailable > valueCompteur) {
            inputQuantityOrdered.value = valueCompteur + 1;
        }
    })

    btnDown.addEventListener("click", () => {
        let valueCompteur = parseInt(inputQuantityOrdered.value)

        if (valueCompteur > 0) {
            inputQuantityOrdered.value = valueCompteur - 1;
        }
    })

    inputQuantityOrdered.addEventListener("change", () => {
        let valueCompteur = parseInt(inputQuantityOrdered.value)

        if (parseInt(valueCompteur) < 0) {
            inputQuantityOrdered.value = 0;
        } else if (valueCompteur > quantityAvailable) {
            inputQuantityOrdered.value = quantityAvailable;
        }
    })
});

inputClientSuggest.addEventListener("input", () => {
    setIdClient("null")
    searchClient(inputClientSuggest.value);
})
inputClientSuggest.addEventListener("focusout", () => {
    //searchClient("");
})

/**
 * Permet de verifier lorsqu'on clique sur le bouton "valider la commande"
 * qu'il y a au moins un input superieur a 0
 */
function checkQuantityInput() {
    var notEmpty = false;
    var allInput = document.querySelectorAll(".quantity-ordered");
    allInput.forEach(input => {
        if (!notEmpty) {
            if (input.value > 0) {
                notEmpty = true;
            }
        }
    })

    if (!notEmpty) {
        Swal.fire({
            icon: 'error',
            title: 'Aucun produit',
            text: 'Merci de sélectionner un produit pour valider une commande',
        });
    }
    return notEmpty;
}

/**
 * @param searchString String qui va etre recherché
 */
function searchClient(searchString) {
    let persons = [];
    listSuggest.innerHTML = "";

    if(searchString.length >= 3) {
        fetch('/client/ajax/searchClient?researchString=' + searchString, {method: 'GET'})
            .then(response => {
                response.json().then(values => {
                    values.forEach(person => {
                        if (persons.length < 5) {
                            persons.push(person);
                        }
                    })
                }).then(() => {
                    listSuggest.innerHTML = "";

                    persons.forEach(person => {
                        listSuggest.innerHTML += "<li class='client-suggestion' onclick='selectClient(this)' clientName=\"" + person.name + "\" clientId=\"" + person.id + "\">"+ person.name +"</li>";
                    })
                }).finally(() => {
                    verifyDisplayList(listSuggest)
                })
            })
    } else {
        verifyDisplayList(listSuggest)
    }

}

function selectClient(source) {
    let sourceValues = source.attributes

    inputClientSuggest.value = sourceValues.clientName.value;
    setIdClient(sourceValues.clientId.value)

    searchClient("");
}

function setIdClient(id) {
    let inputClientId = document.querySelector('#input-client-id');
    inputClientId.value = id;
}

function verifyDisplayList(list) {
    console.log(list.childElementCount)
    list.hidden = list.childElementCount === 0;

    console.log(list)
}