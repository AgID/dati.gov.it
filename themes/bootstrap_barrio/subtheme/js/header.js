(function($, Drupal){
$(window).bind('scroll', function () {
  'use strict';
    if ($(window).scrollTop() > 50) {
      $(".header").addClass('header-scrolled');
      $(".header").css({
          "position" : "fixed",
          "transform" : "translateY(-48px)"
      });

    $(".it-header-center-wrapper").css("height","70px");

    $(".it-brand-wrapper img").css({
      "width": "56px",
       "transition": "all 0.2s ease 0s"
       
    });

    $(".it-brand-text h2").css({
      "font-size": "24px",
      "transition": "all 0.2s ease 0s",
      // "margin-top": "5px"
    });

    $(".it-brand-text h3").css({
      "opacity": "0",
      "height": "0",
      "transition": "all 0.2s ease 0s"
    });

    $(".it-socials").css({
      "opacity": "0",
      "transition" : "all 0.2s ease 0s"});

    $(".it-search-wrapper").css(
      "transform", "translateY(-25px)");

  } else {
    $(".header").removeClass('header-scrolled');
      $(".header").css({
        "position" : "relative",
        "transform" : "translateY(0px)"
        });

      $(".it-header-center-wrapper").css("height","120px");

      $(".it-brand-wrapper img").css({
        "width": "71px",
         "transition": "all 0.2s ease 0s",
         "padding-top": "0px"
      });

      $(".it-brand-text h2").css({
        "font-size": "32px",
        "transition": "all 0.2s ease 0s",
        "margin-top": "0px"
      });

      $(".it-brand-text h3").css({
        "opacity": "1",
        "height":"auto",
        "transition": "all 0.2s ease 0s"
      });

      $(".it-socials").css({
        "opacity": "1",
        "transition" : "all 0.2s ease 0s"});

      $(".it-search-wrapper").css("transform", "translateY(0px)");
     }
    //  else {
    //
    //   $(".header").css({
    //         "position" : "relative",
    //         "transform" : "translateY(0px)"
    //     });
    //
    // }
});
})(jQuery, Drupal);
