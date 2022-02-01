/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.bootstrap_barrio = {
    attach: function (context, settings) {

      var position = $(window).scrollTop();
	    $(window).scroll(function() {
        if ($(this).scrollTop() > 50){  
          $('body').addClass("scrolled");
        }
        else{
          $('body').removeClass("scrolled");
        }
        var scroll = $(window).scrollTop();
        if (scroll > position) {
          $('body').addClass("scrolldown");
          $('body').removeClass("scrollup");
        } else {
          $('body').addClass("scrollup");
          $('body').removeClass("scrolldown");
        }
        position = scroll;
      });

      var toggleAffix = function(affixElement, scrollElement, wrapper) {
        var height = affixElement.outerHeight(),
            top = wrapper.offset().top;
        if (scrollElement.scrollTop() >= top){
            wrapper.height(height);
            affixElement.addClass("affix");
        }
        else {
            affixElement.removeClass("affix");
            wrapper.height('auto');
        }
      };
      $('[data-toggle="affix"]').each(function() {
        var ele = $(this),
          wrapper = $('<div></div>');
        ele.before(wrapper);
        $(window).on('scroll resize', function() {
          toggleAffix(ele, $(this), wrapper);
        });
        // init
        toggleAffix(ele, $(window), wrapper);
      });
    }
  };
  var urlParams = new URLSearchParams(window.location.search);
  
 
$('#block-views-block-macro-categorie-block-1 h2').on('click', function(){
$('.carousel-collapsed').toggleClass('collapsed');
}
);
$('.btn-load-more-filter').on('click', function(e){
  e.preventDefault; $(this).parent().siblings('.leaf.load-more').toggleClass('hidden'); 
  $(this).toggleClass('loaded');        
}
);
if(urlParams.has('groups')){
  var group = urlParams.get('groups');
  $( "#views-exposed-form-dataset-list-test-page-1" ).append("<input type='hidden' name='groups' value='"+group+"' />");

}
if(urlParams.has('organization')){
  var organization = urlParams.get('organization');
  $( "#views-exposed-form-dataset-list-test-page-1" ).append("<input type='hidden' name='organization' value='"+organization+"' />");

}
if(urlParams.has('holder_name')){
  var holder = urlParams.get('holder_name');
  $( "#views-exposed-form-dataset-list-test-page-1" ).append("<input type='hidden' name='holder_name' value='"+holder+"' />");

}
if(urlParams.has('format')){
  var format = urlParams.get('format');
  $( "#views-exposed-form-dataset-list-test-page-1" ).append("<input type='hidden' name='format' value='"+format+"' />");

}
if(urlParams.has('licenze')){
  var licenze = urlParams.get('licenze');
  $( "#views-exposed-form-dataset-list-test-page-1" ).append("<input type='hidden' name='licenze' value='"+licenze+"' />");

}

})(jQuery, Drupal);
