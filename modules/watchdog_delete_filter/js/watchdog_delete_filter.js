(function ($, Drupal) {

	Drupal.behaviors.watchdog_delete_filter = {
	  attach: function (context, settings) {

	    // Attach a click listener to the 'select all' button.
	    var clearBtn = document.getElementById('select-all-button');

	    clearBtn.addEventListener('click', function() {

				$('#select-type option').each(function() {
				  $(this).prop('selected', true);
				});
				$('#select-severity option').each(function() {
				  $(this).prop('selected', true);
				});

	    }, false);
	  }
	};

})(jQuery, Drupal);