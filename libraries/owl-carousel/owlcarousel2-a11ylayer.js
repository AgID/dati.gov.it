/**
 * Owl Carousel v2 Accessibility Plugin
 * Version 0.2.1
 * © Geoffrey Roberts 2016
 */

;(function(jQuery, window, document){
  var Owl2A11y = function(carousel) {
    this._core = carousel;
    this._initialized = false;

    this._core._options = jQuery.extend(Owl2A11y.defaults, this._core.options);

    this.jQueryelement = this._core.jQueryelement;

    var setCurrent = jQuery.proxy(function(e) {
      this.setCurrent(e);
    }, this);

    this._handlers = {
      'initialized.owl.carousel': jQuery.proxy(function(e) {
        this.setupRoot();
        if (e.namespace && !this._initialized) {
          this.setupFocus();
          this.setupKeyboard();
        }
        this.setCurrent(e);
      }, this),
      'changed.owl.carousel': setCurrent,
      'translated.owl.carousel': setCurrent,
      'refreshed.owl.carousel': setCurrent,
      'resized.owl.carousel': setCurrent
    };
    this.jQueryelement.on(this._handlers);
  };


  /* PREFERENCES */

  /**
   * Contains default parameters, if there were any.
   */
  Owl2A11y.defaults = {};


  /* EVENT HANDLERS */

  /**
   * Adds support for things that don't map nicely to the root object
   * such as event handlers.
   */
  Owl2A11y.eventHandlers = {};

  /**
   * Get a callback for keyup events within this carousel.
   *
   * @return callback
   *   An event callback that takes an Event as an argument.
   */
  Owl2A11y.prototype.getDocumentKeyUp = function(){
    var self = this;
    return function(e) {
      var eventTarg = jQuery(e.target),
      targ = self.focused(eventTarg),
      action = null;

      if (!!targ) {
        if (e.keyCode == 37 || e.keyCode == 38) {
          action = 'prev.owl.carousel';
        }
        else if (e.keyCode == 39 || e.keyCode == 40) {
          action = 'next.owl.carousel';
        }
        else if (e.keyCode == 13) {
          if (eventTarg.hasClass('owl-prev')) action = 'prev.owl.carousel';
          else if (eventTarg.hasClass('owl-next')) action = 'next.owl.carousel';
          else if (eventTarg.hasClass('owl-dot')) action = 'click';
        }

        if (!!action) targ.trigger(action);
      }
    };
  };


  /* SETUP AND TEAR DOWN */

  /**
   * Assign attributes to the root element.
   */
  Owl2A11y.prototype.setupRoot = function() {
    this.jQueryelement.attr({
      role: 'listbox',
      tabindex: '0'
    });
  };

  /**
   * Setup keyboard events for this carousel.
   */
  Owl2A11y.prototype.setupKeyboard = function(){
    // Only needed to initialise once for the entire document
    if (!this.jQueryelement.attr('data-owl-access-keyup')) {
      this.jQueryelement.bind('keyup', this.getDocumentKeyUp())
      .attr('data-owl-access-keyup', '1');
    }
    this.jQueryelement.attr('data-owl-carousel-focusable', '1');
  };

  /**
   * Setup focusing behaviour for the carousel.
   */
  Owl2A11y.prototype.setupFocus = function(){
    // Only needed to initialise once for the entire document
    this.jQueryelement.bind('focusin', function(){
      jQuery(this).attr({
        'data-owl-carousel-focused': '1',
        'aria-live': 'polite'
      }).trigger('stop.owl.autoplay');
    }).bind('focusout', function(){
      jQuery(this).attr({
        'data-owl-carousel-focused': '0',
        'aria-live': 'off'
      }).trigger('play.owl.autoplay');
    });

    // Add tabindex to allow navigation to be focused.
    if (!!this._core._plugins.navigation) {
      var navPlugin = this._core._plugins.navigation,
      toFocus = [];
      if (!!navPlugin._controls.jQueryprevious) {
        toFocus.push(navPlugin._controls.jQueryprevious);
      }
      if (!!navPlugin._controls.jQuerynext) {
        toFocus.push(navPlugin._controls.jQuerynext);
      }
      if (!!navPlugin._controls.jQueryindicators) {
        toFocus.push(navPlugin._controls.jQueryindicators.children());
      }
      jQuery.each(toFocus, function(){
        this.attr('tabindex', '0');
      });
    }
  };

  /**
   * Assign attributes to the root element.
   */
  Owl2A11y.prototype.destroy = function() {
    this.jQueryelement.unbind('keyup', this.eventHandlers.documentKeyUp)
    .removeAttr('data-owl-access-keyup data-owl-carousel-focusable')
    .unbind('focusin focusout');
  };


  /* HELPER FUNCTIONS */

  /**
   * Identifies all focusable elements within a given element.
   *
   * @param DOMElement elem
   *   A DOM element.
   *
   * @return jQuery
   *   A jQuery object that may refer to zero or more focusable elements.
   */
  Owl2A11y.prototype.focusableElems = function(elem) {
    return jQuery(elem).find('a, input, select, button, *[tabindex]');
  };

  /**
   * Identifies all focusable elements within a given element.
   *
   * @param jQeury elems
   *   A jQuery object that may refer to zero or more focusable elements.
   * @param boolean enable
   *   Whether focus is to be enabled on these elements or not.
   */
  Owl2A11y.prototype.adjustFocus = function(elems, enable){
    elems.each(function(){
      var item = jQuery(this);
      var newTabIndex = '0',
      storeTabIndex = '0';

      currentTabIndex = item.attr('tabindex'),
      storedTabIndex = item.attr('data-owl-temp-tabindex');

      if (enable) {
        newTabIndex = (
          typeof(storedTabIndex) != 'undefined' && (storedTabIndex != '-1') ?
          item.attr('data-owl-temp-tabindex') :
          '0'
        );
        storedTabIndex = newTabIndex;
      }
      else {
        newTabIndex = '-1';
        storedTabIndex = (
          (typeof(currentTabIndex) != 'undefined') || (currentTabIndex != '-1') ?
          currentTabIndex :
          '0'
        );
      }

      item.attr({
        tabindex: newTabIndex,
        'data-owl-temp-tabindex': storeTabIndex
      });
    });
  };

  /**
   * Get the root element if we are focused within it.
   *
   * @param DOMElement targ
   *   An element that might be within this carousel.
   *
   * @return mixed
   *   Either the jQuery element containing the root element, or NULL.
   */
  Owl2A11y.prototype.focused = function(targ){
    var targ = jQuery(targ);
    if (targ.attr('data-owl-carousel-focused') == 1) {
      return targ;
    }
    var closest = targ.closest('[data-owl-carousel-focused="1"]');
    if (closest.length > 0) return closest;
    return null;
  };


  /* UPDATE FUNCTIONS */

  /**
   * Identify active elements, set WAI-ARIA sttributes accordingly,
   * scroll to show element if we need to, and set up focusing.
   *
   * @param Event e
   *   The triggering event.
   */
  Owl2A11y.prototype.setCurrent = function(e) {
    var targ = this.focused(jQuery(':focus')),
    element = this._core.jQueryelement,
    stage = this._core.jQuerystage,
    focusableElems = this.focusableElems,
    adjustFocus = this.adjustFocus;

    if (!!stage) {
      var offs = stage.offset();
      if (!!targ) {
        window.scrollTo(
          offs.left,
          offs.top - parseInt(jQuery('body').css('padding-top'), 10)
        );
      }

      this._core.jQuerystage.children().each(function(i) {
        var item = jQuery(this);
        var focusable = focusableElems(this);

        // Use the active class to determine if we can see it or not.
        // Pretty lazy, but the Owl API doesn't make it easy to tell
        // from indices alone.
        if (item.hasClass('active')) {
          item.attr('aria-hidden', 'false');
          adjustFocus(focusable, true);
        }
        else {
          item.attr('aria-hidden', 'true');
          adjustFocus(focusable, false);
        }
      });

      if (!!targ) {
        // Focus on the root element after we're done moving,
        // but only if we're not using the controls.
        setTimeout(function(){
          var newFocus = element;
          if (jQuery(':focus').closest('.owl-controls').length) {
            newFocus = jQuery(':focus');
          }
          newFocus.focus();
        }, 250);
      }
    }
  };
setTimeout(() => {
  if(jQuery.fn.owlCarousel.constructor.Plugins == undefined)
  jQuery.fn.owlCarousel.constructor.Plugins = [];
    jQuery.fn.owlCarousel.constructor.Plugins['Owl2A11y'] = Owl2A11y;
}, 500);
})(window.Zepto || window.jQuery, window,  document);
