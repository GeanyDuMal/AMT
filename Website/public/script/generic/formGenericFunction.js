/**
 * Lors du submit d'un formulaire, va desactiver tout les boutons marqués par la classe disableable
 */
function disableButton(){
    document.querySelectorAll('.disableable').forEach(function (button) {
        button.disabled = true
    })
}