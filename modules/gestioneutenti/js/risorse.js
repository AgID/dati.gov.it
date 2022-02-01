(function ($, Drupal) {
  Drupal.behaviors.cancellarisorsa = {
    attach: function (context, settings) {


      //$('#edit-preview').click(function () {
      $('#edit-cancellarisorsa').once().bind('click', function(){
        //alert("ciao mondo 2");
        if(confirm("Sei sicuro di voler cancellare la risorsa?"))
          return true;
        else
          return false;
      });

      $('#edit-save').once().bind('click', function(){
        let myReturn = true;
        let url = $('#edit-urla').val();
        let titolo = $('#edit-titolo').val();
        let formato = $('#edit-formatodistribuzione').val();
        let licenza = $('#edit-licenzarisorsa').val();

        let messagi = '';
        let messagi2 = ''
        let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            '<strong>Attenzione</strong><br>';

        let alert2 = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>';

        if (url === ''){
          $('#edit-urla').addClass('invalid');
          messagi = messagi + '<li>URL di accesso</li>';
          myReturn = false;
        } else {
          $('#edit-urla').removeClass('invalid');
        }
        if (titolo === ''){
          $('#edit-titolo').addClass('invalid');
          messagi = messagi + '<li>Titolo</li>';
          myReturn = false;
        } else {
          $('#edit-titolo').removeClass('invalid');
        }
        if (formato === ''){
          $('#edit-formatodistribuzione').addClass('invalid');
          $('[data-id="edit-formatodistribuzione"]').addClass('invalid');
          messagi = messagi + '<li>Formato</li>';
          myReturn = false;
        } else {
          $('#edit-formatodistribuzione').removeClass('invalid');
          $('[data-id="edit-formatodistribuzione"]').removeClass('invalid');
        }

        if (licenza === ''){
          $('#edit-licenzarisorsa').addClass('invalid');
          $('[data-id="edit-licenzarisorsa"]').addClass('invalid');
          messagi = messagi + '<li>Licenza</li>';
          myReturn = false;
        } else {
          $('#edit-formatodistribuzione').removeClass('invalid');
          $('[data-id="edit-licenzarisorsa"]').removeClass('invalid');
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

    }
  };
})(jQuery, Drupal);