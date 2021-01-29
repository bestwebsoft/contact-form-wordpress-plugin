(function($) {
	$(document).ready( function() {
		$( '#cntctfrm_gdpr' ).on( 'change', function() {
			if( $( this).is( ':checked' ) ) {
				$( '#cntctfrm_gdpr_link_options' ).show();
			} else {
				$( '#cntctfrm_gdpr_link_options' ).hide();
			}
		} ).trigger( 'change' );

		$( '#cntctfrm_show_multi_notice' ).removeAttr('href title').css('cursor', 'pointer');

		$( '#cntctfrm_change_label' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.cntctfrm_change_label_block' ).show();
			} else {
				$( '.cntctfrm_change_label_block' ).hide();
			}
		});

		$( 'input[name="cntctfrm_custom_email"]' ).focus( function() {
			$( '#cntctfrm_select_email_custom' ).attr( 'checked', 'checked' );
		});

		$( 'input[name="cntctfrm_from_field"]' ).focus( function() {
			$( this ).trigger( 'change' );
			$( '#cntctfrm_select_from_custom_field' ).attr( 'checked', 'checked' );
		});

		$( 'input[name="cntctfrm_custom_from_email"]' ).focus( function() {
			$( this ).trigger( 'change' );
			$( '#cntctfrm_from_custom_email' ).attr( 'checked', 'checked' );
		});

		$( 'input[name^="cntctfrm_thank_text"]' ).focus( function() {
			$( '#cntctfrm_action_after_send' ).attr( 'checked', 'checked' );
		});

		$( 'input[name="cntctfrm_redirect_url"]' ).focus( function() {
			$( '#cntctfrm_action_after_send_url' ).attr( 'checked', 'checked' );
		});

		$( '#cntctfrm_display_add_info' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.cntctfrm_display_add_info_block' ).show();
			} else {
				$( '.cntctfrm_display_add_info_block' ).hide();
			}
		});
		$( '#cntctfrm_attachment' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( '.cntctfrm-multi-attachment, #cntctfrm-attachment-explanations' ).show();
			} else {
				$( '.cntctfrm-multi-attachment, #cntctfrm-attachment-explanations' ).hide();
			}
		});
		$( '#cntctfrm_add_language_button' ).click( function( event ) {
			event = event || window.event;
			event.preventDefault();
			var lang_val = $( '#cntctfrm_languages' ).val();
			if ( '0' !== lang_val ) {
				$.ajax({
					url: '../wp-admin/admin-ajax.php',/* update_url, */
					type: "POST",
					data: "action=cntctfrm_add_language&lang=" + lang_val + '&cntctfrm_ajax_nonce_field=' + cntctfrm_ajax.cntctfrm_nonce,
					success: function( result ) {
						var text = $.parseJSON( result );
						var lang_val = $( '#cntctfrm_languages' ).val();
						$( '.cntctfrm_change_label_block .cntctfrm_language_tab, .cntctfrm_action_after_send_block .cntctfrm_language_tab' ).each( function() {
							$( this ).addClass( 'hidden' );
						});
						$( '.cntctfrm_change_label_block .cntctfrm_language_tab' ).first().clone().appendTo( '.cntctfrm_change_label_block' ).removeClass( 'hidden' ).removeClass( 'cntctfrm_tab_default' ).addClass( 'cntctfrm_tab_' + lang_val );
						$( '.cntctfrm_action_after_send_block .cntctfrm_language_tab' ).first().clone().insertBefore( '#cntctfrm_before' ).removeClass( 'hidden' ).removeClass( 'cntctfrm_tab_default' ).addClass( 'cntctfrm_tab_' + lang_val );
						$( '.cntctfrm_change_label_block .cntctfrm_language_tab' ).last().find( 'input' ).each( function() {
							$( this ).val( '' );
							$( this ).attr( 'name', $( this ).attr( 'name' ).replace( '[default]', '[' + lang_val + ']' ) );
						});
						$( '.cntctfrm_change_label_block .cntctfrm_language_tab' ).last().find( '.cntctfrm_shortcode_for_language' ).last().html( text );
						$( '.cntctfrm_action_after_send_block .cntctfrm_language_tab' ).last().find( 'input' ).val( '' ).attr( 'name', $( '.cntctfrm_action_after_send_block .cntctfrm_language_tab' ).last().find( 'input' ).attr( 'name' ).replace( '[default]', '[' + lang_val + ']' ) );
						$( '.cntctfrm_action_after_send_block .cntctfrm_language_tab' ).last().find( '.cntctfrm_shortcode_for_language' ).last().html( text );
						$( '.cntctfrm_change_label_block .cntctfrm_label_language_tab, .cntctfrm_action_after_send_block .cntctfrm_label_language_tab' ).each( function() {
							$( this ).removeClass( 'cntctfrm_active' );
						});
						$( '.cntctfrm_change_label_block .clear' ).prev().clone().attr( 'id', 'cntctfrm_label_' + lang_val ).addClass( 'cntctfrm_active' ).html( $( '#cntctfrm_languages option:selected' ).text() + ' <span class="cntctfrm_delete" rel="' + lang_val + '">X</span>').insertBefore( '.cntctfrm_change_label_block .clear' );
						$( '.cntctfrm_action_after_send_block .clear' ).prev().clone().attr( 'id', 'cntctfrm_text_' + lang_val ).addClass( 'cntctfrm_active' ).html( $( '#cntctfrm_languages option:selected' ).text() + ' <span class="cntctfrm_delete" rel="' + lang_val + '">X</span>').insertBefore( '.cntctfrm_action_after_send_block .clear' );
						$( '#cntctfrm_languages option:selected' ).remove();
						$( '#cntctfrm_change_label' ).prop( 'checked', true );
						$( '.cntctfrm_change_label_block' ).show();
					},
					error: function( request, status, error ) {
						alert( error + request.status );
					}
				});
			}
		});
		$( '.cntctfrm_language_tab_block' ).css( 'display', 'none' );
		$( '.cntctfrm_language_tab_block_mini' ).css( 'display', 'block' );

		$( 'select[name="cntctfrm_user_email"]').focus( function() {
			$('#cntctfrm_select_email_user').attr( 'checked', 'checked' );
		});

		/* change form layout in the settings page appearance tab */
		$( 'input[name="cntctfrm_width_type"]' ).change( function() {
			cntctfrm_change_width();
		});

		function cntctfrm_change_width() {
			var $form = $( '#cntctfrm_contact_form' ),
				width = $( 'input[name="cntctfrm_width_type"]' ).filter( ':checked' ).val();

			if ( 'custom' != width ) {
				$form.attr( 'class', function() {
					return this.className = this.className.replace( 'cntctfrm_width_custom', 'cntctfrm_width_default' );
				});
				$( '.cntctfrm_width_params' ).hide();
			} else {
				$form.attr( 'class', function() {
					return this.className = this.className.replace( 'cntctfrm_width_default', 'cntctfrm_width_custom' );
				});
				$( '.cntctfrm_width_params' ).show();
			}
		}

		$( 'input[name="cntctfrm_width_type"]' ).change( function() {
			if ( 'custom' != $( this ).filter( ':checked' ).val() ) {
				$( '.cntctfrm_width_params' ).hide();
			} else {
				$( '.cntctfrm_width_params' ).show();
			}
		}).trigger( 'change' );

		$( 'input[name="cntctfrm_layout"]' ).change( function() {
			var form_layout = $( this ).val();
			if ( form_layout == 1 ) {
				$( '#cntctfrm_settings_form #cntctfrm_contact_form' ).removeClass( 'cntctfrm_two_columns' );
				$( '#cntctfrm_settings_form #cntctfrm_contact_form' ).addClass( 'cntctfrm_one_column' );
				if( $( '#cntctfrm_second_column li' ).length > 0 ) {
					$( '#cntctfrm_first_column' ).append( $( '#cntctfrm_second_column' ).html() );
				}
				$( '#cntctfrm_second_column' ).html( '' );
				$( '#cntctfrm_second_column' ).hide();
			}
			if ( form_layout == 2 ) {
				$( '#cntctfrm_settings_form #cntctfrm_contact_form' ).removeClass( 'cntctfrm_one_column' );
				$( '#cntctfrm_settings_form #cntctfrm_contact_form' ).addClass( 'cntctfrm_two_columns' );
				$( '#cntctfrm_second_column' ).show();
			}

			$( '#cntctfrm_first_column, #cntctfrm_second_column' ).addClass( 'cntctfrm_column_placeholder' );
			$( '#cntctfrm_second_column' ).css( 'height', $( '#cntctfrm_first_column' ).height() );
			setTimeout( function() {
				$( '#cntctfrm_first_column, #cntctfrm_second_column' ).removeClass( 'cntctfrm_column_placeholder' );
				$( '#cntctfrm_second_column' ).css( 'height', 'auto' );
			}, 1000 );
		});

		/* change submit position in the settings page appearance tab */
		$( 'input[name="cntctfrm_submit_position"]' ).data( 'prev_val', $( 'input[name="cntctfrm_submit_position"]:checked' ).val() );
		$( 'input[name="cntctfrm_submit_position"]' ).change( function() {
			var current_position = $( this ).val(),
				prev_position = $( this ).data( 'prev_val' ),
				direction = $( '.cntctfrm_contact_form' ).hasClass( 'cntctfrm_rtl' ) ? 'rtl' : 'ltr';
				submit = {
					'ltr' : {
						'left'  : '#cntctfrm_submit_first_column',
						'right' : '#cntctfrm_submit_second_column'
					},
					'rtl' : {
						'left'  : '#cntctfrm_submit_second_column',
						'right' : '#cntctfrm_submit_first_column'
					}
				},
				html = $( submit[ direction ][ prev_position ] ).html();
			$( submit[ direction ][ current_position ] ).html( html );
			$( submit[ direction ][ prev_position ] ).html( '' );
			$( 'input[name="cntctfrm_submit_position"]' ).data( 'prev_val', current_position );
			$( '.cntctfrm_input_submit' ).attr( 'style', 'text-align: ' + current_position + ' !important' );
		});

		/* sorting fields in the settings page appearance tab */
		$( "#cntctfrm_first_column, #cntctfrm_second_column" ).sortable({
			items      : 'li',
			connectWith: '.cntctfrm_column',
			scroll: false,
			start: function ( e, ui ) {
				$( '#cntctfrm_first_column, #cntctfrm_second_column' ).addClass( 'cntctfrm_column_placeholder' );
				$( '#cntctfrm_first_column, #cntctfrm_second_column' ).css( 'padding-bottom', 1 );
			},
			stop: function ( e, ui ) {
				$( '#cntctfrm_first_column, #cntctfrm_second_column' ).removeClass( 'cntctfrm_column_placeholder' );
			},
			update: function ( e, ui ) {
				var fields_first_column = fields_second_column = '';

				$( '#cntctfrm_first_column .cntctfrm_field_wrap' ).each( function() {
					fields_first_column += $( this ).find( 'input, select, textarea' ).filter( ':first' ).attr( 'name' ) + ',';
				});
				fields_first_column = fields_first_column.substring( 0, fields_first_column.length - 1 );

				$( '#cntctfrm_second_column .cntctfrm_field_wrap' ).each( function() {
					fields_second_column += $( this ).find( 'input, select, textarea' ).filter( ':first' ).attr( 'name' ) + ',';
				});
				fields_second_column = fields_second_column.substring( 0, fields_second_column.length - 1 );

				$( '#cntctfrm_layout_first_column' ).val( fields_first_column );
				$( '#cntctfrm_layout_second_column' ).val( fields_second_column );

				if( typeof bws_show_settings_notice == 'function' ) {
					bws_show_settings_notice();
				}
			}
		}).disableSelection();
	});
	$(document).on( "click", ".cntctfrm_language_tab_block_mini", function() {
		if ( $( '.cntctfrm_language_tab_block' ).css( 'display' ) == 'none' ) {
			$( '.cntctfrm_language_tab_block' ).css( 'display', 'block' );
			$( '.cntctfrm_language_tab_block_mini' ).addClass( 'cntctfrm_language_tab_block_mini_open' );
		} else {
			$( '.cntctfrm_language_tab_block' ).css( 'display', 'none' );
			$( '.cntctfrm_language_tab_block_mini' ).removeClass( 'cntctfrm_language_tab_block_mini_open' );
		}
	});
	$(document).on( "click", ".cntctfrm_change_label_block .cntctfrm_label_language_tab", function() {
		$( '.cntctfrm_label_language_tab' ).each( function() {
			$( this ).removeClass( 'cntctfrm_active' );
		});
		var index = $( '.cntctfrm_change_label_block .cntctfrm_label_language_tab' ).index( $( this ) );
		$( this ).addClass( 'cntctfrm_active' );
		var blocks = $( '.cntctfrm_action_after_send_block .cntctfrm_label_language_tab' );
		$( blocks[ index ] ).addClass( 'cntctfrm_active' );
		$( '.cntctfrm_language_tab' ).each( function() {
			$( this ).addClass( 'hidden' );
		});
		$( '.' + this.id.replace( 'label', 'tab' ) ).removeClass( 'hidden' );
	});
	$(document).on( "click", ".cntctfrm_action_after_send_block .cntctfrm_label_language_tab", function() {
		$( '.cntctfrm_label_language_tab' ).each( function() {
			$( this ).removeClass( 'cntctfrm_active' );
		});
		var index = $( '.cntctfrm_action_after_send_block .cntctfrm_label_language_tab' ).index( $( this ) );
		$( this ).addClass( 'cntctfrm_active' );
		var blocks = $( '.cntctfrm_change_label_block .cntctfrm_label_language_tab' );
		$( blocks[ index ] ).addClass( 'cntctfrm_active' );
		$( '.cntctfrm_language_tab' ).each( function() {
			$( this ).addClass( 'hidden' );
		});
		$( '.' + this.id.replace( 'text', 'tab' ) ).removeClass( 'hidden' );
	});
	$(document).on( "click", ".cntctfrm_delete", function( event ) {
		event.stopPropagation();
		if ( confirm( cntctfrm_ajax.cntctfrm_confirm_text ) ) {
			var lang = $( this ).attr( 'rel' );
			$.ajax({
				url: '../wp-admin/admin-ajax.php',/* update_url, */
				type: "POST",
				data: "action=cntctfrm_remove_language&lang=" + lang + '&cntctfrm_ajax_nonce_field=' + cntctfrm_ajax.cntctfrm_nonce,
				success: function( result ) {
					$( '#cntctfrm_label_' + lang + ', #cntctfrm_text_' + lang + ', .cntctfrm_tab_' + lang ).each( function() {
						$( this ).remove();
					});
					$( '.cntctfrm_change_label_block .cntctfrm_label_language_tab' ).removeClass( 'cntctfrm_active' ).first().addClass( 'cntctfrm_active' );
					$( '.cntctfrm_action_after_send_block .cntctfrm_label_language_tab' ).removeClass( 'cntctfrm_active' ).first().addClass( 'cntctfrm_active' );
					$( '.cntctfrm_change_label_block .cntctfrm_language_tab' ).addClass( 'hidden' ).first().removeClass( 'hidden' );
					$( '.cntctfrm_action_after_send_block .cntctfrm_language_tab' ).addClass( 'hidden' ).first().removeClass( 'hidden' );
				},
				error: function( request, status, error ) {
					alert( error + request.status );
				}
			});
		}
	});
})(jQuery);