(function ($, Drupal) {
  Drupal.behaviors.cancellarisorsa = {
    attach: function (context, settings) {

      $('#edit-username').change( function () {
         let check = checkUsername($('#edit-username').val());
         if (check){
           let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
               '<strong>Attenzione</strong><br>'+
           'Nome utente gia presente in db'
           + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
               '<span aria-hidden="true">&times;</span>' +
               '</button>' +
               '</div>';
           $('#edit-username').addClass('invalid');
           $('#myAlert').empty();
           $('#myAlert').append(alert1);
         } else {
           $('#myAlert').empty();
           $('#edit-username').removeClass('invalid');
         }
      });
      //$('#edit-preview').click(function () {
      $('#edit-submit1').once().bind('click', function(){
        let myReturn = true;
        let email = $('#edit-email').val();
        let emailResponsabile = $('#edit-emailresponsabile').val();
        let username = $('#edit-username').val();
        let nomecompleto = $('#edit-nomecompleto').val();
        let nomecompletoR = $('#edit-nomecompletoresponsabile').val();
        let pass = $('#edit-password').val();
        let passC = $('#edit-confermapassword').val();
        let ckan = $('#edit-istanzackan').val();
        let codice = $('#edit-codice').val();
        let emailOrg = $('#edit-emailorg').val();
        let org = $('#edit-organizazione').val();
        let desc = $('#edit-descrizione').val();
        let emailPass = true;
        let messagi = '';
        let messagi2 = ''
        let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            '<strong>Attenzione</strong><br>';

        let alert2 = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>';

        if (ckan === ''){
          $('#edit-istanzackan').addClass('invalid');
          $('[data-id="edit-istanzackan"]').addClass('invalid');
          myReturn = false;
          messagi = messagi + '<li>Istanza ckan</li>';
        } else {
          $('#edit-istanzackan').removeClass('invalid');
          $('[data-id="edit-istanzackan"]').removeClass('invalid');
        }

        if (email === ''){
          $('#edit-email').addClass('invalid');
          myReturn = false;
          emailPass = false;
          messagi = messagi + '<li>Email</li>';
        } else {
          if (isEmail(email)){
            $('#edit-email').removeClass('invalid');
          } else {
            $('#edit-email').addClass('invalid');
            messagi2 = messagi2 + 'Email:  Indirizzo e-mail non valido.</br>';
            myReturn = false;
            emailPass = false;
          }
        }


        if (org === ''){
          $('#edit-organizazione').addClass('invalid');
          myReturn = false;
          messagi = messagi + '<li>Nome organizzazione</li>';
        } else {
          $('#edit-organizazione').removeClass('invalid');
        }

        if (desc === ''){
          $('#edit-descrizione').addClass('invalid');
          myReturn = false;
          messagi = messagi + '<li>Descrizione organizzazione</li>';
        } else {
          $('#edit-descrizione').removeClass('invalid');
        }

        if (emailResponsabile === ''){
          $('#edit-emailresponsabile').addClass('invalid');
          myReturn = false;
          emailPass = false;
          messagi = messagi + '<li>Email di responsabile</li>';
        } else {
          if (isEmail(emailResponsabile)){
            $('#edit-emailresponsabile').removeClass('invalid');
          } else {
            $('#edit-emailresponsabile').addClass('invalid');
            messagi2 = messagi2 + 'Email di responsabile: Indirizzo e-mail non valido.</br>';
            myReturn = false;
            emailPass = false;
          }
        }

        if (username === ''){
          $('#edit-username').addClass('invalid');
          messagi = messagi + '<li>Nome utente</li>';
          myReturn = false;
        } else {
          if (checkUsername(username)){
            $('#edit-username').addClass('invalid');
            myReturn = false;
            messagi2 = messagi2 + 'Nome utente gia presente in db<br>';
          } else {
            $('#edit-username').removeClass('invalid');
          }
        }

        if (nomecompleto === ''){
          $('#edit-nomecompleto').addClass('invalid');
          myReturn = false;
          messagi = messagi + '<li>Nome completo</li>';
        } else {
          $('#edit-nomecompleto').removeClass('invalid');
        }

        if (codice === ''){
          $('#edit-codice').addClass('invalid');
          myReturn = false;
          messagi = messagi + '<li>Codice IPA/P. IVA</li>';
        } else {
          $('#edit-codice').removeClass('invalid');
        }

        if (nomecompletoR === ''){
          $('#edit-nomecompletoresponsabile').addClass('invalid');
          messagi = messagi + '<li>Nome completo di responsabile</li>';
          myReturn = false;
        } else {
          $('#edit-nomecompletoresponsabile').removeClass('invalid');
        }

        if (pass === '' || passC === ''){
          $('#edit-password').addClass('invalid');
          $('#edit-confermapassword').addClass('invalid');
          messagi = messagi + '<li>Password</li>';
          myReturn = false;
        } else {
          if (pass !== passC){
            $('#edit-password').addClass('invalid');
            $('#edit-confermapassword').addClass('invalid');
            messagi2 = messagi2 + 'Password: Le password non corrispondono <br>';
            myReturn = false;
          } else {
            $('#edit-password').removeClass('invalid');
            $('#edit-confermapassword').removeClass('invalid');
          }
        }

        if (emailPass){
            if (email === emailResponsabile){
              if (!confirm("Attenzione hai indicato la stessa mail, Confermi")) {
                myReturn = false;
                messagi2 = messagi2 + 'Il campo Email e Email Responsabile non possono corrispondere<br>';
                $('#edit-email').addClass('invalid');
                $('#edit-emailresponsabile').addClass('invalid');
              }
            }
        }

        if (!myReturn){
          $('#myAlert').empty();
          if (messagi !== ''){
            messagi = '  Inserisci il campi obbligatori:' + '<ul>' + messagi + '</ul>';
          }
          $('#myAlert').append( alert1 + messagi + messagi2 + alert2 );
          $('html, body').animate({ scrollTop: 150 }, 900);
        }

        return myReturn;
      });

      function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
      }

      function checkUsername(username){
        let myReturn = false;
        jQuery.ajax({
          url: "/admin/config/gestioneutenti/check-username/"+username,
          type: 'get',
          async: false
        }).done(function(response){
          //console.log('Response:',response )
          myReturn = response.status;
        });
        return myReturn;
      }

    }
  };
})(jQuery, Drupal);
