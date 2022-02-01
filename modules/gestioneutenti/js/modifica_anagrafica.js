function check_form_modifca_anagrafica($ = jQuery){
  let pass = $('#edit-password').val();
  let passC = $('#edit-confermapassword').val();
  let email = $('#edit-email').val();
  let passCor = $('#edit-corpass').val();
  let myReturn = true;
  let messagi = '';
  let messagi2 = ''
  let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
      '<strong>Attenzione</strong><br>';

  let alert2 = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '</div>';


  if (email === ''){
    $('#edit-email').addClass('invalid');
    myReturn = false;
    messagi = messagi + '<li>Email</li>';
  } else {
    if (isEmail(email)){
      $('#edit-email').removeClass('invalid');
    } else {
      $('#edit-email').addClass('invalid');
      messagi2 = messagi2 + 'Email:  Indirizzo e-mail non valido.</br>';
      myReturn = false;
    }
  }
  if (pass !== passC){
    $('#edit-password').addClass('invalid');
    $('#edit-confermapassword').addClass('invalid');
    messagi2 = messagi2 + 'Password: Le password non corrispondono <br>';
    myReturn = false;
  } else {
    $('#edit-password').removeClass('invalid');
    $('#edit-confermapassword').removeClass('invalid');
  }

  if (!checkPass(passCor)){
    $('#edit-corpass').addClass('invalid');
    messagi2 = messagi2 + ' La tua password corrente è mancante o errata<br>';
    myReturn = false;
  } else {
    $('#edit-corpass').removeClass('invalid');
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

}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}


function MyPass($ = jQuery){
    let pass = $('#edit-corpass').val();
    let myReturn = true;
    let messagi = '';
    let messagi2 = ''
    let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
        '<strong>Attenzione</strong><br>';

  let alert2 = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '</div>';
    if (!checkPass(pass)){
      $('#edit-corpass').addClass('invalid');
      messagi2 = messagi2 + ' La tua password corrente è mancante o errata<br>';
      myReturn = false;
    } else {
      $('#edit-corpass').removeClass('invalid');
    }
    if (!myReturn){
      $('#myAlert').empty();
      if (messagi !== ''){
        messagi = '  Inserisci il campi obbligatori:' + '<ul>' + messagi + '</ul>';
      }
      $('#myAlert').append( alert1 + messagi + messagi2 + alert2 );
      $('html, body').animate({ scrollTop: 150 }, 900);
    } else {
      $('#myAlert').empty();
    }
}


function checkPass(pass){
  pass = pass.trim();
  console.log('Pass:', pass);
  let myReturn = false;
  jQuery.ajax({
    url: "/admin/config/gestioneutenti/dataset/checkPass/"+pass,
    type: 'get',
    async: false
  }).done(function(response){
    //console.log('Response:',response )
    myReturn = response.status;
  });
  return myReturn;
}