function verifyInputLogin() {
      var form = document.querySelector("#form_login");
  
      var valueLogin = form._username.value;
      var valuePassword = form._password.value;
  
      if (valueLogin === "" || valuePassword === "") {
          Swal.fire({
              title: 'Incomplet !',
              html: "Merci de completer tout les champs afin de se connecter",
              icon: 'error',
              confirmButtonText: 'Completer'
            })
          return false;
      }else{
          return true;
      }
}