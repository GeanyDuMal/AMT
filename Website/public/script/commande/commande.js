var blocksProduct = document.querySelectorAll(".product_block");

blocksProduct.forEach(block => {
      var btnUp = block.querySelector(".btn_up")
      var btnDown = block.querySelector(".btn_down")
      var compteur = block.querySelector(".compteur_produit")
      
      btnUp.addEventListener("click", () => {
            compteur.value = parseInt(compteur.value)+1
      })
      btnDown.addEventListener("click", () => {
            if(parseInt(compteur.value) >0){
                  compteur.value = parseInt(compteur.value)-1
            }
      })

      compteur.addEventListener("change", () => {
            if(parseInt(compteur.value) <0){
                  compteur.value = 0
            }
      })
});