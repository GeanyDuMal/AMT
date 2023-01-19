const prevButton = document.getElementById("slide-arrow-prev");
const nextButton = document.getElementById("slide-arrow-next");

const slides = document.querySelectorAll(".slide");
const slidesQuantity = slides.length;

function switchCarouselItem() {
    let active = document.querySelector(".slide-active");
    let indexActive;

    /**
     * Deprecated
     * Fonctionnel mais a remplacer une fois une solution trouvé
     */
    let sourceButton = event.target;

    for (let i = 0; i < slidesQuantity; i++) {
        if (slides.item(i) === active){
            indexActive = i;
        }
    }
    
    if (sourceButton === nextButton) {
        if (indexActive === slidesQuantity - 1) {
            slides.item(0).classList.add("slide-active")
        } else {
            slides.item(indexActive + 1).classList.add("slide-active")
        }
    } else if (sourceButton === prevButton) {
        if (indexActive === 0){
            slides.item(slidesQuantity-1).classList.add("slide-active")
        } else {
            slides.item(indexActive -1).classList.add("slide-active")
        }
    } else {
        console.log("Bouton non existant")
    }

    active.classList.remove("slide-active");
}