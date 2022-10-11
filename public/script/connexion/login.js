/**
 * Va verifier les input dans le form
 */
function verifyInputLogin() {
      var form = document.querySelector("#form-login");

      var valueLogin = form.login.value;
      var valuePassword = form.password.value;

      if (valueLogin === "" || valuePassword === "" || valuePassword.length < 5) {
          Swal.fire({
              title: 'Incomplet !',
              html: "Merci de completer correctement tout les champs afin de vous connecter",
              icon: 'error',
              confirmButtonText: 'Completer'
            })
          return false;
      }else{
          return true;
      }
}