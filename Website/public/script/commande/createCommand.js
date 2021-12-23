var blocksProduct = document.querySelectorAll(".block_product");

blocksProduct.forEach(block => {
      var btnUp = block.querySelector("div .block_button .btn_plus");
      var btnDown = block.querySelector("div .block_button .btn_minus");
      var compteur = block.querySelector("div .input_quantity");
      var quantityAvailable = parseInt(block.querySelector(".flex_vertical .quantity_block .quantity_value").innerHTML);

      btnUp.addEventListener("click", () => {
            if (quantityAvailable > parseInt(compteur.value)) {
                  compteur.value = parseInt(compteur.value)+1;
            }            
      })
      btnDown.addEventListener("click", () => {
            if(parseInt(compteur.value) > 0){
                  compteur.value = parseInt(compteur.value)-1;
            }
      })

      compteur.addEventListener("change", () => {
            if(parseInt(compteur.value) < 0){
                  compteur.value = 0;
            }else if(parseInt(compteur.value) > quantityAvailable){
                  compteur.value = quantityAvailable;
            }
      })
});

/**
 * Permet de bloquer les compteurs
 * avec 0 pour minimum
 * avec la valeur dispo max a l'initialisation de la page
 *    prevent si un utilisateur modifie cette valeur manuellement
 */