(function($) {
	$(document).ready( function() {
		$(window).resize(function() {
			$( '.cntctfrm_contact_form.cntctfrm_width_default.cntctfrm_two_columns' ).each( function() {
				var $cntctfrm_form = $( this ),
					$parent = $cntctfrm_form.parent(),
					$cntctfrm_column = $cntctfrm_form.find( '.cntctfrm_column' ),
					margin = 10,
					min_column_width = 200,
					max_column_width = 320,
					min_form_width = min_column_width * 2 + margin,
					max_form_width = max_column_width * 2 + margin;
				if ( $parent.width() < max_form_width  ) {
					var new_column_width = Math.floor( ( $parent.width() - margin ) / 2 );
					if ( new_column_width * 2 + margin >= min_form_width ) {
						$cntctfrm_column.css( 'max-width', new_column_width );
					}
				}
			});
		}).trigger( 'resize' );
	});
})(jQuery);