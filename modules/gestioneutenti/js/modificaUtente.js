(function ($, Drupal) {
  Drupal.behaviors.cancellarisorsa = {
    attach: function (context, settings) {


      //$('#edit-preview').click(function () {
      $('#edit-salva').once().bind('click', function(){
        let myReturn = true;

        let dati = $('#edit-ckankeydatigov').val();
        let basi = $('#edit-ckankeybasigov').val();

        let datiO = $('#edit-idorgdatigov').val();
        let basiO = $('#edit-idorgbasigov').val();

        let messagi = '';
        let messagi2 = ''
        let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            '<strong>Attenzione</strong><br>  Inserisci il campi obbligatori:';

        let alert2 = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>';


        if (dati === ''){
          $('#edit-ckankeydatigov').addClass('invalid');
          messagi = messagi + '<li>Chiave ckan dati gov</li>';
          myReturn = false;
        } else {
          $('#edit-ckankeydatigov').removeClass('invalid');
        }

        if (datiO === ''){
          $('#edit-idorgdatigov').addClass('invalid');
          messagi = messagi + '<li>Id organizzazione su Istanza ckan:Dati gov</li>';
          myReturn = false;
        } else {
          $('#edit-idorgdatigov').removeClass('invalid');
        }


        if (basi === ''){
          $('#edit-ckankeybasigov').addClass('invalid');
          messagi = messagi + '<li>Chiave ckan basi gov</li>';
          myReturn = false;
        } else {
          $('#edit-ckankeybasigov').removeClass('invalid');
        }

        if (basiO === ''){
          $('#edit-idorgbasigov').addClass('invalid');
          messagi = messagi + '<li>Id organizzazione su Istanza ckan:Basi gov</li>';
          myReturn = false;
        } else {
          $('#edit-idorgbasigov').removeClass('invalid');
        }


        if (!myReturn){
          $('#myAlert').empty();
          $('#myAlert').append( alert1 + '<ul>' + messagi + '</ul>' + messagi2 + alert2 );
          $('html, body').animate({ scrollTop: 150 }, 900);
        }

        return myReturn;
      });

      function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
      }

    }
  };
})(jQuery, Drupal);