/**
 * Permet de bloquer les compteurs
 * avec 0 pour minimum
 * avec la valeur dispo max a l'initialisation de la page
 *    prevent si un utilisateur modifie cette valeur manuellement
 */
var blocksProduct = document.querySelectorAll(".card");

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

            if(valueCompteur > 0){
                  inputQuantityOrdered.value  = valueCompteur - 1;
            }
      })

      inputQuantityOrdered.addEventListener("change", () => {
            let valueCompteur = parseInt(inputQuantityOrdered.value)

            if(parseInt(valueCompteur) < 0){
                  inputQuantityOrdered.value  = 0;
            }else if(valueCompteur > quantityAvailable){
                  inputQuantityOrdered.value = quantityAvailable;
            }
      })
});

/**
 * Permet de verifier lorsqu'on clique sur le bouton "valider la commande"
 * qu'il y a au moins un input superieur a 0
 */
function checkQuantityInput() {
      var notEmpty = false;
      var allInput = document.querySelectorAll(".quantity-ordered");
      allInput.forEach(input =>{
            if (!notEmpty){
                  if (input.value > 0){
                        notEmpty = true;
                  }
            }
      })

      if (!notEmpty){
            Swal.fire({
                  icon: 'error',
                  title: 'Aucun produit',
                  text: 'Merci de sélectionner un produit pour valider une commande',
            })
      }
      return notEmpty;
}