<?php
/**
 * Contains the list of the deprecated functions
 * @since 4.0.2
 */

/**
 * @since 2.1.1
 * @todo delete after 25.04.2017
 */
if ( ! function_exists( 'cntctfrm_options_update' ) ) {
	function cntctfrm_options_update( $cntctfrm_options, $cntctfrm_option_defaults, $contact_form_multi_active ) {
		global $cntctfrm_related_plugins;

		foreach ( $cntctfrm_option_defaults as $key => $value ) {
			if ( isset( $cntctfrm_options['cntctfrm_' . $key ] ) ) {
				$cntctfrm_options[ $key ] = $cntctfrm_options[ 'cntctfrm_' . $key ];
				unset( $cntctfrm_options[ 'cntctfrm_' . $key ] );
			}
		}

		foreach ( array( 'display_captcha', 'display_google_captcha', 'display_subscribe', 'save_email_to_db' ) as $key ) {
			if ( isset( $cntctfrm_options['cntctfrm_' . $key ] ) ) {
				$cntctfrm_options[ $key ] = $cntctfrm_options[ 'cntctfrm_' . $key ];
				unset( $cntctfrm_options[ 'cntctfrm_' . $key ] );
			}
		}

		if ( $contact_form_multi_active ) {
			if ( empty( $cntctfrm_related_plugins ) )
				cntctfrm_related_plugins();

			if ( ! isset( $cntctfrm_options['display_captcha'] ) ) {
				if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) )
					$cntctfrm_options['display_captcha'] = $cntctfrm_related_plugins['captcha']['enabled'];
				else
					$cntctfrm_options['display_captcha'] = false;
			}
			if ( ! isset( $cntctfrm_options['display_google_captcha'] ) ) {
				if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) )
					$cntctfrm_options['display_google_captcha'] = $cntctfrm_related_plugins['google-captcha']['options']['contact_form'];
				else
					$cntctfrm_options['display_google_captcha'] = false;
			}
			if ( ! isset( $cntctfrm_options['display_subscribe'] ) ) {
				if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) )
					$cntctfrm_options['display_subscribe'] = $cntctfrm_related_plugins['subscriber']['options']['contact_form'];
				else
					$cntctfrm_options['display_subscribe'] = false;
			}
			if ( ! isset( $cntctfrm_options['save_email_to_db'] ) ) {
				if ( array_key_exists( 'contact-form-to-db', $cntctfrm_related_plugins ) )
					$cntctfrm_options['save_email_to_db'] = ! empty( $cntctfrm_related_plugins['contact-form-to-db']['options'][ $cntctfrm_related_plugins['contact-form-to-db']['save_option'] ] ) ? true : false;
				else
					$cntctfrm_options['save_email_to_db'] = false;
			}
		}

		if ( ! isset( $cntctfrm_options['cntctfrm_name_label']['default'] ) && isset( $cntctfrm_options['cntctfrm_name_label']['en'] ) ) {
			$cntctfrm_options['cntctfrm_name_label']['default'] = $cntctfrm_options['cntctfrm_name_label']['en'];
			unset( $cntctfrm_options['cntctfrm_name_label']['en'] );
			$cntctfrm_options['cntctfrm_address_label']['default'] = $cntctfrm_options['cntctfrm_address_label']['en'];
			unset( $cntctfrm_options['cntctfrm_address_label']['en'] );
			$cntctfrm_options['cntctfrm_email_label']['default'] = $cntctfrm_options['cntctfrm_email_label']['en'];
			unset( $cntctfrm_options['cntctfrm_email_label']['en'] );
			$cntctfrm_options['cntctfrm_phone_label']['default'] = $cntctfrm_options['cntctfrm_phone_label']['en'];
			unset( $cntctfrm_options['cntctfrm_phone_label']['en'] );
			$cntctfrm_options['cntctfrm_subject_label']['default'] = $cntctfrm_options['cntctfrm_subject_label']['en'];
			unset( $cntctfrm_options['cntctfrm_subject_label']['en'] );
			$cntctfrm_options['cntctfrm_message_label']['default'] = $cntctfrm_options['cntctfrm_message_label']['en'];
			unset( $cntctfrm_options['cntctfrm_message_label']['en'] );
			$cntctfrm_options['cntctfrm_attachment_label']['default'] = $cntctfrm_options['cntctfrm_attachment_label']['en'];
			unset( $cntctfrm_options['cntctfrm_attachment_label']['en'] );
			$cntctfrm_options['cntctfrm_attachment_tooltip']['default'] = $cntctfrm_options['cntctfrm_attachment_tooltip']['en'];
			unset( $cntctfrm_options['cntctfrm_attachment_tooltip']['en'] );
			$cntctfrm_options['cntctfrm_send_copy_label']['default'] = $cntctfrm_options['cntctfrm_send_copy_label']['en'];
			unset( $cntctfrm_options['cntctfrm_send_copy_label']['en'] );
			$cntctfrm_options['cntctfrm_submit_label']['default'] = $cntctfrm_options['cntctfrm_submit_label']['en'];
			unset( $cntctfrm_options['cntctfrm_submit_label']['en'] );
			$cntctfrm_options['cntctfrm_name_error']['default'] = $cntctfrm_options['cntctfrm_name_error']['en'];
			unset( $cntctfrm_options['cntctfrm_name_error']['en'] );
			$cntctfrm_options['cntctfrm_address_error']['default'] = $cntctfrm_options['cntctfrm_address_error']['en'];
			unset( $cntctfrm_options['cntctfrm_address_error']['en'] );
			$cntctfrm_options['cntctfrm_email_error']['default'] = $cntctfrm_options['cntctfrm_email_error']['en'];
			unset( $cntctfrm_options['cntctfrm_email_error']['en'] );
			$cntctfrm_options['cntctfrm_phone_error']['default'] = $cntctfrm_options['cntctfrm_phone_error']['en'];
			unset( $cntctfrm_options['cntctfrm_phone_error']['en'] );
			$cntctfrm_options['cntctfrm_subject_error']['default'] = $cntctfrm_options['cntctfrm_subject_error']['en'];
			unset( $cntctfrm_options['cntctfrm_subject_error']['en'] );
			$cntctfrm_options['cntctfrm_message_error']['default'] = $cntctfrm_options['cntctfrm_message_error']['en'];
			unset( $cntctfrm_options['cntctfrm_message_error']['en'] );
			$cntctfrm_options['cntctfrm_attachment_error']['default'] = $cntctfrm_options['cntctfrm_attachment_error']['en'];
			unset( $cntctfrm_options['cntctfrm_attachment_error']['en'] );
			$cntctfrm_options['cntctfrm_attachment_upload_error']['default'] = $cntctfrm_options['cntctfrm_attachment_upload_error']['en'];
			unset( $cntctfrm_options['cntctfrm_attachment_upload_error']['en'] );
			$cntctfrm_options['cntctfrm_attachment_move_error']['default'] = $cntctfrm_options['cntctfrm_attachment_move_error']['en'];
			unset( $cntctfrm_options['cntctfrm_attachment_move_error']['en'] );
			$cntctfrm_options['cntctfrm_attachment_size_error']['default'] = $cntctfrm_options['cntctfrm_attachment_size_error']['en'];
			unset( $cntctfrm_options['cntctfrm_attachment_size_error']['en'] );
			$cntctfrm_options['cntctfrm_captcha_error']['default'] = $cntctfrm_options['cntctfrm_captcha_error']['en'];
			unset( $cntctfrm_options['cntctfrm_captcha_error']['en'] );
			$cntctfrm_options['cntctfrm_form_error']['default'] = $cntctfrm_options['cntctfrm_form_error']['en'];
			unset( $cntctfrm_options['cntctfrm_form_error']['en'] );
			$cntctfrm_options['cntctfrm_thank_text']['default'] = $cntctfrm_options['cntctfrm_thank_text']['en'];
			unset( $cntctfrm_options['cntctfrm_thank_text']['en'] );
		}

		if ( empty( $cntctfrm_options['cntctfrm_language'] ) && isset( $cntctfrm_options['cntctfrm_name_label'] ) &&  ! is_array( $cntctfrm_options['cntctfrm_name_label'] ) ) {
			$cntctfrm_options['cntctfrm_name_label']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_name_label']['default'] );
			$cntctfrm_options['cntctfrm_address_label']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_address_label']['default'] );
			$cntctfrm_options['cntctfrm_email_label']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_email_label']['default'] );
			$cntctfrm_options['cntctfrm_phone_label']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_phone_label']['default'] );
			$cntctfrm_options['cntctfrm_subject_label']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_subject_label']['default'] );
			$cntctfrm_options['cntctfrm_message_label']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_message_label']['default'] );
			$cntctfrm_options['cntctfrm_attachment_label']			= array( 'default' => $cntctfrm_option_defaults['cntctfrm_attachment_label']['default'] );
			$cntctfrm_options['cntctfrm_attachment_tooltip']		= array( 'default' => $cntctfrm_option_defaults['cntctfrm_attachment_tooltip']['default'] );
			$cntctfrm_options['cntctfrm_send_copy_label']			= array( 'default' => $cntctfrm_option_defaults['cntctfrm_send_copy_label']['default'] );
			$cntctfrm_options['cntctfrm_thank_text']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_thank_text']['default'] );
			$cntctfrm_options['cntctfrm_submit_label']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_submit_label']['default'] );
			$cntctfrm_options['cntctfrm_name_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_name_error']['default'] );
			$cntctfrm_options['cntctfrm_address_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_address_error']['default'] );
			$cntctfrm_options['cntctfrm_email_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_email_error']['default'] );
			$cntctfrm_options['cntctfrm_phone_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_phone_error']['default'] );
			$cntctfrm_options['cntctfrm_subject_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_subject_error']['default'] );
			$cntctfrm_options['cntctfrm_message_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_message_error']['default'] );
			$cntctfrm_options['cntctfrm_attachment_error']			= array( 'default' => $cntctfrm_option_defaults['cntctfrm_attachment_error']['default'] );
			$cntctfrm_options['cntctfrm_attachment_upload_error']	= array( 'default' => $cntctfrm_option_defaults['cntctfrm_attachment_upload_error']['default'] );
			$cntctfrm_options['cntctfrm_attachment_move_error']		= array( 'default' => $cntctfrm_option_defaults['cntctfrm_attachment_move_error']['default'] );
			$cntctfrm_options['cntctfrm_attachment_size_error']		= array( 'default' => $cntctfrm_option_defaults['cntctfrm_attachment_size_error']['default'] );
			$cntctfrm_options['cntctfrm_captcha_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_captcha_error']['default'] );
			$cntctfrm_options['cntctfrm_form_error']				= array( 'default' => $cntctfrm_option_defaults['cntctfrm_form_error']['default'] );
		}

		return $cntctfrm_options;
	}
}

/**
 * Display message about deprecated shortcode
 * @since 4.0.3
 * @todo delete after 01.04.2017
 */
if ( ! function_exists( 'cntctfrm_display_deprecated_shortcode_message' ) ) {
	function cntctfrm_display_deprecated_shortcode_message() {
		global $cntctfrm_plugin_info;

		/* get options to avoid conflict with CF Multi */
		$options = get_option( 'cntctfrm_options' );

		if ( empty( $options['deprecated_shortcode'] ) )
			return '';

		if ( isset( $_GET['cntctfrm_nonce'] ) &&  wp_verify_nonce( $_GET['cntctfrm_nonce'], 'cntctfrm_clean_deprecated' ) ) {
			unset( $options['deprecated_shortcode'] );
			update_option( 'cntctfrm_options', $options );
			return '';
		}

		$url = add_query_arg(
			array(
				'cntctfrm_clean_deprecated' => '1',
				'cntctfrm_nonce'            => wp_create_nonce( 'cntctfrm_clean_deprecated' )
			),
			( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
		);
		$close_link = "<a href=\"{$url}\" class=\"close_icon notice-dismiss\"></a>";

		$message = sprintf( __( "Deprecated shortcode %1s from the %2s plugin is used on your site. Please replace it with %3s. If you close this message it'll appear again after deprecated shortcode reuse.", 'contact-form-pro' ), '<strong>[contact_form]</strong>', $cntctfrm_plugin_info['Name'], '<strong>[bestwebsoft_contact_form]</strong>' );

		return
			"<style type=\"text/css\">
				.cntctfrm_deprecated_error {
					position: relative;
				}
				div.error.cntctfrm_deprecated_error p {
					margin-left: 15px;
					margin-right: 15px;
				}
				.cntctfrm_deprecated_error a {
					text-decoration: none;
				}
			</style>
			<div class=\"cntctfrm_deprecated_error error\"><p>{$message}</p>{$close_link}</div>";
	}
}

/**
 * Adds information about deprecated shortcode to plugin settings during its call
 * @since 4.0.3
 * @todo delete after 01.04.2017
 */
if ( ! function_exists( 'cntctfrm_detect_deprecated' ) ) {
	function cntctfrm_detect_deprecated( $atts = array( 'lang' => 'default' ) ) {
		/* get options to avoid conflict with CF Multi */
		$options = get_option( 'cntctfrm_options' );
		if ( empty( $options['deprecated_shortcode'] ) ) {
			$options['deprecated_shortcode'] = 1;
			update_option( 'cntctfrm_options', $options );
		}

		return cntctfrm_display_form( $atts );
	}
}

/**
 * @deprecated sinse 4.0.3
 * @todo delete after 01.04.2017
 */
add_shortcode( 'contact_form', 'cntctfrm_detect_deprecated' );