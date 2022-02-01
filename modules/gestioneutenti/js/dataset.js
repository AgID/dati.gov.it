function chengTemi(id, $ = jQuery){
  let idTema = jQuery('#edit-tema-' + id).val();
  $('#div-sotto-'+ id).empty();
  let select = '';
  if (idTema === ''){
    select = '<option value="" >Nessuna tema selezionata</option>';
  } else {
    jQuery.ajax({
      url: "/admin/config/gestioneutenti/dataset/sottotemi/"+idTema,
      type: 'get',
      async: false
    }).done(function(response){
      response.data.map((value) => {
        for (const key in value) {
          select = select + '<option value="'+ key +'" >'+value[key]+'</option>';
        }
      })
    });
  }
  let divSelect = '<div class="bootstrap-select-wrapper selectpicker">' +
      '<label for="edit-sotto-'+ id +'" class="active">Sottotema</label>' +
      '<select title="Nessuna selezione" name="temi['+ id +'][sotto][]" multiple="true" data-drupal-selector="edit-sotto-'+ id +'" id="edit-sotto-'+ id +'" class="form-select" tabindex="null">' +
        select +
      '</select>' +
      '</div>';
  $('#div-sotto-'+ id).append(divSelect);
  $('#edit-sotto-'+ id).selectpicker();
}

function checkDatasetName(name, idCkan){
  let myReturn = false;
  jQuery.ajax({
    url: "/admin/config/gestioneutenti/dataset/checkName/"+name+"/"+idCkan,
    type: 'get',
    async: false
  }).done(function(response){
    //console.log('Response:',response )
    myReturn = response.status;
  });
  return myReturn;
}

function deleteTemi(id, $ = jQuery){
    let temiTotal = parseInt($('#temiTotale').val());
    if (temiTotal === 0){
      $('#div-row-temi-' + id).remove();
      let temi =  parseInt($('#temiAdesso').val());
      temi++;
      let select = '';
      $.ajax({
        url: "/admin/config/gestioneutenti/dataset/temi",
        type: 'get',
        async: false
      }).done(function(response){
        response.data.map((value) => {
          for (const key in value) {
            select = select + '<option value="'+ key +'" >'+value[key]+'</option>';
          }
        })
      });
      let add = '' +
          '<div id="div-row-temi-'+ temi +'" class="row mt-5 pl-4 pr-4">' +
          '<div class="col-12 col-lg-5 col-md-5">' +
          '<div class="bootstrap-select-wrapper selectpicker">' +
          '<label for="edit-tema-'+ temi +'" class="active">Tema</label>' +
          '<select name="temi['+ temi +'][tema]" onchange="chengTemi('+ temi +');" data-drupal-selector="edit-tema-'+ temi +'" id="edit-tema-'+ temi +'" class="form-select">' +
          select +
          '</select>' +
          '</div>' +
          '</div>' +
          '<div id="div-sotto-' + temi +'" class="col-12 col-lg-5 col-md-5">' +
          '<div class="bootstrap-select-wrapper selectpicker">' +
          '<label for="edit-sotto-'+ temi +'" class="active">Sottotema</label>' +
          '<select title="Nessuna selezione" name="temi['+ temi +'][sotto][]" multiple="true" disabled="disabled" data-drupal-selector="edit-sotto-'+ temi +'" id="edit-sotto-'+ temi +'" class="form-select">' +
          '</select>' +
          '</div>' +
          '</div>' +
          '<div class="col-12 col-lg-2 col-md-2">' +
          '<input onclick="deleteTemi('+ temi +')" data-drupal-selector="edit-deletetema-'+ temi +'" type="submit" id="edit-deletetema-'+ temi +'" name="op" value="Rimuovi" class="button js-form-submit form-submit btn btn-primary">' +
          '</div>' +
          '</div>';

      $('#temi').append(add);
      $('#edit-tema-'+ temi).selectpicker();
      $('#edit-sotto-'+ temi).selectpicker();
      $('#temiAdesso').val(temi);
    } else {
      temiTotal--;
      $('#div-row-temi-' + id).remove();
      $('#temiTotale').val(temiTotal);
    }
    return false;
}



(function ($, Drupal) {

  Drupal.behaviors.dataset = {
    attach: function (context, settings) {
      $.fn.selectpicker.Constructor.BootstrapVersion = '4';
      'use strict';

      // Fix grafici relativi al form di inserimento dataset.
      // css per etichetta uuid
      $('label[for="edit-uuid"]').css("padding-left", "2px");
      $('label[for="edit-uuid"]').css("margin-bottom", "4px");
      // css per etichetta tags
      $('label[for="edit-tags"]').css("padding-left", "2px"); 

      $('#addTemaButton').click(function () {
        let temi =  parseInt($('#temiAdesso').val());
        let temiTotale =  parseInt($('#temiTotale').val());
        temi++;
        temiTotale++;
        let select = '';
        $.ajax({
          url: "/admin/config/gestioneutenti/dataset/temi",
          type: 'get',
          async: false
        }).done(function(response){
            response.data.map((value) => {
              for (const key in value) {
                select = select + '<option value="'+ key +'" >'+value[key]+'</option>';
              }
            })
        });
        let add = '' +
            '<div id="div-row-temi-'+ temi +'" class="row mt-5 pl-4 pr-4">' +
            '<div class="col-12 col-lg-5 col-md-5">' +
            '<div class="bootstrap-select-wrapper selectpicker">' +
            '<label for="edit-tema-'+ temi +'" class="active">Tema</label>' +
            '<select name="temi['+ temi +'][tema]" onchange="chengTemi('+ temi +');" data-drupal-selector="edit-tema-'+ temi +'" id="edit-tema-'+ temi +'" class="form-select">' +
                select +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div id="div-sotto-' + temi +'" class="col-12 col-lg-5 col-md-5">' +
              '<div class="bootstrap-select-wrapper selectpicker">' +
                '<label for="edit-sotto-'+ temi +'" class="active">Sottotema</label>' +
                '<select title="Nessuna selezione"  name="temi['+ temi +'][sotto][]" multiple="true" disabled="disabled" data-drupal-selector="edit-sotto-'+ temi +'" id="edit-sotto-'+ temi +'" class="form-select">' +
                '</select>' +
              '</div>' +
            '</div>' +
            '<div class="col-12 col-lg-2 col-md-2">' +
            '<input onclick="deleteTemi('+ temi +')" data-drupal-selector="edit-deletetema-'+ temi +'" type="submit" id="edit-deletetema-'+ temi +'" name="op" value="Rimuovi" class="button js-form-submit form-submit btn btn-primary">' +
            '</div>' +
            '</div>';

        $('#temi').append(add);
        $('#edit-tema-'+ temi).selectpicker();
        $('#edit-sotto-'+ temi).selectpicker();
        $('#temiAdesso').val(temi);
        $('#temiTotale').val(temiTotale);
        return false;
      });

      $('#edit-titolo').change( function (){
        let titolo = $('#edit-titolo').val();
        let idCkan = parseInt($('#edit-idckan').val());
        let check = checkDatasetName(titolo,idCkan);
        if (check){
          let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
              '<strong>Attenzione</strong><br>'+
              'Il titolo inserito è già presente'
              + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
              '<span aria-hidden="true">&times;</span>' +
              '</button>' +
              '</div>';
          $('#edit-titolo').addClass('invalid');
          $('#myAlert').empty();
          $('#myAlert').append(alert1);
        } else {
          $('#myAlert').empty();
          $('#edit-titolo').removeClass('invalid');
        }
      } );


      $('#edit-idckan').change( function () {
          let myDiv = '' +
              '<div class="row pr-4 pl-4">' +
                '<div class="col-12 mt-4 mb-4">' +
                  '<h4>Localizzazione amministrazioni  <small class="text-muted">(Ulteriori informazioni per consentire la ricerca per territorio)</small></h4>' +
                '</div>' +
                '<div class="col-12 col-lg-4 col-md-4">' +
                  '<div class="js-form-item form-item form-group js-form-type-textfield form-item-regione js-form-item-regione">' +
                    '<label for="edit-regione" class="" style="width: auto;">Regione *</label>' +
                    '<input data-drupal-selector="edit-regione" type="text" id="edit-regione" name="regione" value="" size="60" maxlength="128" class="form-text form-control">' +
                  '</div>' +
                '</div>' +
                '<div class="col-12 col-lg-4 col-md-4">' +
                  '<div class="js-form-item form-item form-group js-form-type-textfield form-item-provincia js-form-item-provincia">' +
                    '<label for="edit-provincia" class="" style="width: auto;">Provincia *</label>' +
                    '<input data-drupal-selector="edit-provincia" type="text" id="edit-provincia" name="provincia" value="" size="60" maxlength="128" class="form-text form-control">' +
                  '</div>' +
                '</div>' +
                '<div class="col-12 col-lg-4 col-md-4">' +
                  '<div class="js-form-item form-item form-group js-form-type-textfield form-item-comune js-form-item-comune">' +
                    '<label for="edit-comune" class="" style="width: auto;">Comune *</label>' +
                    '<input data-drupal-selector="edit-comune" type="text" id="edit-comune" name="comune" value="" size="60" maxlength="128" class="form-text form-control">' +
                  '</div>' +
                '</div>' +
               // '<div class="row pr-4 pl-4"><small id="formGroupExampleInputWithHelpDescription" class="form-text text-muted">* Aggiungi ulteriori informazioni relative alla localizzazione per agevolare la ricerca</small></div>' +
              '</div>';


          let value =  parseInt($('#edit-idckan').val());
          if (value === 2){
            $('#divRegione').append(myDiv);
            $('#licenza').addClass('d-none');
            $('#temiTitle').removeClass('mt-5');
          } else {
            $('#divRegione').empty();
            $('#temiTitle').addClass('mt-5');
            $('#licenza').removeClass('d-none');
          }
      });
    }
  }
})(jQuery, Drupal);;


function addTemp($ = jQuery){

  let temp = parseInt($('#tempAdesso').val());
  let tempTotal = parseInt($('#tempTotale').val());
  temp++;
  tempTotal++;
  let divTemp = '' +
      '<div id="div-row-temp-'+ temp +'" class="row pl-4 pr-4">' +
        '<div class="col-12 col-lg-5 col-md-5">' +
          '<div class="js-form-item form-item form-group js-form-type-date form-item-fir-dt-ini-'+ temp +' js-form-item-fir-dt-ini-'+ temp +'">' +
            '<label for="edit-fir-dt-ini-'+ temp +'" class="active">Data iniziale gg/mm/aaaa</label>' +
            '<input name="ext['+ temp +'][datainizio]" data-drupal-selector="edit-fir-dt-ini-'+ temp +'" type="date" id="edit-fir-dt-ini-'+ temp +'" value="" class="form-date form-control">' +
          '</div>' +
        '</div>' +
        '<div class="col-12 col-lg-5 col-md-5">' +
          '<div class="js-form-item form-item form-group js-form-type-date form-item-fir-dt-fin-'+ temp +' js-form-item-fir-dt-fin-'+ temp +'">' +
            '<label for="edit-fir-dt-fin-'+ temp +'" class="active">Data finale gg/mm/aaaa</label>' +
            '<input name="ext['+ temp +'][datafine]" data-drupal-selector="edit-fir-dt-fin-'+ temp +'" type="date" id="edit-fir-dt-fin-'+ temp +'" value="" class="form-date form-control">' +
          '</div>' +
        '</div>' +
        '<div class="col-12 col-lg-2 col-md-2">' +
            '<input onclick="return delTemp('+ temp +')" data-drupal-selector="edit-fir-del-'+ temp +'" type="submit" id="edit-fir-del-'+ temp +'" name="op" value="Rimuovi" class="button js-form-submit form-submit btn btn-primary">' +
        '</div>' +
      '</div>';

  $('#temp').append(divTemp);
  $('#tempTotale').val(temp);
  $('#tempAdesso').val(tempTotal);
  return false;
}

function delTemp(id,$ = jQuery){
  let tempTotal = parseInt($('#tempTotale').val());

  if (tempTotal === 0){
    $('#div-row-temp-'+id).remove();
    let temp = parseInt($('#tempAdesso').val());
    temp++;
    let divTemp = '' +
        '<div id="div-row-temp-'+ temp +'" class="row mt-5 pl-4 pr-4">' +
        '<div class="col-12 col-lg-5 col-md-5">' +
        '<div class="js-form-item form-item form-group js-form-type-date form-item-fir-dt-ini-'+ temp +' js-form-item-fir-dt-ini-'+ temp +'">' +
        '<label for="edit-fir-dt-ini-'+ temp +'" class="active">Data iniziale gg/mm/aaaa</label>' +
        '<input name="ext['+ temp +'][datainizio]" data-drupal-selector="edit-fir-dt-ini-'+ temp +'" type="date" id="edit-fir-dt-ini-'+ temp +'" value="" class="form-date form-control">' +
        '</div>' +
        '</div>' +
        '<div class="col-12 col-lg-5 col-md-5">' +
        '<div class="js-form-item form-item form-group js-form-type-date form-item-fir-dt-fin-'+ temp +' js-form-item-fir-dt-fin-'+ temp +'">' +
        '<label for="edit-fir-dt-fin-'+ temp +'" class="active">Data finale gg/mm/aaaa</label>' +
        '<input name="ext['+ temp +'][datafine]" data-drupal-selector="edit-fir-dt-fin-'+ temp +'" type="date" id="edit-fir-dt-fin-'+ temp +'" value="" class="form-date form-control">' +
        '</div>' +
        '</div>' +
        '<div class="col-12 col-lg-2 col-md-2">' +
        '<input onclick="return delTemp('+ temp +')" data-drupal-selector="edit-fir-del-'+ temp +'" type="submit" id="edit-fir-del-'+ temp +'" name="op" value="Rimuovi" class="button js-form-submit form-submit btn btn-primary">' +
        '</div>' +
        '</div>';

    $('#temp').append(divTemp);
    $('#tempAdesso').val(temp);
  } else {
    $('#div-row-temp-'+id).remove();
    tempTotal--;
    $('#tempTotale').val(tempTotal);
  }

  return false;
}

function addCon($ = jQuery){
    let con = parseInt($('#conAdesso').val());
    let conTotal = parseInt($('#conTotale').val());
    con++;
    conTotal++;

    let myDiv = '' +
        '<div id="div-row-con-'+ con +'" class="row pr-4 pl-4">' +
          '<div class="col-12 col-lg-5 col-md-5">' +
            '<div class="js-form-item form-item form-group js-form-type-textfield form-item-con-tit-'+ con +' js-form-item-con-tit-'+ con +'">' +
              '<label for="edit-con-tit-'+ con +'" class="" style="width: auto;">Titolo</label>' +
              '<input name="con['+ con +'][titolostandard]" data-drupal-selector="edit-con-tit-'+ con +'" type="text" id="edit-con-tit-'+ con +'" value="" size="60" maxlength="128" class="form-text form-control">' +
            '</div>' +
          '</div>' +
          '<div class="col-12 col-lg-5 col-md-5">' +
            '<div class="js-form-item form-item form-group js-form-type-textfield form-item-con-url-'+ con +' js-form-item-con-url-'+ con +'">' +
              '<label for="edit-con-url-'+ con +'" class="" style="width: auto;">URI standard</label>' +
              '<input name="con['+ con +'][urlstandard]" data-drupal-selector="edit-con-url-'+ con +'" type="text" id="edit-con-url-'+ con +'" value="" size="60" maxlength="128" class="form-text form-control">' +
            '</div>' +
          '</div>' +
          '<div class="col-12 col-lg-2 col-md-2">' +
            '<input onclick="return delCon('+ con +')" data-drupal-selector="edit-con-del-'+ con +'" type="submit" id="edit-con-del-'+ con +'" name="op" value="Rimuovi" class="button js-form-submit form-submit btn btn-primary">' +
          '</div>' +
        '</div>';

    $('#con').append(myDiv);
    $('#conAdesso').val(con);
    $('#conTotale').val(conTotal);
    return false;
}

function delCon(id,$ = jQuery){
  let conTotal = parseInt($('#conTotale').val());
  if (conTotal === 0){
      let con = parseInt($('#conAdesso').val());
      con++;
      $('#div-row-con-'+id).remove();
      let myDiv = '' +
          '<div id="div-row-con-'+ con +'" class="row pr-4 pl-4">' +
          '<div class="col-12 col-lg-5 col-md-5">' +
          '<div class="js-form-item form-item form-group js-form-type-textfield form-item-con-tit-'+ con +' js-form-item-con-tit-'+ con +'">' +
          '<label for="edit-con-tit-'+ con +'" class="" style="width: auto;">Titolo</label>' +
          '<input name="con['+ con +'][titolostandard]" data-drupal-selector="edit-con-tit-'+ con +'" type="text" id="edit-con-tit-'+ con +'" value="" size="60" maxlength="128" class="form-text form-control">' +
          '</div>' +
          '</div>' +
          '<div class="col-12 col-lg-5 col-md-5">' +
          '<div class="js-form-item form-item form-group js-form-type-textfield form-item-con-url-'+ con +' js-form-item-con-url-'+ con +'">' +
          '<label for="edit-con-url-'+ con +'" class="" style="width: auto;">URI standard</label>' +
          '<input name="con['+ con +'][urlstandard]" data-drupal-selector="edit-con-url-'+ con +'" type="text" id="edit-con-url-'+ con +'" value="" size="60" maxlength="128" class="form-text form-control">' +
          '</div>' +
          '</div>' +
          '<div class="col-12 col-lg-2 col-md-2">' +
          '<input onclick="return delCon('+ con +')" data-drupal-selector="edit-con-del-'+ con +'" type="submit" id="edit-con-del-'+ con +'" name="op" value="Rimuovi" class="button js-form-submit form-submit btn btn-primary">' +
          '</div>' +
          '</div>';
      $('#con').append(myDiv);
      $('#conAdesso').val(con);
  } else {
      $('#div-row-con-'+id).remove();
      conTotal--;
      $('#conTotale').val(conTotal);
  }
  return false;
}


function validURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
      '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  return !!pattern.test(str);
}

function form_dataset_submit($ = jQuery){
  let myReturn = true;
  let idCkan = parseInt($('#edit-idckan').val());
  let titolo = $('#edit-titolo').val();
  let descrizione = $('#edit-note').val();
  let ult = $('#edit-datamodifica').val();
  let freg = $('#edit-frequenza').val();
  let visibilita = $('#edit-visibilita').val();
  let licenza = $('#edit-licenzarisorsa').val();
  let email = $('#edit-emailcontatto').val();

  let url = $('#edit-pagina').val();
  let regione = $('#edit-regione').val();
  let provincia = $('#edit-provincia').val();
  let comune = $('#edit-comune').val();
  let messagi = '';
  let messagi2 = ''
  let alert1 = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
      '<strong>Attenzione</strong><br>';

  let alert2 = '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
      '<span aria-hidden="true">&times;</span>' +
      '</button>' +
      '</div>';

     if (url !== '') {
    if (url.indexOf("http://") == 0 || url.indexOf("https://") == 0){
      if (!validURL(url)) {
        myReturn = false;
        messagi2 = messagi2 + 'Campo Pagina di accesso: url non valido<br>';
        $('#edit-pagina').addClass('invalid');
      }
      else {
        $('#edit-pagina').removeClass('invalid');
      }
    } else {
      myReturn = false;
      messagi2 = messagi2 + 'Campo Pagina di accesso: url non valido<br>';
      $('#edit-pagina').addClass('invalid');
    }
  } else {
  $('#edit-pagina').removeClass('invalid');
  }

  if (idCkan === 0){
     $('#edit-idckan').addClass('invalid');
     $('[data-id="edit-idckan"]').addClass('invalid');
     messagi = messagi + '<li>Seleziona il catalogo</li>';
     myReturn = false;
  } else {
    $('#edit-idckan').removeClass('invalid');
    $('[data-id="edit-idckan"]').removeClass('invalid');
  }

  if (licenza === '' && idCkan !== 2){
    $('#edit-licenzarisorsa').addClass('invalid');
    $('[data-id="edit-licenzarisorsa"]').addClass('invalid');
    messagi = messagi + '<li>Licenza</li>';
    myReturn = false;
  } else {
    $('#edit-licenzarisorsa').removeClass('invalid');
    $('[data-id="edit-licenzarisorsa"]').removeClass('invalid');
  }

  if (visibilita === ''){
    $('#edit-visibilita').addClass('invalid');
    $('[data-id="edit-visibilita"]').addClass('invalid');
    messagi = messagi + '<li>Visibilità</li>';
    myReturn = false;
  } else {
    $('#edit-visibilita').removeClass('invalid');
    $('[data-id="edit-visibilita"]').removeClass('invalid');
  }

  if (titolo === ''){
    $('#edit-titolo').addClass('invalid');
    messagi = messagi + '<li>Titolo</li>';
    myReturn = false;
  } else {
    if (checkDatasetName(titolo,idCkan)){
      $('#edit-titolo').addClass('invalid');
      myReturn = false;
      messagi2 = messagi2 + 'Il titolo inserito è già presente<br>';
    } else {
      $('#edit-titolo').removeClass('invalid');
    }

  }
  if (descrizione === ''){
    $('#edit-note').addClass('invalid');
    messagi = messagi + '<li>Descrizione</li>';
    myReturn = false;
  } else {
    $('#edit-note').removeClass('invalid');
  }
  if (ult === ''){
    $('#edit-datamodifica').addClass('invalid');
    messagi = messagi + '<li>Ultima modifica</li>';
    myReturn = false;
  } else {
    $('#edit-datamodifica').removeClass('invalid');
  }
  if (freg === ''){
    $('#edit-frequenza').addClass('invalid');
    $('[data-id="edit-frequenza"]').addClass('invalid');
    messagi = messagi + '<li>Frequenza aggiornamento</li>';
    myReturn = false;
  } else {
    $('#edit-frequenza').removeClass('invalid');
    $('[data-id="edit-edit-frequenza"]').removeClass('invalid');
  }


  let isTema = true;
  $('[name^="temi"]').map(function() {
    let name = $( this ).attr('name');
    let find = name.indexOf("[tema]");
    if (find > 0){
      let val = $( this ).val();
      if ( val !== ''){
        isTema = false;
      }
    }
  })



  if (isTema){
    messagi = messagi + '<li>Tema</li>';
    myReturn = false;
    $('[name^="temi"]').map(function() {
      let name = $( this ).attr('name');
      let find = name.indexOf("[tema]");
      if (find > 0){

          let id = $( this ).attr('data-drupal-selector');
          $('[data-id="'+ id +'"]').addClass('invalid');
          $( this ).addClass('invalid');
      }
    })
  } else{
    $('[name^="temi"]').map(function() {
      let name = $( this ).attr('name');
      let find = name.indexOf("[tema]");
      if (find > 0){
        let id = $( this ).attr('data-drupal-selector');
        $('[data-id="'+ id +'"]').removeClass('invalid');
        $( this ).removeClass('invalid');
      }
    })
  }


  if (regione === '' && idCkan !== 1){
    $('#edit-regione').addClass('invalid');
    $('[data-id="edit-regione"]').addClass('invalid');
    messagi = messagi + '<li>Regione</li>';
    myReturn = false;
  } else {
    $('#edit-regione').removeClass('invalid');
    $('[data-id="edit-regione"]').removeClass('invalid');
  }
  
  if (provincia === '' && idCkan !== 1){
    $('#edit-provincia').addClass('invalid');
    $('[data-id="edit-provincia"]').addClass('invalid');
    messagi = messagi + '<li>Provincia</li>';
    myReturn = false;
  } else {
    $('#edit-provincia').removeClass('invalid');
    $('[data-id="edit-provincia"]').removeClass('invalid');
  }
  
  if (comune === '' && idCkan !== 1){
    $('#edit-comune').addClass('invalid');
    $('[data-id="edit-comune"]').addClass('invalid');
    messagi = messagi + '<li>Comune</li>';
    myReturn = false;
  } else {
    $('#edit-comune').removeClass('invalid');
    $('[data-id="edit-comune"]').removeClass('invalid');
  }

  // Controllo sulla e-mail 
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (email) {
    	if (regex.test(email) === false) {
      		$('#edit-emailcontatto').addClass('invalid');
      		$('[data-id="edit-emailcontatto"]').addClass('invalid');
      		messagi = messagi + '<li>Email</li>';
      		myReturn = false;
    	} else {
      		$('#edit-emailcontatto').removeClass('invalid');
      		$('[data-id="edit-edit-emailcontatto"]').removeClass('invalid');
    	}
    }

  let start = '';

  let arrayTime = [];

  $('[name^="ext"]').map(function() {
    let name = $( this ).attr('name')
    let findStart = name.indexOf("[datainizio]");
    let findEnd = name.indexOf("[datafine]");
    if (findStart > 0){
      start = $( this ).val();
    }
    if (findEnd > 0 ){
      arrayTime.push({
        start:start,
        end:$( this ).val()
      });
      start = '';
    }
  })

  arrayTime.map( (val, id ) => {
    console.log(val.end);
    console.log(val.start);
  } );

  if (!myReturn){
    $('#myAlert').empty();
     if (messagi !== ''){
            messagi = '  Inserire i campi seguenti:' + '<ul>' + messagi + '</ul>';
     }
     $('#myAlert').append( alert1 + messagi + messagi2 + alert2 );
    $('html, body').animate({ scrollTop: 150 }, 900);
  } else {
    $("#edit-tags").val($("#edit-tags").tagsinput('items')) ;
  }

  return myReturn;
}



     
    
      
   
   
window.onload = function(){
   setInterval(function(){
   // Fix per lo states in form.
   $('label[for="edit-soggetti-nome"]').css("width", "auto");
   $('label[for="edit-autore-nome"]').css("width", "auto");
   $('label[for="edit-autore-codice"]').css("width", "auto");
   $('label[for="edit-con-tit-0"]').css("width", "auto");
   $('label[for="edit-autore-codice"]').css("width", "auto");
   $('label[for="edit-con-url-0"]').css("width", "auto");
   $('label[for="edit-nomecontatto"]').css("width", "auto");
   $('label[for="edit-emailcontatto"]').css("width", "auto");
   $('label[for="edit-soggetti-codice"]').css("width", "auto");
   $('label[for="edit-urlgeografico"]').css("width", "auto");
   }, 1000);
 };






