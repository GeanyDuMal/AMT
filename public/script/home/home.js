function changeAccordionActive() {
    let active = document.querySelector(".active-accordion")
    active.classList.toggle("active-accordion");
    this.classList.toggle("active-accordion");
    return true
}

let accordionItems = document.querySelectorAll(".accordion-item")

for (let item of accordionItems) {
    item.addEventListener("click", function() {
        let active = document.querySelector(".active-accordion")
        active.classList.toggle("active-accordion");
        this.classList.toggle("active-accordion");
        return true
    });
}