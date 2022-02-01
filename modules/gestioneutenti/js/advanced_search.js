
var bottomH = 0;

function copyLinkRisorse(a, link){
    var textArea = document.createElement("textarea");
    textArea.value = link;

    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    var successful = document.execCommand('copy');

    document.body.removeChild(textArea);

    let idNotifica = 'copyLink-' + Math.floor(Math.random() * (99999 - 1 + 1) ) + 1;

    document.getElementById("myNotificaCopy").innerHTML = document.getElementById("myNotificaCopy").innerHTML + '' +
        // '<div class="row" >' +
        // '<div class="col-12 col-md-6" >' +
        '<div class="notification with-icon success dismissable" role="alert" style="display: block;margin-bottom: 80px; bottom: '+ bottomH +'px;" aria-labelledby="not2dms-title" id="'+ idNotifica +'">\n' +
            '<h5 id="not2dms-title"><svg class="icon"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-check-circle"></use></svg>Link Copiato con successo</h5>\n' +
            '<button type="button" class="btn notification-close">\n' +
                '<svg class="icon"><use xlink:href="/themes/contrib/bootstrap_italia/assets/icons/sprite.svg#it-close"></use></svg>\n' +
                '<span class="sr-only">Chiudi notifica: Titolo notifica</span>\n' +
            '</button>\n' +
        // '</div>\n' +
        // '</div>\n' +
        '</div>';
    if (bottomH === 640){
        bottomH = 0;
    } else {
        bottomH = bottomH + 80;
    }

    // console.log('BOTTOM:', bottomH);
    notificationShow(idNotifica,6000);
}

function notificationShow(notificationTarget, notificationTimeOut) {
    // console.log('ID NOTIFICA', notificationTarget);
    jQuery('#' + notificationTarget).fadeIn(300)

    if (!jQuery('#' + notificationTarget).hasClass('dismissable')) {
        //standard (timeout)
        jQuery('#' + notificationTarget).fadeIn(300)
        if (typeof notificationTimeOut == 'number') {
            //timeout set by parameter
            var timeToFade = notificationTimeOut
        } else {
            //timeout default value 7s
            var timeToFade = 7000
        }
        //fadeout
        setTimeout(function() {
            jQuery('#' + notificationTarget).fadeOut(100)
        }, timeToFade)
    }
}
window.onload = function(){ jQuery('#bs-select-1').prop('title', 'ordinamento'); };
// jQuery(document).load(function(){
//     jQuery('#bs-select-1').prop('title', 'ordinamento');s
// });

(function ($, Drupal) {
  Drupal.behaviors.scaricalink = {
    attach: function (context, settings) {
        'use strict';
	
        jQuery('#bs-select-1').prop('title', 'ordinamento');
 
        // DELETE FIELD FROM DATASET AND DATA SECTION.
        if((window.location.href.split("/")).pop() != "layout") {
	    // Fix date field converted. 
	    var stringDate = $("#it-it-dataset-catalogo-ultima-modifica-value div").text();
	    var newDate = new Date(stringDate);
	    if(!isNaN(newDate.getTime())) {
            	var yearConverted = newDate.getFullYear();
	    	var monthConverted = ('0' + (newDate.getMonth()+1)).slice(-2);
	    	var dayConverted = ('0' + newDate.getDate()).slice(-2);
	    	var dateConverted = yearConverted + "-" + monthConverted + "-" + dayConverted;
	    	$("#it-it-dataset-catalogo-ultima-modifica-value div").text(dateConverted);
	    }	
 
	    var stringDate = $("#it-it-dataset-ultima-modifica-value div").text().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3");
	    var newDate = new Date(stringDate);
	    if(!isNaN(newDate.getTime())) {
	    	var yearConverted = newDate.getFullYear();
	    	var monthConverted = ('0' + (newDate.getMonth()+1)).slice(-2);
	    	var dayConverted = ('0' + newDate.getDate()).slice(-2);
	    	var dateConverted = yearConverted + "-" + monthConverted + "-" + dayConverted;
	    	$("#it-it-dataset-ultima-modifica-value div").text(dateConverted);
	    }

	    var stringDate = $("#it-it-dataset-data-rilascio-value div").text().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3");
            var newDate = new Date(stringDate);
            if(!isNaN(newDate.getTime())) {  
            	var yearConverted = newDate.getFullYear();
            	var monthConverted = ('0' + (newDate.getMonth()+1)).slice(-2);
            	var dayConverted = ('0' + newDate.getDate()).slice(-2);
            	var dateConverted = yearConverted + "-" + monthConverted + "-" + dayConverted;
            	$("#it-it-dataset-data-rilascio-value div").text(dateConverted);
	    }

	    // Fix date for base di dati
            var stringDate = $("#it-it-base-ultima-modifica-value div").text().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3");
            var newDate = new Date(stringDate);
            if(!isNaN(newDate.getTime())) {
                var yearConverted = newDate.getFullYear();
                var monthConverted = ('0' + (newDate.getMonth()+1)).slice(-2);
                var dayConverted = ('0' + newDate.getDate()).slice(-2);
                var dateConverted = yearConverted + "-" + monthConverted + "-" + dayConverted;
                $("#it-it-base-ultima-modifica-value div").text(dateConverted);
            }

            var stringDate = $("#it-it-base-catalogo-ultima-modifica-value div").text().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3");
            var newDate = new Date(stringDate);
            if(!isNaN(newDate.getTime())) {
                var yearConverted = newDate.getFullYear();
                var monthConverted = ('0' + (newDate.getMonth()+1)).slice(-2);
                var dayConverted = ('0' + newDate.getDate()).slice(-2);
                var dateConverted = yearConverted + "-" + monthConverted + "-" + dayConverted;
                $("#it-it-base-catalogo-ultima-modifica-value div").text(dateConverted);
            }   

            var stringDate = $("#it-it-base-data-rilascio-value div").text().replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3");
            var newDate = new Date(stringDate);
            if(!isNaN(newDate.getTime())) {
                var yearConverted = newDate.getFullYear();
                var monthConverted = ('0' + (newDate.getMonth()+1)).slice(-2);
                var dayConverted = ('0' + newDate.getDate()).slice(-2);
                var dateConverted = yearConverted + "-" + monthConverted + "-" + dayConverted;
                $("#it-it-base-data-rilascio-value div").text(dateConverted);
            }

            if($("#it-dataset-est-temp-data-inizio-value").length == 0) {
                $("#it-dataset-est-temp-data-inizio").remove();
                $("#it-it-dataset-est-temp-data-inizio-space").remove();
            }

            if($("#it-it-dataset-est-temp-data-fine-value").length == 0) {
                $("#it-it-dataset-est-temp-data-fine").remove();
                $("#it-it-dataset-est-temp-data-fine-space").remove();
            }

            if($("#it-it-dataset-autore-codice-ipa-value").length == 0) {
                $("#it-it-dataset-autore-codice-ipa").remove();
                $("#it-dataset-dettaglio-block-9").remove();
            }

            if($("#it-it-dataset-autore-value").length == 0) {
                $("#it-it-dataset-autore").remove();
                $("#it-it-dataset-autore-codice-ipa-space").remove();
            }

            if($("#it-it-dataset-sottotemi-value").length == 0) {
                $("#it-it-dataset-sottotemi").remove();
                $("#it-it-dataset-sottotemi-space").remove();
            }

            if($("#it-it-dataset-parole-value").length == 0) {
                $("#it-it-dataset-parole").remove();
                $("#it-it-dataset-parole-space").remove();
            }

            if($("#it-it-dataset-data-rilascio-value").length == 0) {
                $("#it-it-dataset-data-rilascio").remove();
                $("#it-dataset-dettaglio-block-16").remove();
            }
	
            //if($("#it-it-dataset-est-temp-data-fine-value").length == 0) {
            //    $("#it-it-dataset-est-temp-data-fine").remove();
            //    $("#it-it-dataset-est-temp-data-fine-space").remove();
            //}

            if($("#it-it-dataset-cop-geo-value").length == 0) {
                $("#it-it-dataset-cop-geo").remove();
                $("#it-it-dataset-cop-geo-space").remove();
            }

            if($("#it-it-dataset-lingua-value").length == 0) {
                $("#it-it-dataset-lingua").remove();
                $("#it-it-dataset-lingua-space").remove();
            }

            if($("#it-it-dataset-versione-value").length == 0) {
                $("#it-it-dataset-versione").remove();
                $("#it-it-dataset-versione-space").remove();
            }

            if($("#it-it-dataset-conformita-value").length == 0) {
                $("#it-it-dataset-conformita").remove();
                $("#it-it-dataset-conformita-space").remove();
            }

            if($("#it-it-dataset-conformita-titolo-value").length == 0) {
                $("#it-it-dataset-conformita-titolo").remove();
                $("#it-it-dataset-conformita-titolo-space").remove();
            }

            if($("#it-it-dataset-altro-identificativo-value").length == 0) {
                $("#it-it-dataset-altro-identificativo").remove();
                $("#it-it-dataset-altro-identificativo-space").remove();
            }

            if($("#it-it-dataset-pagina-accesso-value").length == 0) {
                $("#it-it-dataset-pagina-accesso").remove();
                $("#it-it-dataset-pagina-accesso-space").remove();
            }

            if($("#it-it-dataset-pagina-accesso-value").length == 0) {
                $("#it-it-dataset-pagina-accesso").remove();
                $("#it-it-dataset-pagina-accesso-space").remove();
            }
 
	 

	    


	    if($("#it-it-base-comune-value").length == 0 && $("#it-it-base-provincia-value").length == 0 && $("#it-it-base-regione-value").length == 0) {
                $('#it-base-dati-dettaglio-block-11').parent().parent().parent().parent().parent().css('display', 'none');
            }

            if($("#it-dataset-est-temp-data-inizio-value").length == 0 && $("#it-it-dataset-cop-geo-value").length == 0 && $("#it-it-dataset-lingua-value").length == 0 && $("#it-it-dataset-versione-value").length == 0 && $("#it-it-dataset-conformita-value").length == 0 && $("#it-it-dataset-conformita-titolo-value").length == 0 && $("#it-it-dataset-altro-identificativo-value").length == 0 && $("#it-it-dataset-pagina-accesso-value").length == 0) {
		$('#it-dataset-dettaglio-block-29').parent().parent().parent().parent().parent().css('display', 'none');
	    }

	    if($("#it-it-base-regione-value").length == 0 && $("#it-it-base-provincia-value").length == 0 && $("#it-it-base-comune-value").length == 0) {
		$('#it-base-dati-dettaglio-localizzazione-territoriale-block').parent().parent().parent().parent().parent().css('display', 'none');
	    }   

            if($("#it-it-base-comune-value").length == 0) {
                $("#it-it-base-comune").remove();
                $("#it-base-dati-dettaglio-block-10").remove();
            }

            if($("#it-it-base-provincia-value").length == 0) {
                $("#it-it-base-provincia").remove();
                $("#it-base-dati-dettaglio-block-9").remove();
            }

            if($("#it-it-base-regione-value").length == 0) {
                $("#it-it-base-regione").remove();
                $("#it-it-base-regione-space").remove();
            }

            if($("#it-it-base-categoria-value").length == 0) {
                $("#it-it-base-categoria").remove();
                $("#it-base-dati-dettaglio-block-8").remove();
            }

            if($("#it-it-base-parole-value").length == 0) {
                $("#it-it-base-parole").remove();
                $("#it-it-base-parole-space").remove();
            }

            if($("#it-it-base-data-rilascio-value").length == 0) {
                $("#it-it-base-data-rilascio").remove();
                $("#it-base-dati-dettaglio-block-14").remove();
            }

            if($("#it-it-base-conformita-value").length == 0) {
                $("#it-it-base-conformita").remove();
                $("#it-it-base-conformita-space").remove();
            }

            if($("#it-it-base-catalogo-lingua-value").length == 0) {
                $("#it-base-dati-dettaglio-block-38").remove();
                $("#it-base-dati-dettaglio-block-37").remove();
            }
        }


        $("#countDataset").empty();
        let myCount = $("#totalDataset").val();
        $("#countDataset").append( myCount ? myCount : '0' );

        let temi = $('#listaTemi li');
        let cataloghi = $('#listaCataloghi li');
        let formati = $('#listaFormati li');
        let licenze = $('#listaLicenze li');

        /* Bottoni */
        let buttonTemiM = $('#listaTemiLiM');
        let buttonTemiN = $('#listaTemiLiN');

        let buttonCataloghiM = $('#listaCataloghiLiM');
        let buttonCataloghiN = $('#listaCataloghiLiN');

        let buttonFormatiM = $('#listaFormatiLiM');
        let buttonFormatiN = $('#listaFormatiLiN');

        let buttonLicenzeM = $('#listaLicenzeLiM');
        let buttonLicenzeN = $('#listaLicenzeLiN');
        
	let moreButton = $('#more-button-dataset');
	let lessButton = $('#less-button-dataset'); 
	/* ********************** */

	moreButton.on("click", function(){
		$("#accordion2 li").each(function() {
       			if($(this).attr('class') == 'leaf hidden load-more' || $(this).attr('class') == 'leaf load-more hidden') {
          			$(this).removeClass('hidden');
       			} 
		});
		$('#more-button-dataset').addClass('hidden');
		$('#less-button-dataset').css('display', 'block');
	});

	lessButton.on("click", function(){
		$("#accordion2 li").each(function() {
                	if($(this).attr('class') == 'leaf load-more') {
                        	$(this).addClass('hidden');
                        }
                });
	
		$('#more-button-dataset').removeClass('hidden');
		$('#less-button-dataset').css('display', 'none');
	});


        let sizeList = 7;

        let url_page = '/view-dataset'

        let adessoTemi = sizeList;
        let adessoCataloghi = sizeList;
        let adessoFormati = sizeList;
        let adessoLicenze  = sizeList;

        let countFormati = formati.length;
        let countTemi = temi.length;
        let countCataloghi = cataloghi.length;
        let countLicenze = licenze.length;

        /* Nascondo tutti */
        temi.hide();
        cataloghi.hide();
        formati.hide();
        licenze.hide();

        buttonTemiM.hide();
        buttonTemiN.hide();
        buttonCataloghiM.hide();
        buttonCataloghiN.hide();
        buttonFormatiM.hide();
        buttonFormatiN.hide();
        buttonLicenzeM.hide();
        buttonLicenzeN.hide();


        $('#listaTemi li:lt('+sizeList+')').show();
        $('#listaCataloghi li:lt('+sizeList+')').show();
        $('#listaFormati li:lt('+sizeList+')').show();
        $('#listaLicenze li:lt('+sizeList+')').show();

        /* Controllo sei server bottone mostra */
        if( countTemi > sizeList ){ buttonTemiM.show(); }
        if( countFormati > sizeList ){ buttonFormatiM.show(); }
        if( countCataloghi > sizeList ){ buttonCataloghiM.show(); }
        if( countLicenze > sizeList ){ buttonLicenzeM.show(); }

        $('#listaLicenzeButtonM').click(function () {
            adessoLicenze = (adessoLicenze+sizeList <= countLicenze) ? adessoLicenze+sizeList : countLicenze;
            $('#listaLicenze li:lt('+adessoLicenze+')').show();
            if (adessoLicenze >= countLicenze) { buttonLicenzeM.hide(); }
            if (sizeList < adessoLicenze) { buttonLicenzeN.show(); }
        });
        $('#listaLicenzeButtonN').click(function () {
            adessoLicenze = sizeList;
            $('#listaLicenze li').not(':lt('+sizeList+')').hide();
            if (adessoLicenze <= countLicenze) { buttonLicenzeM.show(); }
            buttonLicenzeN.hide();
            $('input[name="licenze"]').prop('checked', false);
        });
        /* Temi */
        $('#listaTemiButtonM').click(function () {
            adessoTemi = (adessoTemi+sizeList <= countTemi) ? adessoTemi+sizeList : countTemi;
            $('#listaTemi li:lt('+adessoTemi+')').show();
            if (adessoTemi >= countTemi) { buttonTemiM.hide(); }
            if (sizeList < adessoTemi) { buttonTemiN.show(); }
        });
        $('#listaTemiButtonN').click(function () {
            adessoTemi = sizeList;
            $('#listaTemi li').not(':lt('+sizeList+')').hide();
            if (adessoTemi <= countTemi) { buttonTemiM.show(); }
            buttonTemiN.hide();
            $('input[name="groups[]"]').prop('checked', false);
        });
        /* Cataloghi */
        $('#listaCataloghiButtonM').click(function () {
            adessoCataloghi = (adessoCataloghi+sizeList <= countCataloghi) ? adessoCataloghi+sizeList : countCataloghi;
            $('#listaCataloghi li:lt('+adessoCataloghi+')').show();
            if (adessoCataloghi >= countCataloghi) { buttonCataloghiM.hide(); }
            if (sizeList < adessoCataloghi) { buttonCataloghiN.show(); }
        });
        $('#listaCataloghiButtonN').click(function () {
            adessoCataloghi = sizeList;
            $('#listaCataloghi li').not(':lt('+sizeList+')').hide();
            if (adessoCataloghi <= countCataloghi) { buttonCataloghiM.show(); }
            buttonCataloghiN.hide();
            $('input[name="cataloghi"]').prop('checked', false);
        });
        /* Formati */
        $('#listaFormatiButtonM').click(function () {
            adessoFormati = (adessoFormati+sizeList <= countFormati) ? adessoFormati+sizeList : countFormati;
            $('#listaFormati li:lt('+adessoFormati+')').show();
            if (adessoFormati >= countFormati) { buttonFormatiM.hide(); }
            if (sizeList < adessoFormati) { buttonFormatiN.show(); }
        });
        $('#listaFormatiButtonN').click(function () {
            adessoFormati = sizeList;
            $('#listaFormati li').not(':lt('+sizeList+')').hide();
            if (adessoFormati <= countFormati) { buttonFormatiM.show(); }
            buttonFormatiN.hide();
            $('input[name="formati"]').prop('checked', false);
        });
        /* Bottoni Clear */
        $('#advancedSearchFormReset').click(function () {
            clearMyForm();
        });
        $('#advancedSearchFormResetG').click(function () {
            clearMyForm();
        });
        /* Bottoni Cerca */
        $('#advancedSearchButton').click(function () {
            creaUrl();
        });
        $('#advancedSearchButtonG').click(function () {
            creaUrl();
        });




        function clearMyForm(){
            $('input[name="licenze"]').prop('checked', false);
            $('input[name="groups[]"]').prop('checked', false);
            $('input[name="cataloghi"]').prop('checked', false);
            $('input[name="formati"]').prop('checked', false);
            $('#testoCerco').val('');
        }

        function creaUrl(){
            let url_groups = '';
            let url_cataloghi = '';
            let url_licenze = '';
            let url_formatti = '';
            let url_cerca = '';
            let url = '';
            /* ********* */
            $('input[name="groups[]"]').each(function () {
                if (this.checked){
                    url_groups = (url_groups === '') ? $(this).val() : url_groups + '|' + $(this).val();
                }
            });
            url_cataloghi = $('input[name="cataloghi"]:checked'). val();
            url_licenze = $('input[name="licenze"]:checked'). val();
            url_formatti = $('input[name="formati"]:checked'). val();
            url_cerca = $('#testoCerco').val();
            /* ******** */
            url = url_cerca ? concatUrl(url, url_cerca, 'Cerca') : url;
            url = url_groups ? concatUrl(url, url_groups, 'groups') : url;
            url = url_cataloghi ? concatUrl(url, url_cataloghi, 'organization') : url;
            url = url_licenze ? concatUrl(url, url_licenze, 'licenze') : url;
            url = url_formatti ? concatUrl(url, url_formatti, 'format') : url;
            url = (url === '') ? url_page : url;
            window.location.href = url;
        }
        function concatUrl(url, urlConcat, typeConcat){
            return (url === '') ? url + url_page + '?' + typeConcat + '=' + urlConcat : url + '&'+ typeConcat +'=' + urlConcat;
        }
    }
  };
})(jQuery, Drupal);
