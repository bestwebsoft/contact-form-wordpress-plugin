<?php
/*
Plugin Name: Contact Form by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/contact-form/
Description: Simple contact form plugin any WordPress website must have.
Author: BestWebSoft
Text Domain: contact-form-plugin
Domain Path: /languages
Version: 4.0.6
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
*/

/*  @ Copyright 2017 BestWebSoft  ( https://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * @todo remove file after 01.04.2017
 */
require_once( dirname( __FILE__ ) . '/includes/deprecated.php' );

/**
* Add Wordpress page 'bws_panel' and sub-page of this plugin to admin-panel.
* @return void
*/
if ( ! function_exists( 'cntctfrm_admin_menu' ) ) {
	function cntctfrm_admin_menu() {
		bws_general_menu();
		$cntctfrm_settings = add_submenu_page( 'bws_panel', __( 'Contact Form Settings', 'contact-form-plugin' ), __( 'Contact Form', 'contact-form-plugin' ), 'manage_options', 'contact_form.php', 'cntctfrm_settings_page' );
		add_action( 'load-' . $cntctfrm_settings, 'cntctfrm_add_tabs' );
	}
}

if ( ! function_exists ( 'cntctfrm_init' ) ) {
	function cntctfrm_init() {
		global $bws_plugin_info, $cntctfrm_plugin_info;

		if ( ! session_id() )
			@session_start();

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $cntctfrm_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$cntctfrm_plugin_info = get_plugin_data( __FILE__ );
		}
		/* Function check if plugin is compatible with current WP version  */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $cntctfrm_plugin_info, '3.8' );

		if ( ! is_admin() )
			cntctfrm_check_and_send();
	}
}

if ( ! function_exists ( 'cntctfrm_admin_init' ) ) {
	function cntctfrm_admin_init() {
		global $bws_plugin_info, $cntctfrm_plugin_info, $bws_shortcode_list, $cntctfrm_lang_codes;
		/* Add variable for bws_menu */

		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '77', 'version' => $cntctfrm_plugin_info["Version"] );

		/* Display form on the setting page */
		$cntctfrm_lang_codes = array(
			'ab' => 'Abkhazian', 'aa' => 'Afar', 'af' => 'Afrikaans', 'ak' => 'Akan', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'an' => 'Aragonese', 'hy' => 'Armenian', 'as' => 'Assamese', 'av' => 'Avaric', 'ae' => 'Avestan', 'ay' => 'Aymara', 'az' => 'Azerbaijani',
			'bm' => 'Bambara', 'ba' => 'Bashkir', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali', 'bh' => 'Bihari', 'bi' => 'Bislama', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian', 'my' => 'Burmese',
			'ca' => 'Catalan; Valencian', 'ch' => 'Chamorro', 'ce' => 'Chechen', 'ny' => 'Chichewa; Chewa; Nyanja', 'zh' => 'Chinese', 'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic', 'cv' => 'Chuvash', 'km' => 'Central Khmer', 'kw' => 'Cornish', 'co' => 'Corsican', 'cr' => 'Cree', 'hr' => 'Croatian', 'cs' => 'Czech',
			'da' => 'Danish', 'dv' => 'Divehi; Dhivehi; Maldivian', 'nl' => 'Dutch; Flemish', 'dz' => 'Dzongkha',
			'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe',
			'fo' => 'Faroese', 'fj' => 'Fijjian', 'fi' => 'Finnish', 'fr' => 'French', 'ff' => 'Fulah',
			'gd' => 'Gaelic; Scottish Gaelic', 'gl' => 'Galician', 'lg' => 'Ganda', 'ka' => 'Georgian', 'de' => 'German', 'el' => 'Greek, Modern', 'gn' => 'Guarani', 'gu' => 'Gujarati',
			'ht' => 'Haitian; Haitian Creole', 'ha' => 'Hausa', 'he' => 'Hebrew', 'hz' => 'Herero', 'hi' => 'Hindi', 'ho' => 'Hiri Motu', 'hu' => 'Hungarian',
			'is' => 'Icelandic', 'io' => 'Ido', 'ig' => 'Igbo', 'id' => 'Indonesian', 'ie' => 'Interlingue', 'ia' => 'Interlingua (International Auxiliary Language Association)', 'iu' => 'Inuktitut', 'ik' => 'Inupiaq', 'ga' => 'Irish', 'it' => 'Italian',
			'ja' => 'Japanese', 'jv' => 'Javanese',
			'kl' => 'Kalaallisut; Greenlandic', 'kn' => 'Kannada', 'kr' => 'Kanuri', 'ks' => 'Kashmiri', 'kk' => 'Kazakh', 'ki' => 'Kikuyu; Gikuyu', 'rw' => 'Kinyarwanda', 'ky' => 'Kirghiz; Kyrgyz', 'kv' => 'Komi', 'kg' => 'Kongo', 'ko' => 'Korean', 'kj' => 'Kuanyama; Kwanyama', 'ku' => 'Kurdish',
			'lo' => 'Lao', 'la' => 'Latin', 'lv' => 'Latvian', 'li' => 'Limburgan; Limburger; Limburgish', 'ln' => 'Lingala', 'lt' => 'Lithuanian', 'lu' => 'Luba-Katanga', 'lb' => 'Luxembourgish; Letzeburgesch',
			'mk' => 'Macedonian', 'mg' => 'Malagasy', 'ms' => 'Malay', 'ml' => 'Malayalam', 'mt' => 'Maltese', 'gv' => 'Manx', 'mi' => 'Maori', 'mr' => 'Marathi', 'mh' => 'Marshallese', 'mo' => 'Moldavian', 'mn' => 'Mongolian',
			'na' => 'Nauru', 'nv' => 'Navajo; Navaho', 'nr' => 'Ndebele, South; South Ndebele', 'nd' => 'Ndebele, North; North Ndebele', 'ng' => 'Ndonga', 'ne' => 'Nepali', 'se' => 'Northern Sami', 'no' => 'Norwegian', 'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian', 'nb' => 'Norwegian Bokmål; Bokmål, Norwegian',
			'oc' => 'Occitan, Provençal', 'oj' => 'Ojibwa', 'or' => 'Oriya', 'om' => 'Oromo', 'os' => 'Ossetian; Ossetic',
			'pi' => 'Pali', 'pa' => 'Panjabi; Punjabi', 'fa' => 'Persian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'ps' => 'Pushto',
			'qu' => 'Quechua',
			'ro' => 'Romanian', 'rm' => 'Romansh', 'rn' => 'Rundi', 'ru' => 'Russian',
			'sm' => 'Samoan', 'sg' => 'Sango', 'sa' => 'Sanskrit', 'sc' => 'Sardinian', 'sr' => 'Serbian', 'sn' => 'Shona', 'ii' => 'Sichuan Yi', 'sd' => 'Sindhi', 'si' => 'Sinhala; Sinhalese', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'so' => 'Somali', 'st' => 'Sotho, Southern', 'es' => 'Spanish; Castilian', 'su' => 'Sundanese', 'sw' => 'Swahili', 'ss' => 'Swati', 'sv' => 'Swedish',
			'tl' => 'Tagalog', 'ty' => 'Tahitian', 'tg' => 'Tajik', 'ta' => 'Tamil', 'tt' => 'Tatar', 'te' => 'Telugu', 'th' => 'Thai', 'bo' => 'Tibetan', 'ti' => 'Tigrinya', 'to' => 'Tonga (Tonga Islands)', 'ts' => 'Tsonga', 'tn' => 'Tswana', 'tr' => 'Turkish', 'tk' => 'Turkmen', 'tw' => 'Twi',
			'ug' => 'Uighur; Uyghur', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek',
			've' => 'Venda', 'vi' => 'Vietnamese', 'vo' => 'Volapük',
			'wa' => 'Walloon', 'cy' => 'Welsh', 'fy' => 'Western Frisian', 'wo' => 'Wolof',
			'xh' => 'Xhosa',
			'yi' => 'Yiddish', 'yo' => 'Yoruba',
			'za' => 'Zhuang; Chuang', 'zu' => 'Zulu'
		);

		/* Call register settings function */
		if ( isset( $_REQUEST['page'] ) && 'contact_form.php' == $_REQUEST['page'] )
			cntctfrm_settings();

		/* add contact form to global $bws_shortcode_list  */
		$bws_shortcode_list['cntctfrm'] = array( 'name' => 'Contact Form', 'js_function' => 'cntctfrm_shortcode_init' );
	}
}

if ( ! function_exists ( 'cntctfrm_plugins_loaded' ) ) {
	function cntctfrm_plugins_loaded() {
		/* Internationalization */
		load_plugin_textdomain( 'contact-form-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Register settings for plugin */
if ( ! function_exists( 'cntctfrm_settings' ) ) {
	function cntctfrm_settings( $form_id = false ) {
		global $cntctfrm_options, $cntctfrm_plugin_info;
		$db_version = '1.0';

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( ! $cntctfrm_plugin_info )
			$cntctfrm_plugin_info = get_plugin_data( __FILE__ );

		/* Install the option defaults */
		if ( ! get_option( 'cntctfrm_options' ) ) {
			$option_defaults = cntctfrm_get_option_defaults();
			add_option( 'cntctfrm_options', $option_defaults );
		}

		/* Get options from the database for default options */
		if ( $contact_form_multi_active ) {
			if ( ! get_option( 'cntctfrmmlt_options' ) ) {
				if ( ! isset( $option_defaults ) )
					$option_defaults = cntctfrm_get_option_defaults();
				add_option( 'cntctfrmmlt_options', $option_defaults );
			}

			$cntctfrmmlt_options = get_option( 'cntctfrmmlt_options' );

			if ( ! isset( $cntctfrmmlt_options['plugin_option_version'] ) || $cntctfrmmlt_options['plugin_option_version'] != $cntctfrm_plugin_info["Version"] ) {

				if ( ! isset( $option_defaults ) )
					$option_defaults = cntctfrm_get_option_defaults();
				/**
				 * @since 3.98
				 * @todo delete after 25.04.2017
				 */
				$cntctfrmmlt_options = cntctfrm_options_update( $cntctfrmmlt_options, $option_defaults, $contact_form_multi_active );

				$cntctfrmmlt_options = array_merge( $option_defaults, $cntctfrmmlt_options );
				$cntctfrmmlt_options['plugin_option_version'] = $cntctfrm_plugin_info["Version"];
				update_option( 'cntctfrmmlt_options', $cntctfrmmlt_options );
			}

			/* Get options from the database */
			if ( isset( $_SESSION['cntctfrmmlt_id_form'] ) || $form_id ) {
				$id = ( $form_id ) ? $form_id : $_SESSION['cntctfrmmlt_id_form'];
				if ( $cntctfrm_options = get_option( 'cntctfrmmlt_options_' . $id ) ) {
					/* */
				} else {
					if ( 'pro' == $contact_form_multi_active && $options_main = get_option( 'cntctfrmmltpr_options_main' ) ) {
						/**/
					} elseif ( $contact_form_multi_active ) {
						$options_main = get_option( 'cntctfrmmlt_options_main' );
					}

					if (  1 == $id && 1 == count( $options_main['name_id_form'] ) ) {
						add_option( 'cntctfrmmlt_options_1' , get_option( 'cntctfrm_options' ) );
						$cntctfrm_options = get_option( 'cntctfrmmlt_options_1' );
					} else {
						$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
					}
				}
			} else {
				$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
			}
		} else {
			/* Get options from the database */
			$cntctfrm_options = get_option( 'cntctfrm_options' );
		}

		if ( ! isset( $cntctfrm_options['plugin_option_version'] ) || $cntctfrm_options['plugin_option_version'] != $cntctfrm_plugin_info["Version"] ) {

			if ( ! isset( $option_defaults ) )
				$option_defaults = cntctfrm_get_option_defaults();
			/**
			 * @since 3.98
			 * @todo delete after 25.04.2017
			 */
			$cntctfrm_options = cntctfrm_options_update( $cntctfrm_options, $option_defaults, $contact_form_multi_active );

			$cntctfrm_options = array_merge( $option_defaults, $cntctfrm_options );
			$cntctfrm_options['plugin_option_version'] = $cntctfrm_plugin_info["Version"];
			/* show pro features */
			$cntctfrm_options['hide_premium_options'] = array();

			if ( $contact_form_multi_active ) {
				if ( isset( $_SESSION['cntctfrmmlt_id_form'] ) || $form_id ) {
					$id = ( $form_id ) ? $form_id : $_SESSION['cntctfrmmlt_id_form'];
					if ( get_option( 'cntctfrmmlt_options_' . $id ) ) {
						update_option( 'cntctfrmmlt_options_' . $id , $cntctfrm_options );
					} else {
						update_option( 'cntctfrmmlt_options', $cntctfrm_options );
					}
				} else {
					update_option( 'cntctfrmmlt_options', $cntctfrm_options );
				}
			} else {
				update_option( 'cntctfrm_options', $cntctfrm_options );
			}
		}

		/* Create db table of fields list */
		if ( ! isset( $cntctfrm_options['plugin_db_version'] ) || $cntctfrm_options['plugin_db_version'] != $db_version ) {
			cntctfrm_db_create();
			$cntctfrm_options['plugin_db_version'] = $db_version;
			if ( $contact_form_multi_active ) {
				if ( isset( $_SESSION['cntctfrmmlt_id_form'] ) || $form_id ) {
					$id = ( $form_id ) ? $form_id : $_SESSION['cntctfrmmlt_id_form'];
					if ( get_option( 'cntctfrmmlt_options_' . $id ) ) {
						update_option( 'cntctfrmmlt_options_' . $id , $cntctfrm_options );
					} else {
						update_option( 'cntctfrmmlt_options', $cntctfrm_options );
					}
				} else {
					update_option( 'cntctfrmmlt_options', $cntctfrm_options );
				}
			} else {
				update_option( 'cntctfrm_options', $cntctfrm_options );
			}
		}
	}
}

/**
* @return array Default plugin options
* @since 4.0.2
*/
if ( ! function_exists( 'cntctfrm_get_option_defaults' ) ) {
	function cntctfrm_get_option_defaults() {
		global $cntctfrm_plugin_info;

		if ( ! $cntctfrm_plugin_info )
			$cntctfrm_plugin_info = get_plugin_data( __FILE__ );

		$sitename = strtolower( filter_var( $_SERVER['SERVER_NAME'], FILTER_SANITIZE_STRING ) );
		if ( substr( $sitename, 0, 4 ) == 'www.' )
			$sitename = substr( $sitename, 4 );
		$from_email = 'wordpress@' . $sitename;

		$option_defaults = array(
			'plugin_option_version' 	=> $cntctfrm_plugin_info["Version"],
			'display_settings_notice'	=>	1,
			'first_install'				=>	strtotime( "now" ),
			'suggest_feature_banner'	=>	1,
			'user_email'				=> 'admin',
			'custom_email'				=> get_option("admin_email"),
			'select_email'				=> 'custom',
			'from_email'				=> 'custom',
			'custom_from_email'			=> $from_email,
			'attachment'				=> 0,
			'attachment_explanations'	=> 1,
			'send_copy'					=> 0,
			'from_field'				=> get_bloginfo( 'name' ),
			'select_from_field'			=> 'custom',
			'display_name_field'		=> 1,
			'display_address_field' 	=> 0,
			'display_phone_field' 		=> 0,
			'required_name_field' 		=> 1,
			'required_address_field' 	=> 0,
			'required_email_field' 		=> 1,
			'required_phone_field' 		=> 0,
			'required_subject_field' 	=> 1,
			'required_message_field' 	=> 1,
			'required_symbol'			=> '*',
			'display_add_info' 			=> 1,
			'display_sent_from' 		=> 1,
			'display_date_time' 		=> 1,
			'mail_method' 				=> 'wp-mail',
			'display_coming_from' 		=> 1,
			'display_user_agent' 		=> 1,
			'language'					=> array(),
			'change_label'				=> 0,
			'name_label' 				=> array( 'default' => __( "Name", 'contact-form-plugin' ) . ':' ),
			'address_label' 			=> array( 'default' => __( "Address", 'contact-form-plugin' ) . ':' ),
			'email_label' 				=> array( 'default' => __( "Email Address", 'contact-form-plugin' ) . ':' ),
			'phone_label' 				=> array( 'default' => __( "Phone number", 'contact-form-plugin' ) . ':' ),
			'subject_label' 			=> array( 'default' => __( "Subject", 'contact-form-plugin' ) . ':' ),
			'message_label' 			=> array( 'default' => __( "Message", 'contact-form-plugin' ) . ':' ),
			'attachment_label'			=> array( 'default' => __( "Attachment", 'contact-form-plugin' ) . ':' ),
			'attachment_tooltip'		=> array( 'default' => __( "Supported file types: HTML, TXT, CSS, GIF, PNG, JPEG, JPG, TIFF, BMP, AI, EPS, PS, CSV, RTF, PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR, WAV, MP3, PPT.", 'contact-form-plugin' ) ),
			'send_copy_label'			=> array( 'default' => __( "Send me a copy", 'contact-form-plugin' ) ),
			'submit_label'				=> array( 'default' => __( "Submit", 'contact-form-plugin' ) ),
			'name_error' 				=> array( 'default' => __( "Your name is required.", 'contact-form-plugin' ) ),
			'address_error' 			=> array( 'default' => __( "Address is required.", 'contact-form-plugin' ) ),
			'email_error' 				=> array( 'default' => __( "A valid email address is required.", 'contact-form-plugin' ) ),
			'phone_error' 				=> array( 'default' => __( "Phone number is required.", 'contact-form-plugin' ) ),
			'subject_error' 			=> array( 'default' => __( "Subject is required.", 'contact-form-plugin' ) ),
			'message_error' 			=> array( 'default' => __( "Message text is required.", 'contact-form-plugin' ) ),
			'attachment_error' 			=> array( 'default' => __( "File format is not valid.", 'contact-form-plugin' ) ),
			'attachment_upload_error'	=> array( 'default' => __( "File upload error.", 'contact-form-plugin' ) ),
			'attachment_move_error' 	=> array( 'default' => __( "The file could not be uploaded.", 'contact-form-plugin' ) ),
			'attachment_size_error' 	=> array( 'default' => __( "This file is too large.", 'contact-form-plugin' ) ),
			'captcha_error' 			=> array( 'default' => __( "Please fill out the CAPTCHA.", 'contact-form-plugin' ) ),
			'form_error'				=> array( 'default' => __( "Please make corrections below and try again.", 'contact-form-plugin' ) ),
			'action_after_send' 		=> 1,
			'thank_text' 				=> array( 'default' => __( "Thank you for contacting us.", 'contact-form-plugin' ) ),
			'redirect_url'				=> '',
			'delete_attached_file'		=> '0',
			'html_email'				=> 1,
			'change_label_in_email'		=> 0,
			'layout'                   	=> 1,
			'submit_position' 			=> 'left',
			'order_fields'             	=> array(
												'first_column'  => array(
													'cntctfrm_contact_name',
													'cntctfrm_contact_address',
													'cntctfrm_contact_email',
													'cntctfrm_contact_phone',
													'cntctfrm_contact_subject',
													'cntctfrm_contact_message',
													'cntctfrm_contact_attachment',
													'cntctfrm_contact_send_copy',
													'cntctfrm_subscribe',
													'cntctfrm_captcha'
												),
												'second_column' => array()
											)
		);

		return $option_defaults;
	}
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'cntctfrm_db_create' ) ) {
	function cntctfrm_db_create() {
		global $wpdb;
		$sql = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "cntctfrm_field` (
			id int NOT NULL AUTO_INCREMENT,
			name CHAR(100) NOT NULL,
			UNIQUE KEY id (id)
		);";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		$fields = array(
			'name',
			'email',
			'subject',
			'message',
			'address',
			'phone',
			'attachment',
			'attachment_explanations',
			'send_copy',
			'sent_from',
			'date_time',
			'coming_from',
			'user_agent'
		);
		foreach ( $fields as $key => $value ) {
			$db_row = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "cntctfrm_field WHERE `name` = '" . $value . "'", ARRAY_A );
			if ( empty( $db_row ) ) {
				$wpdb->insert(  $wpdb->prefix . "cntctfrm_field", array( 'name' => $value ), array( '%s' ) );
			}
		}
	}
}

if ( ! function_exists ( 'cntctfrm_activation' ) ) {
	function cntctfrm_activation( $networkwide ) {
		global $wpdb;
		if ( function_exists( 'is_multisite' ) && is_multisite() && $networkwide ) {
			$cntctfrm_blog_id = $wpdb->blogid;
			$cntctfrm_get_blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $cntctfrm_get_blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				cntctfrm_settings();
				cntctfrm_db_create();
			}
			switch_to_blog( $cntctfrm_blog_id );
			return;
		} else {
			cntctfrm_settings();
			cntctfrm_db_create();
		}
	}
}

/**
* @since 4.0.1
* @todo after 1.08.2017. Delete keys marked with # from $cntctfrm_related_plugins global massive. They are created for compatibility with versions of related plugins without modified prefixes or containing prefixes in options' names.
*/
if ( ! function_exists( 'cntctfrm_related_plugins' ) ) {
	function cntctfrm_related_plugins() {
		global $cntctfrm_related_plugins;

		$cntctfrm_related_plugins = array();

		if ( ! function_exists( 'is_plugin_active' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		/* Get Captcha options */
		if ( is_plugin_active( 'captcha/captcha.php' ) ) {
			$cptch_options = get_option( 'cptch_options' );

			$captcha_enabled = 0;

			if ( isset( $cptch_options['forms']['bws_contact']['enable'] ) ) {
				$captcha_enabled = $cptch_options['forms']['bws_contact']['enable'];
			} else {
				if ( isset( $cptch_options['cptch_contact_form'] ) ) {
					$captcha_enabled = $cptch_options['cptch_contact_form'];
				}

				if ( isset( $cptch_options['contact_form'] ) ) {
					$captcha_enabled = $cptch_options['contact_form'];
				}
			}

			$label = isset( $cptch_options['label_form'] ) ? 'label_form' : 'cptch_label_form';
			$required_symbol = isset( $cptch_options['required_symbol'] ) ? 'required_symbol' : 'cptch_required_symbol';
			$cntctfrm_related_plugins['captcha'] = array(
				'enabled'			=> $captcha_enabled,
				'options'			=> $cptch_options,
				'options_name'		=> 'cptch_options',
				'label'				=> $label,
				'required_symbol'	=> $required_symbol,
				'settings_page'		=> 'captcha.php'
			);
		} elseif ( is_plugin_active( 'captcha-plus/captcha-plus.php' ) ) {
			if ( $cptchpls_options = get_option( 'cptchpls_options' ) ) {
				$options_name = 'cptchpls_options';
			} elseif ( $cptchpls_options = get_option( 'cptch_options' ) ) {
				$options_name = 'cptch_options';
			}

			$captcha_enabled = 0;

			if ( isset( $cptchpls_options['forms']['bws_contact']['enable'] ) ) {
				$captcha_enabled = $cptchpls_options['forms']['bws_contact']['enable'];
			} else {
				if ( isset( $cptchpls_options['cptch_contact_form'] ) ) {
					$captcha_enabled = $cptchpls_options['cptch_contact_form'];
				}

				if ( isset( $cptchpls_options['contact_form'] ) ) {
					$captcha_enabled = $cptchpls_options['contact_form'];
				}
			}

			$label = isset( $cptchpls_options['label_form'] ) ? 'label_form' : 'cptchpls_label_form';
			$required_symbol = isset( $cptchpls_options['required_symbol'] ) ? 'required_symbol' : 'cptchpls_required_symbol';
			$cntctfrm_related_plugins['captcha'] = array(
				'enabled'			=> $captcha_enabled,
				'options'			=> $cptchpls_options,
				'options_name'		=> $options_name,
				'label'			=> $label,
				'required_symbol'	=> $required_symbol,
				'settings_page'		=> 'captcha-plus.php'
			);
		} elseif ( is_plugin_active( 'captcha-pro/captcha_pro.php' ) ) {
			if ( $cptchpr_options = get_option( 'cptchpr_options' ) ) {
				$options_name = 'cptchpr_options';
			} elseif ( $cptchpr_options = get_option( 'cptch_options' ) ) {
				$options_name = 'cptch_options';
			}

			$captcha_enabled = 0;

			if ( isset( $cptchpr_options['forms']['bws_contact']['enable'] ) ) {
				$captcha_enabled = $cptchpr_options['forms']['bws_contact']['enable'];
			} else {
				if ( isset( $cptchpr_options['cptch_contact_form'] ) ) {
					$captcha_enabled = $cptchpr_options['cptch_contact_form'];
				}

				if ( isset( $cptchpr_options['contact_form'] ) ) {
					$captcha_enabled = $cptchpr_options['contact_form'];
				}
			}

			$label = isset( $cptchpr_options['label_form'] ) ? 'label_form' : 'cptchpr_label_form';
			$required_symbol = isset( $cptchpr_options['required_symbol'] ) ? 'required_symbol' : 'cptchpr_required_symbol';

			$cntctfrm_related_plugins['captcha'] = array(
				'enabled'			=> $captcha_enabled,
				'options'			=> $cptchpr_options,
				'options_name'		=> $options_name,
				'label'				=> $label,
				'required_symbol'	=> $required_symbol,
				'settings_page'		=> 'captcha_pro.php'
			);
		}

		/* Get Google Captcha options */
		if ( is_plugin_active( 'google-captcha/google-captcha.php' ) ) {
			$gglcptch_options = get_option( 'gglcptch_options' );

			$cntctfrm_related_plugins['google-captcha'] = array(
				'options'			=> $gglcptch_options,
				'options_name'		=> 'gglcptch_options',
				'settings_page'		=> 'google-captcha.php'
			);
		} elseif ( is_plugin_active( 'google-captcha-pro/google-captcha-pro.php' ) ) {
			if ( $gglcptchpr_options = get_option( 'gglcptchpr_options' ) ) {
				$options_name = 'gglcptchpr_options';
			} elseif ( $gglcptchpr_options =  get_option( 'gglcptch_options' ) ) {
				$options_name = 'gglcptch_options';
			}
			$cntctfrm_related_plugins['google-captcha'] = array(
				'options'			=> $gglcptchpr_options,
			'options_name'		=> $options_name,
				'settings_page'		=> 'google-captcha-pro.php'
			);
		}

		/* Get Subscriber options */
		if ( is_multisite() ) {
			if ( is_plugin_active_for_network( 'subscriber/subscriber.php' ) ) {
				$sbscrbr_options = get_site_option( 'sbscrbr_options' );

				$cntctfrm_related_plugins['subscriber'] = array(
					'options'			=> $sbscrbr_options,
					'options_name'		=> 'sbscrbr_options',
					'settings_page'		=> 'sbscrbr_settings_page'
				);
			} elseif ( is_plugin_active_for_network( 'subscriber-pro/subscriber-pro.php' ) ) {
				if ( $sbscrbrpr_options = get_site_option( 'sbscrbrpr_options' ) ) {
					$options_name = 'sbscrbrpr_options';
				} elseif ( $sbscrbrpr_options = get_site_option( 'sbscrbr_options' ) ) {
					$options_name = 'sbscrbr_options';
				}

				$cntctfrm_related_plugins['subscriber'] = array(
					'options'			=> $sbscrbrpr_options,
					'options_name'		=> $options_name,
					'settings_page'		=> 'sbscrbrpr_settings_page'
				);
			}
		} else {
			if ( is_plugin_active( 'subscriber/subscriber.php' ) ) {
				$sbscrbr_options = get_option( 'sbscrbr_options' );

				$cntctfrm_related_plugins['subscriber'] = array(
					'options'			=> $sbscrbr_options,
					'options_name'		=> 'sbscrbr_options',
					'settings_page'		=> 'sbscrbr_settings_page'
				);
			} elseif ( is_plugin_active( 'subscriber-pro/subscriber-pro.php' ) ) {
				if ( $sbscrbrpr_options = get_option( 'sbscrbrpr_options' ) ) {
					$options_name = 'sbscrbrpr_options';
				} elseif ( $sbscrbrpr_options = get_option( 'sbscrbr_options' ) ) {
					$options_name = 'sbscrbr_options';
				}

				$cntctfrm_related_plugins['subscriber'] = array(
					'options'			=> $sbscrbrpr_options,
					'options_name'		=> $options_name,
					'settings_page'		=> 'sbscrbrpr_settings_page'
				);
			}
		}

		/* Get Contact Form to DB options */
		if ( is_plugin_active( 'contact-form-to-db/contact_form_to_db.php' ) ) {
			$cntctfrmtdb_options = get_option( 'cntctfrmtdb_options' );

			$save_option = isset( $cntctfrmtdb_options['save_messages_to_db'] ) ? 'save_messages_to_db' : 'cntctfrmtdb_save_messages_to_db';
			$cntctfrm_related_plugins['contact-form-to-db'] = array(
				'options'			=> $cntctfrmtdb_options,
				'options_name'		=> 'cntctfrmtdb_options',
				'save_option'		=> $save_option,
				'settings_page'		=> 'cntctfrmtdb_settings'
			);
		} elseif ( is_plugin_active( 'contact-form-to-db-pro/contact_form_to_db_pro.php' ) ) {
			if ( $cntctfrmtdbpr_options = get_option( 'cntctfrmtdbpr_options' ) ) {
				$options_name = 'cntctfrmtdbpr_options';
			} elseif ( $cntctfrmtdbpr_options = get_option( 'cntctfrmtdb_options' ) ) {
				$options_name = 'cntctfrmtdb_options';
			}
			$save_option = isset( $cntctfrmtdbpr_options['save_messages_to_db'] ) ? 'save_messages_to_db' : 'cntctfrmtdbpr_save_messages_to_db';
			$cntctfrm_related_plugins['contact-form-to-db'] = array(
				'options'			=> $cntctfrmtdbpr_options,
				'options_name'		=> $options_name,
				'save_option'		=> $save_option,
				'settings_page'		=> 'cntctfrmtdbpr_settings'
			);
		}
	}
}

if ( ! function_exists( 'cntctfrm_check_cf_multi_active' ) ) {
	function cntctfrm_check_cf_multi_active() {
		/* Check contact-form-multi (free) plugin */
		if ( is_plugin_active( 'contact-form-multi/contact-form-multi.php' ) )
			$contact_form_multi_active = true;

		/* Check contact-form-multi-pro plugin */
		if ( is_plugin_active( 'contact-form-multi-pro/contact-form-multi-pro.php' ) )
			$contact_form_multi_pro_active = true;

		if ( isset( $contact_form_multi_active ) )
			return "free";

		if ( isset( $contact_form_multi_pro_active ) )
			return "pro";

		return false;
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'cntctfrm_get_ordered_fields' ) ) {
	function cntctfrm_get_ordered_fields() {
		global $cntctfrm_options, $cntctfrm_related_plugins;

		if ( ! isset( $cntctfrm_options['order_fields'] ) )
			cntctfrm_settings();

		if ( empty( $cntctfrm_related_plugins ) )
			cntctfrm_related_plugins();

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( ! $contact_form_multi_active ) {
			$display_captcha = $display_google_captcha = $display_subscriber = false;

			if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) )
				$display_captcha = ! empty( $cntctfrm_related_plugins['captcha']['enabled'] );

			if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) )
				$display_google_captcha = ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] );

			if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) )
				$display_subscriber = ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] );
		} else {
			$display_captcha		= ( isset( $cntctfrm_options['display_captcha'] ) && 1 == $cntctfrm_options['display_captcha'] ) ? true : false;
			$display_google_captcha	= ( isset( $cntctfrm_options['display_google_captcha'] ) && 1 == $cntctfrm_options['display_google_captcha'] ) ? true : false;
			$display_subscriber		= ( isset( $cntctfrm_options['display_subscribe'] ) && 1 == $cntctfrm_options['display_subscribe'] ) ? true : false;
		}

		$default_order_fields = array(
			'cntctfrm_contact_name'			=> ( 1 == $cntctfrm_options['display_name_field'] ) ? true : false,
			'cntctfrm_contact_address'		=> ( 1 == $cntctfrm_options['display_address_field'] ) ? true : false,
			'cntctfrm_contact_email'		=> true,
			'cntctfrm_contact_phone'		=> ( 1 == $cntctfrm_options['display_phone_field'] ) ? true : false,
			'cntctfrm_contact_subject'		=> true,
			'cntctfrm_contact_message'		=> true,
			'cntctfrm_contact_attachment'	=> ( 1 == $cntctfrm_options['attachment'] ) ? true : false,
			'cntctfrm_contact_send_copy'	=> ( 1 == $cntctfrm_options['send_copy'] ) ? true : false,
			'cntctfrm_subscribe'			=> $display_subscriber,
			'cntctfrm_captcha'				=> $display_captcha || $display_google_captcha ? true : false,
		);

		$display_fields = array();
		foreach ( $default_order_fields as $field => $value ) {
			if ( $value == true ) {
				array_push( $display_fields , $field );
			}
		}

		$ordered_fields = array_merge( $cntctfrm_options['order_fields']['first_column'], $cntctfrm_options['order_fields']['second_column'] );
		$diff_fields = array_diff( $display_fields, $ordered_fields );

		foreach ( $diff_fields as $field ) {
			array_push( $cntctfrm_options['order_fields'][ 'first_column' ], $field );
		}

		return $cntctfrm_options['order_fields'];
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'cntctfrm_settings_page' ) ) {
	function cntctfrm_settings_page() {
		global $cntctfrm_options, $wpdb, $wp_version, $cntctfrm_plugin_info, $cntctfrm_lang_codes, $cntctfrm_related_plugins;

		if ( empty( $cntctfrm_related_plugins ) )
			cntctfrm_related_plugins();

		/* Check contact-form-multi plugin */
		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		$error = $message = $notice = '';
		$plugin_basename = plugin_basename( __FILE__ );

		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( ! isset( $_GET['action'] ) || 'additional' == $_GET['action'] || 'appearance' == $_GET['action'] )
			$all_plugins = get_plugins();

		$userslogin = get_users( 'blog_id=' . $GLOBALS['blog_id'] . '&role=administrator' );

		if ( isset( $_POST['cntctfrm_form_appearance_submit'] ) && check_admin_referer( $plugin_basename, 'cntctfrm_nonce_name' ) ) {

			$options_submit = array();

			if ( isset( $_POST['bws_hide_premium_options'] ) ) {
				$hide_result = bws_hide_premium_options( $options_submit );
				$options_submit = $hide_result['options'];
				if ( $contact_form_multi_active ) {
					$cntctfrm_options = array_merge( $cntctfrm_options, $options_submit );
					update_option( 'cntctfrm_options', $cntctfrm_options );
				}
			}

			$layout = ( isset( $_POST['cntctfrm_layout'] ) ) ? (int) $_POST['cntctfrm_layout'] : 1;

			$submit_position = ( isset( $_POST['cntctfrm_submit_position'] ) ) ? stripslashes( esc_html( $_POST['cntctfrm_submit_position'] ) ) : 'left';

			$layout_first_column_string = stripslashes( esc_html( $_POST['cntctfrm_layout_first_column'] ) );
			$layout_first_column = explode( ',', $layout_first_column_string );
			$layout_first_column = array_diff( $layout_first_column, array('') );

			$layout_second_column_string = stripslashes( esc_html( $_POST['cntctfrm_layout_second_column'] ) );
			$layout_second_column = explode( ',', $layout_second_column_string );
			$layout_second_column = array_diff( $layout_second_column, array('') );

			if ( $layout === 1 && ! empty( $layout_second_column ) ) {
				$layout_first_column = array_merge( $layout_first_column, $layout_second_column );
				$layout_second_column = array();
			}

			$options_submit['layout'] = $layout;
			$options_submit['submit_position'] = $submit_position;
			$options_submit['order_fields']['first_column'] = $layout_first_column;
			$options_submit['order_fields']['second_column'] = $layout_second_column;

			$cntctfrm_options = array_merge( $cntctfrm_options, $options_submit );

			if ( 'pro' == $contact_form_multi_active && get_option( 'cntctfrmmltpr_options_main' ) ) {
				$options_main = get_option( 'cntctfrmmltpr_options_main' );

				if ( $options_main['id_form'] !== $_SESSION['cntctfrmmlt_id_form'] )
					add_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
				else if ( $options_main['id_form'] == $_SESSION['cntctfrmmlt_id_form'] )
					update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
			} elseif ( $contact_form_multi_active ) {
				$options_main = get_option( 'cntctfrmmlt_options_main' );

				if ( $options_main['id_form'] !== $_SESSION['cntctfrmmlt_id_form'] )
					add_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
				else if ( $options_main['id_form'] == $_SESSION['cntctfrmmlt_id_form'] )
					update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
			} else {
				update_option( 'cntctfrm_options', $cntctfrm_options );
			}
			$message = __( "Settings saved.", 'contact-form-plugin' );
		}

		/* Save data for settings page */
		if ( isset( $_POST['cntctfrm_form_submit'] ) && check_admin_referer( $plugin_basename, 'cntctfrm_nonce_name' ) ) {

			$options_submit = array();

			if ( isset( $_POST['bws_hide_premium_options'] ) ) {
				$hide_result = bws_hide_premium_options( $options_submit );
				$options_submit = $hide_result['options'];
				if ( $contact_form_multi_active ) {
					$cntctfrm_options = array_merge( $cntctfrm_options, $options_submit );
					update_option( 'cntctfrm_options', $cntctfrm_options );
				}
			}

			$options_submit['user_email'] = $_POST['cntctfrm_user_email'];
			$options_submit['custom_email'] = trim( stripslashes( esc_html( $_POST['cntctfrm_custom_email'] ) ), " ," );
			$options_submit['select_email'] = $_POST['cntctfrm_select_email'];
			$options_submit['from_email'] = $_POST['cntctfrm_from_email'];
			$options_submit['custom_from_email'] = stripslashes( esc_html( $_POST['cntctfrm_custom_from_email'] ) );

			$options_submit['mail_method']				= $_POST['cntctfrm_mail_method'];
			$options_submit['from_field']				= stripslashes( esc_html( $_POST['cntctfrm_from_field'] ) );
			$options_submit['select_from_field']		= $_POST['cntctfrm_select_from_field'];
			$options_submit['display_name_field']		= isset( $_POST['cntctfrm_display_name_field'] ) ? 1 : 0;
			$options_submit['display_address_field']	= isset( $_POST['cntctfrm_display_address_field'] ) ? 1 : 0;
			$options_submit['display_phone_field']		= isset( $_POST['cntctfrm_display_phone_field'] ) ? 1 : 0;
			$options_submit['attachment']				= isset( $_POST['cntctfrm_attachment'] ) ? $_POST['cntctfrm_attachment'] : 0;
			$options_submit['attachment_explanations']	= isset( $_POST['cntctfrm_attachment_explanations'] ) ? $_POST['cntctfrm_attachment_explanations'] : 0;
			$options_submit['send_copy']				= isset( $_POST['cntctfrm_send_copy'] ) ? $_POST['cntctfrm_send_copy'] : 0;

			$options_submit['delete_attached_file'] = isset( $_POST['cntctfrm_delete_attached_file'] ) ? $_POST['cntctfrm_delete_attached_file'] : 0;

			if ( isset( $_POST['cntctfrm_add_language_button'] ) )
				cntctfrm_add_language();

			if ( isset( $_POST['cntctfrm_delete_button'] ) )
				cntctfrm_remove_language();

			if ( $contact_form_multi_active ) {
				$options_submit['display_captcha']			= isset( $_POST['cntctfrm_display_captcha'] ) ? 1 : 0;
				$options_submit['display_google_captcha']	= isset( $_POST['cntctfrm_display_google_captcha'] ) ? 1 : 0;
				$options_submit['display_subscribe']		= isset( $_POST['cntctfrm_display_subscriber'] ) ? 1 : 0;
				$options_submit['save_email_to_db']			= isset( $_POST['cntctfrm_save_email_to_db'] ) ? 1 : 0;
			} else {
				/* Update related plugins options if Contact Form Multi is not active */
				if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) ) {
					$cptch_enable = isset( $_POST['cntctfrm_display_captcha'] ) ? 1 : 0;
					$cntctfrm_related_plugins['captcha']['enabled'] = $cptch_enable;
					$cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] = $cptch_enable;
					update_option( $cntctfrm_related_plugins['captcha']['options_name'], $cntctfrm_related_plugins['captcha']['options'] );
				}

				if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) ) {
					$cntctfrm_related_plugins['google-captcha']['options']['contact_form'] = isset( $_POST['cntctfrm_display_google_captcha'] ) ? 1 : 0;
					update_option( $cntctfrm_related_plugins['google-captcha']['options_name'], $cntctfrm_related_plugins['google-captcha']['options'] );
				}

				if ( is_multisite() ) {
					if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
						$cntctfrm_related_plugins['subscriber']['options']['contact_form'] = isset( $_POST['cntctfrm_display_subscriber'] ) ? 1 : 0;
						update_site_option( $cntctfrm_related_plugins['subscriber']['options_name'], $cntctfrm_related_plugins['subscriber']['options'] );
					}
				} else {
					if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
						$cntctfrm_related_plugins['subscriber']['options']['contact_form'] = isset( $_POST['cntctfrm_display_subscriber'] ) ? 1 : 0;
						update_option( $cntctfrm_related_plugins['subscriber']['options_name'], $cntctfrm_related_plugins['subscriber']['options'] );
					}
				}

				if ( array_key_exists( 'contact-form-to-db', $cntctfrm_related_plugins ) ) {
					$cntctfrm_related_plugins['contact-form-to-db']['options'][ $cntctfrm_related_plugins['contact-form-to-db']['save_option'] ] = isset( $_POST['cntctfrm_save_email_to_db'] ) ? 1 : 0;
					update_option( $cntctfrm_related_plugins['contact-form-to-db']['options_name'], $cntctfrm_related_plugins['contact-form-to-db']['options'] );
				}
			}

			if ( 0 == $options_submit['display_name_field'] ) {
				$options_submit['required_name_field'] = 0;
			} else {
				$options_submit['required_name_field'] = isset( $_POST['cntctfrm_required_name_field'] ) ? 1 : 0;
			}
			if ( 0 == $options_submit['display_address_field'] ) {
				$options_submit['required_address_field']	= 0;
			} else {
				$options_submit['required_address_field']	= isset( $_POST['cntctfrm_required_address_field'] ) ? 1 : 0;
			}
			$options_submit['required_email_field'] = isset( $_POST['cntctfrm_required_email_field'] ) ? 1 : 0;
			if ( 0 == $options_submit['display_phone_field'] ) {
				$options_submit['required_phone_field']	= 0;
			} else {
				$options_submit['required_phone_field']	= isset( $_POST['cntctfrm_required_phone_field'] ) ? 1 : 0;
			}
			$options_submit['required_subject_field']	= isset( $_POST['cntctfrm_required_subject_field'] ) ? 1 : 0;
			$options_submit['required_message_field']	= isset( $_POST['cntctfrm_required_message_field'] ) ? 1 : 0;

			$options_submit['required_symbol']			= isset( $_POST['cntctfrm_required_symbol'] ) ? stripslashes( esc_html( $_POST['cntctfrm_required_symbol'] ) ) : '*';
			$options_submit['html_email'] 				= isset( $_POST['cntctfrm_html_email'] ) ? 1 : 0;
			$options_submit['display_add_info']			= isset( $_POST['cntctfrm_display_add_info'] ) ? 1 : 0;

			$options_submit['display_sent_from']		= isset( $_POST['cntctfrm_display_sent_from'] ) ? 1 : 0;
			$options_submit['display_date_time']		= isset( $_POST['cntctfrm_display_date_time'] ) ? 1 : 0;
			$options_submit['display_coming_from']		= isset( $_POST['cntctfrm_display_coming_from'] ) ? 1 : 0;
			$options_submit['display_user_agent']		= isset( $_POST['cntctfrm_display_user_agent'] ) ? 1 : 0;

			if ( 0 == $options_submit['display_sent_from'] && 0 == $options_submit['display_date_time'] && 0 == $options_submit['display_coming_from'] && 0 == $options_submit['display_user_agent'] )
				$options_submit['display_add_info'] = 0;

			if ( 0 == $options_submit['display_add_info'] ) {
				$options_submit['display_sent_from']	= 1;
				$options_submit['display_date_time']	= 1;
				$options_submit['display_coming_from']	= 1;
				$options_submit['display_user_agent']	= 1;
			}

			$options_submit['change_label']				= isset( $_POST['cntctfrm_change_label'] ) ? 1 : 0;
			$options_submit['change_label_in_email']	= isset( $_POST['cntctfrm_change_label_in_email'] ) ? 1 : 0;

			if ( 1 == $options_submit['change_label'] ) {
				foreach ( $_POST['cntctfrm_name_label'] as $key => $val ) {
					$options_submit['name_label'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_name_label'][ $key ] ) );
					$options_submit['address_label'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_address_label'][ $key ] ) );
					$options_submit['email_label'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_email_label'][ $key ] ) );
					$options_submit['phone_label'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_phone_label'][ $key ] ) );
					$options_submit['subject_label'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_subject_label'][ $key ] ) );
					$options_submit['message_label'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_message_label'][ $key ] ) );
					$options_submit['attachment_label'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_attachment_label'][ $key ] ) );
					$options_submit['attachment_tooltip'][ $key ]		= stripcslashes( htmlspecialchars( $_POST['cntctfrm_attachment_tooltip'][ $key ] ) );
					$options_submit['send_copy_label'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_send_copy_label'][ $key ] ) );
					$options_submit['thank_text'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_thank_text'][ $key ] ) );
					$options_submit['submit_label'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_submit_label'][ $key ] ) );
					$options_submit['name_error'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_name_error'][ $key ] ) );
					$options_submit['address_error'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_address_error'][ $key ] ) );
					$options_submit['email_error'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_email_error'][ $key ] ) );
					$options_submit['phone_error'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_phone_error'][ $key ] ) );
					$options_submit['subject_error'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_subject_error'][ $key ] ) );
					$options_submit['message_error'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_message_error'][ $key ] ) );
					$options_submit['attachment_error'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_attachment_error'][ $key ] ) );
					$options_submit['attachment_upload_error'][ $key ]	= stripcslashes( htmlspecialchars( $_POST['cntctfrm_attachment_upload_error'][ $key ] ) );
					$options_submit['attachment_move_error'][ $key ]	= stripcslashes( htmlspecialchars( $_POST['cntctfrm_attachment_move_error'][ $key ] ) );
					$options_submit['attachment_size_error'][ $key ]	= stripcslashes( htmlspecialchars( $_POST['cntctfrm_attachment_size_error'][ $key ] ) );
					$options_submit['captcha_error'][ $key ]			= stripcslashes( htmlspecialchars( $_POST['cntctfrm_captcha_error'][ $key ] ) );
					$options_submit['form_error'][ $key ]				= stripcslashes( htmlspecialchars( $_POST['cntctfrm_form_error'][ $key ] ) );
				}
			} else {
				$option_defaults = cntctfrm_get_option_defaults();

				if ( empty( $cntctfrm_options['language'] ) ) {
					$options_submit['name_label']				= $option_defaults['name_label'];
					$options_submit['address_label']			= $option_defaults['address_label'];
					$options_submit['email_label']				= $option_defaults['email_label'];
					$options_submit['phone_label']				= $option_defaults['phone_label'];
					$options_submit['subject_label']			= $option_defaults['subject_label'];
					$options_submit['message_label']			= $option_defaults['message_label'];
					$options_submit['attachment_label']			= $option_defaults['attachment_label'];
					$options_submit['attachment_tooltip']		= $option_defaults['attachment_tooltip'];
					$options_submit['send_copy_label']			= $option_defaults['send_copy_label'];
					$options_submit['thank_text']				= $_POST['cntctfrm_thank_text'];
					$options_submit['submit_label']				= $option_defaults['submit_label'];
					$options_submit['name_error']				= $option_defaults['name_error'];
					$options_submit['address_error']			= $option_defaults['address_error'];
					$options_submit['email_error']				= $option_defaults['email_error'];
					$options_submit['phone_error']				= $option_defaults['phone_error'];
					$options_submit['subject_error']			= $option_defaults['subject_error'];
					$options_submit['message_error']			= $option_defaults['message_error'];
					$options_submit['attachment_error']			= $option_defaults['attachment_error'];
					$options_submit['attachment_upload_error']	= $option_defaults['attachment_upload_error'];
					$options_submit['attachment_move_error']	= $option_defaults['attachment_move_error'];
					$options_submit['attachment_size_error']	= $option_defaults['attachment_size_error'];
					$options_submit['captcha_error']			= $option_defaults['captcha_error'];
					$options_submit['form_error']				= $option_defaults['form_error'];
					foreach ( $options_submit['thank_text'] as $key => $val ) {
						$options_submit['thank_text'][ $key ] = stripcslashes( htmlspecialchars( $val ) );
					}
				} else {
					$options_submit['name_label']['default']				= $option_defaults['name_label']['default'];
					$options_submit['address_label']['default']				= $option_defaults['address_label']['default'];
					$options_submit['email_label']['default']				= $option_defaults['email_label']['default'];
					$options_submit['phone_label']['default']				= $option_defaults['phone_label']['default'];
					$options_submit['subject_label']['default']				= $option_defaults['subject_label']['default'];
					$options_submit['message_label']['default']				= $option_defaults['message_label']['default'];
					$options_submit['attachment_label']['default']			= $option_defaults['attachment_label']['default'];
					$options_submit['attachment_tooltip']['default']		= $option_defaults['attachment_tooltip']['default'];
					$options_submit['send_copy_label']['default']			= $option_defaults['send_copy_label']['default'];
					$options_submit['submit_label']['default']				= $option_defaults['submit_label']['default'];
					$options_submit['name_error']['default']				= $option_defaults['name_error']['default'];
					$options_submit['address_error']['default']				= $option_defaults['address_error']['default'];
					$options_submit['email_error']['default']				= $option_defaults['email_error']['default'];
					$options_submit['phone_error']['default']				= $option_defaults['phone_error']['default'];
					$options_submit['subject_error']['default']				= $option_defaults['subject_error']['default'];
					$options_submit['message_error']['default']				= $option_defaults['message_error']['default'];
					$options_submit['attachment_error']['default']			= $option_defaults['attachment_error']['default'];
					$options_submit['attachment_upload_error']['default']	= $option_defaults['attachment_upload_error']['default'];
					$options_submit['attachment_move_error']['default']		= $option_defaults['attachment_move_error']['default'];
					$options_submit['attachment_size_error']['default']		= $option_defaults['attachment_size_error']['default'];
					$options_submit['captcha_error']['default']				= $option_defaults['captcha_error']['default'];
					$options_submit['form_error']['default']				= $option_defaults['form_error']['default'];

					foreach ( $_POST['cntctfrm_thank_text'] as $key => $val ) {
						$options_submit['thank_text'][ $key ] = stripcslashes( htmlspecialchars( $_POST['cntctfrm_thank_text'][ $key ] ) );
					}
				}
			}
			/* if 'FROM' field was changed */
			if ( ( 'custom' == $cntctfrm_options['from_email'] && 'custom' != $options_submit['from_email'] ) ||
				( 'custom' == $options_submit['from_email'] && $cntctfrm_options['custom_from_email'] != $options_submit['custom_from_email'] ) ) {
				$notice = __( "Email 'FROM' field option was changed, which may cause email messages being moved to the spam folder or email delivery failures.", 'contact-form-plugin' );
			}

			$options_submit['action_after_send'] = $_POST['cntctfrm_action_after_send'];
			$options_submit['redirect_url']	= esc_url( $_POST['cntctfrm_redirect_url'] );
			$cntctfrm_options = array_merge( $cntctfrm_options, $options_submit );

			if ( 0 == $options_submit['action_after_send']
				&& ( "" == trim( $options_submit['redirect_url'] )
				|| ! filter_var( $options_submit['redirect_url'], FILTER_VALIDATE_URL) ) ) {
					$error .= __(  "If the 'Redirect to page' option is selected then the URL field should be in the following format", 'contact-form-plugin' )." <code>http://your_site/your_page</code>";
					$cntctfrm_options['action_after_send'] = 1;
			}
			if ( 'user' == $options_submit['select_email'] ) {
				if ( false !== get_user_by( 'login', $options_submit['user_email'] ) ) {
					/**/
				} else {
					$error .= __(  "Such user does not exist.", 'contact-form-plugin' );
				}
			} else {
				if ( preg_match( '|,|', $options_submit['custom_email'] ) ) {
					$cntctfrm_custom_emails = explode( ',', $options_submit['custom_email'] );
				} else {
					$cntctfrm_custom_emails[0] = $options_submit['custom_email'];
				}
				foreach ( $cntctfrm_custom_emails as $cntctfrm_custom_email ) {
					if ( $cntctfrm_custom_email == "" || ! is_email( trim( $cntctfrm_custom_email ) ) ) {
						$error .= __( "Please enter a valid email address in the 'Use this email address' field.", 'contact-form-plugin' );
						break;
					}
				}
			}
			if ( 'custom' == $options_submit['from_email'] ) {
				if ( "" == $options_submit['custom_from_email']
					|| ! is_email( trim( $options_submit['custom_from_email'] ) ) ) {
					$error .= __( "Please enter a valid email address in the 'FROM' field.", 'contact-form-plugin' );
				}
			}

			if ( '' == $error ) {
				if ( 'pro' == $contact_form_multi_active && $options_main = get_option( 'cntctfrmmltpr_options_main' ) ) {
					if ( $options_main['id_form'] !== $_SESSION['cntctfrmmlt_id_form'] )
						add_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
					else if ( $options_main['id_form'] == $_SESSION['cntctfrmmlt_id_form'] )
						update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
				} elseif ( $contact_form_multi_active ) {
					$options_main = get_option( 'cntctfrmmlt_options_main' );

					if ( $options_main['id_form'] !== $_SESSION['cntctfrmmlt_id_form'] )
						add_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
					else if ( $options_main['id_form'] == $_SESSION['cntctfrmmlt_id_form'] )
						update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
				} else {
					update_option( 'cntctfrm_options', $cntctfrm_options );
				}
				$message = __( "Settings saved.", 'contact-form-plugin' );
			} else {
				$error .=  ' ' . __( "Settings are not saved.", 'contact-form-plugin' );
			}
		}

		$bws_hide_premium_options_check = bws_hide_premium_options_check( get_option( 'cntctfrm_options' ) );

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename, 'cntctfrm_options' );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
			elseif ( ! empty( $go_pro_result['message'] ) )
				$message = $go_pro_result['message'];
		}

		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
			$option_defaults = cntctfrm_get_option_defaults();
			if ( $contact_form_multi_active ) {
				$contact_form_multi_options = array(
					'display_captcha'			=> 0,
					'display_google_captcha'	=> 0,
					'display_subscribe'			=> 0,
					'save_email_to_db'			=> 1,
				);
				$option_defaults = array_merge( $option_defaults, $contact_form_multi_options );
			}

			$cntctfrm_options = $option_defaults;
			if ( $contact_form_multi_active ) {
				if ( isset( $_SESSION['cntctfrmmlt_id_form'] ) && get_option( 'cntctfrmmlt_options_' . $_SESSION['cntctfrmmlt_id_form'] ) ) {
					update_option( 'cntctfrmmlt_options_' . $_SESSION['cntctfrmmlt_id_form'] , $cntctfrm_options );
				} else {
					update_option( 'cntctfrmmlt_options', $cntctfrm_options );
				}
			} else {
				update_option( 'cntctfrm_options', $cntctfrm_options );
			}
			$message = __( 'All plugin settings were restored.', 'contact-form-plugin' );
		} /* end */ ?>
		<div class="wrap">
			<h1><?php _e( "Contact Form Settings", 'contact-form-plugin' ); ?></h1>
			<div id="cntctfrm_nav_container">
				<ul class="subsubsub cntctfrm_how_to_use">
					<li><a href="https://docs.google.com/document/d/1qZYPJhkSdVyyM6XO5WfiBcTS2Sa9_9UMn4vS2g48JRY/" target="_blank"><?php _e( 'How to Use Step-by-step Instruction', 'contact-form-plugin' ); ?></a></li>
				</ul>
				<h2 id="cntctfrm_nav" class="nav-tab-wrapper">
					<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>"  href="admin.php?page=contact_form.php"><?php _e( 'Settings', 'contact-form-plugin' ); ?></a>
					<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'additional' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=contact_form.php&amp;action=additional"><?php _e( 'Additional settings', 'contact-form-plugin' ); ?></a>
					<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'appearance' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=contact_form.php&amp;action=appearance"><?php _e( 'Appearance', 'contact-form-plugin' ); ?></a>
					<a class="nav-tab <?php if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=contact_form.php&amp;action=custom_code"><?php _e( 'Custom code', 'contact-form-plugin' ); ?></a>
					<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=contact_form.php&amp;action=go_pro"><?php _e( 'Go PRO', 'contact-form-plugin' ); ?></a>
				</h2>
				<div class="cntctfrm_clear"></div>
			</div>
			<div class="updated fade below-h2" <?php if ( $message == "" || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error below-h2" <?php if ( "" == $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php bws_show_settings_notice();
			if ( ! empty( $hide_result['message'] ) ) { ?>
				<div class="updated fade below-h2"><p><strong><?php echo $hide_result['message']; ?></strong></p></div>
			<?php }
			if ( ! empty( $notice ) ) { ?>
				<div class="error below-h2"><p><strong><?php _e( 'Notice', 'contact-form-plugin' ) . ":"; ?></strong> <?php echo $notice; ?></p></div>
			<?php }
			if ( ( ! isset( $_GET['action'] ) || ( 'go_pro' != $_GET['action'] && 'custom_code' != $_GET['action'] ) ) && ! $contact_form_multi_active ) { ?>
				<h3 class="nav-tab-wrapper">
					<span class="nav-tab nav-tab-active"><?php _e( 'NEW_FORM', 'contact-form-plugin' )?></span>
					<a id="cntctfrm_show_multi_notice" class="nav-tab" target="_new" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form-multi/?k=747ca825fb44711e2d24e40697747bc6&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" title="<?php _e( "If you want to create multiple contact forms, please install the Contact Form Multi plugin.", 'contact-form-plugin' ); ?>">+</a>
				</h3>
			<?php }
			if ( ! isset( $_GET['action'] ) || 'additional' == $_GET['action'] ) {
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else { ?>
					<form id="cntctfrm_settings_form" class="bws_form" method="post" action="">
						<div style="margin: 20px 0;">
							<?php printf( __( "If you would like to add a Contact Form to your page or post, please use %s button", 'contact-form-plugin' ),
								'<span class="bws_code"><span class="bwsicons bwsicons-shortcode"></span></span>'
							); ?>
							<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
								<div class="bws_hidden_help_text" style="min-width: 260px;">
									<?php printf(
										__( "You can add the Contact Form to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s or %s where * stands for Contact Form language.", 'contact-form-plugin' ),
										'<span class="bws_code"><span class="bwsicons bwsicons-shortcode"></span></span>',
										sprintf( '<br/><span class="bws_code">[bestwebsoft_contact_form%s]</span><br/>', ( ! $contact_form_multi_active ) ? '' : ' id=' . $_SESSION['cntctfrmmlt_id_form'] ),
										sprintf( '<br/><span class="bws_code">[bestwebsoft_contact_form%s lang=*]</span>,<br/>', ( ! $contact_form_multi_active ) ? '' : ' id=' . $_SESSION['cntctfrmmlt_id_form'] )
									); ?>
								</div>
							</div>
						</div>
						<div <?php if ( isset( $_GET['action'] ) ) echo 'style="display: none;"'; ?> >
							<p><?php _e( "If you leave the fields empty, the messages will be sent to the email address specified during registration.", 'contact-form-plugin' ); ?></p>
							<table class="form-table" style="width:auto;">
								<tr valign="top">
									<th scope="row"><?php _e( "The user's email address", 'contact-form-plugin' ); ?>: </th>
									<td colspan="2">
										<label><input type="radio" id="cntctfrm_select_email_user" name="cntctfrm_select_email" value="user" <?php if ( $cntctfrm_options['select_email'] == 'user' ) echo 'checked="checked" '; ?>/>
										<select class="cntctfrm_user_email" name="cntctfrm_user_email">
											<option disabled><?php _e( "Select a username", 'contact-form-plugin' ); ?></option>
												<?php foreach ( $userslogin as $key => $value ) {
													if ( isset( $value->data ) ) {
														if ( $value->data->user_email != '' ) { ?>
															<option value="<?php echo $value->data->user_login; ?>" <?php if ( $cntctfrm_options['user_email'] == $value->data->user_login ) echo 'selected="selected" '; ?>><?php echo $value->data->user_login; ?></option>
														<?php }
													} else {
														if ( $value->user_email != '' ) { ?>
															<option value="<?php echo $value->user_login; ?>" <?php if ( $cntctfrm_options['user_email'] == $value->user_login ) echo 'selected="selected" '; ?>><?php echo $value->user_login; ?></option>
														<?php }
													}
												} ?>
										</select>
										<span class="bws_info cntctfrm_info"><?php _e( "Select a username of the person who should get the messages from the contact form.", 'contact-form-plugin' ); ?></span></label>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Use this email address", 'contact-form-plugin' ); ?>:</th>
									<td colspan="2">
										<label><input type="radio" id="cntctfrm_select_email_custom" name="cntctfrm_select_email" value="custom" <?php if ( 'custom' == $cntctfrm_options['select_email'] ) echo 'checked="checked" '; ?>/>
										<input type="text" name="cntctfrm_custom_email" value="<?php echo $cntctfrm_options['custom_email']; ?>" maxlength="500" />
										<span class="bws_info cntctfrm_info"><?php _e( "Enter the email address for receiving messages", 'contact-form-plugin' ); ?>.</span></label>
									</td>
								</tr>
							</table>
							<?php if ( ! $bws_hide_premium_options_check ) { ?>
								<div class="bws_pro_version_bloc">
									<div class="bws_pro_version_table_bloc">
										<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'contact-form-plugin' ); ?>"></button>
										<div class="bws_table_bg"></div>
										<table class="form-table bws_pro_version">
											<tr valign="top">
												<th scope="row"><?php _e( "Add department selectbox to the contact form", 'contact-form-plugin' ); ?>:</th>
												<td colspan="2">
													<input type="radio" name="cntctfrm_select_email" value="departments" disabled="disabled" />
													<div><img style="width:100%;" src="<?php echo plugins_url( 'images/pro_screen_1.png', __FILE__ ); ?>" alt="" /></div>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" colspan="2">
													* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'contact-form-plugin' ); ?>
												</th>
											</tr>
										</table>
									</div>
									<div class="bws_pro_version_tooltip">
										<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=697c5e74f39779ce77850e11dbe21962&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="Contact Form Pro">
											<?php _e( 'Upgrade to Pro', 'contact-form-plugin' ); ?>
										</a>
										<div class="clear"></div>
									</div>
								</div>
							<?php } ?>
							<table class="form-table" style="width:auto;">
								<tr valign="top">
									<th scope="row"><?php _e( "Save emails to the database", 'contact-form-plugin' ); ?> </th>
									<td colspan="2">
										<?php if ( array_key_exists( 'contact-form-to-db/contact_form_to_db.php', $all_plugins ) || array_key_exists( 'contact-form-to-db-pro/contact_form_to_db_pro.php', $all_plugins ) ) {
											if ( array_key_exists( 'contact-form-to-db', $cntctfrm_related_plugins ) ) {
												$save_emails  = false;
												if ( ! $contact_form_multi_active ) {
													$save_emails = ! empty( $cntctfrm_related_plugins['contact-form-to-db']['options'][ $cntctfrm_related_plugins['contact-form-to-db']['save_option'] ] ) ? true : false;
												} else {
													$save_emails = ! empty( $cntctfrm_options['save_email_to_db'] ) ? true : false;
												}
												if (  ! $contact_form_multi_active || ! empty( $cntctfrm_related_plugins['contact-form-to-db']['options'][ $cntctfrm_related_plugins['contact-form-to-db']['save_option'] ] ) ) { ?>
													<label><input type="checkbox" name="cntctfrm_save_email_to_db" value="1" <?php if ( $save_emails ) echo 'checked="checked"'; ?> />
														<span class="bws_info"> (<?php _e( 'Using', 'contact-form-plugin' ); ?>
															<a href="<?php echo self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['contact-form-to-db']['settings_page'] ); ?>" target="_blank">Contact Form to DB by BestWebSoft</a>)
														</span>
													</label>
												<?php } else { ?>
													<label><input type="checkbox" name="cntctfrm_save_email_to_db" value="1" disabled="disabled" <?php if ( $save_emails ) echo 'checked="checked"'; ?> /></label>
													<span class="bws_info">&nbsp;(<?php _e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
														<?php printf( '<a href="%s" target="_blank"> Contact Form to DB %s</a>&nbsp;)',
															self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['contact-form-to-db']['settings_page'] ),
															__( 'settings page', 'contact-form-plugin' ) ); ?>
													</span>
												<?php }
											} else { ?>
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_save_email_to_db" value="1" <?php if ( ! empty( $cntctfrm_options["save_email_to_db"] ) ) echo 'checked="checked"'; ?> />
													<span class="bws_info">(<?php _e( 'Using', 'contact-form-plugin' ); ?> Contact Form to DB by BestWebSoft)
													<?php printf( '<a href="%s" target="_blank">%s Contact Form to DB</a>', self_admin_url( 'plugins.php' ), __( 'Activate', 'contact-form-plugin' ) ); ?>
													</span>
												</label>
											<?php }
										} else { ?>
											<label><input disabled="disabled" type="checkbox" name="cntctfrm_save_email_to_db" value="1" />
												<span class="bws_info">(<?php _e( 'Using', 'contact-form-plugin' ); ?> Contact Form to DB by BestWebSoft)
													<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/contact-form-to-db/?k=19d806f45d866e70545de83169b274f2&amp;pn=77&amp;v=%s&amp;wp_v=%s"> %s Contact Form to DB</a>', $cntctfrm_plugin_info["Version"], $wp_version, __( 'Download', 'contact-form-plugin' ) ); ?>
												</span>
											</label>
										<?php } ?>
									</td>
								</tr>
							</table>
						</div>
						<!-- end of main 'settings' div -->
						<div <?php if ( ! isset( $_GET['action'] ) ) echo 'style="display: none;"'; ?>>
							<table class="form-table" style="width:auto;">
								<tr>
									<th scope="row"><?php _e( 'Sending method', 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<fieldset>
											<label>
												<input type='radio' name='cntctfrm_mail_method' value='wp-mail' <?php if ( 'wp-mail' == $cntctfrm_options['mail_method'] ) echo 'checked="checked" '; ?>/>
												<?php _e( 'Wp-mail', 'contact-form-plugin' ); ?>
											</label>
											<label><span class="bws_info">(<?php _e( 'You can use the Wordpress wp_mail function for mailing', 'contact-form-plugin' ); ?>)</span></label><br />
											<label>
												<input type='radio' name='cntctfrm_mail_method' value='mail' <?php if ( 'mail' == $cntctfrm_options['mail_method'] ) echo 'checked="checked" '; ?>/>
												<?php _e( 'Mail', 'contact-form-plugin' ); ?>
											</label>
											<label><span class="bws_info">(<?php _e( 'You can use the PHP mail function for mailing', 'contact-form-plugin' ); ?>)</span></label>
										</fieldset>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "'FROM' field", 'contact-form-plugin' ); ?></th>
									<td class="cntctfrm_td_name" style="vertical-align: top;">
										<table id="cntctfrm_table_from_name">
											<tbody>
												<tr>
													<td colspan="2"><?php _e( "Name", 'contact-form-plugin' ); ?></td>
												</tr>
												<tr>
													<td class="cntctfrm_radio_from_name"><input type="radio" id="cntctfrm_select_from_custom_field" name="cntctfrm_select_from_field" value="custom" <?php if ( 'custom' == $cntctfrm_options['select_from_field'] ) echo 'checked="checked" '; ?> /></td>
													<td><input type="text" name="cntctfrm_from_field" value="<?php echo stripslashes( $cntctfrm_options['from_field'] ); ?>" size="18" maxlength="100" /></td>
												</tr>
												<tr>
													<td class="cntctfrm_radio_from_name">
														<input type="radio" id="cntctfrm_select_from_field" name="cntctfrm_select_from_field" value="user_name" <?php if ( 'user_name' == $cntctfrm_options['select_from_field'] ) echo 'checked="checked" '; ?>/>
													</td>
													<td>
														<label for="cntctfrm_select_from_field"><?php _e( "User name", 'contact-form-plugin' ); ?></label>
														<div class="bws_help_box dashicons dashicons-editor-help">
															<div class="bws_hidden_help_text" style="min-width: 200px;"><?php echo __( "The name of the user who fills the form will be used in the field 'From'.", 'contact-form-plugin' ); ?></div>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
									<td class="cntctfrm_td_email" style="vertical-align: top;">
										<table id="cntctfrm_table_from_email">
											<tbody>
												<tr>
													<td colspan="2"><?php _e( "Email", 'contact-form-plugin' ); ?></td>
												</tr>
												<tr>
													<td class="cntctfrm_radio_from_email"><input type="radio" id="cntctfrm_from_custom_email" name="cntctfrm_from_email" value="custom" <?php if ( 'custom' == $cntctfrm_options['from_email'] ) echo 'checked="checked" '; ?>/></td>
													<td><input type="text" name="cntctfrm_custom_from_email" value="<?php echo $cntctfrm_options['custom_from_email']; ?>" maxlength="100" /></td>
												</tr>
												<tr>
													<td class="cntctfrm_radio_from_email">
														<input type="radio" id="cntctfrm_from_email" name="cntctfrm_from_email" value="user" <?php if ( 'user' == $cntctfrm_options['from_email'] ) echo 'checked="checked" '; ?>/>
													</td>
													<td>
														<label for="cntctfrm_from_email"><?php _e( "User email", 'contact-form-plugin' ); ?></label>
													<div class="bws_help_box dashicons dashicons-editor-help">
														<div class="bws_hidden_help_text" style="min-width: 200px;"><?php echo __( "The email address of the user who fills the form will be used in the field 'From'.", 'contact-form-plugin' ); ?></div>
													</div>
													</td>
												</tr>
												<tr>
													<td>
													</td>
													<td>
														<div>
															<span class="bws_info">(<?php _e( "If this option is changed, email messages may be moved to the spam folder or email delivery failures may occur.", 'contact-form-plugin' ); ?>)</span>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Required symbol", 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<input type="text" id="cntctfrm_required_symbol" name="cntctfrm_required_symbol" value="<?php echo $cntctfrm_options['required_symbol']; ?>" maxlength="100" />
									</td>
								</tr>
							</table>
							<br />
							<table class="cntctfrm_settings_table" style="width: auto;">
								<thead>
									<tr valign="top">
										<th scope="row"><?php _e( "Fields", 'contact-form-plugin' ); ?></th>
										<th><?php _e( "Used", 'contact-form-plugin' ); ?></th>
										<th><?php _e( "Required", 'contact-form-plugin' ); ?></th>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<th><?php _e( "Visible", 'contact-form-plugin' ); ?></th>
											<th><?php _e( "Disabled for editing", 'contact-form-plugin' ); ?></th>
											<th scope="row" ><?php _e( "Field's default value", 'contact-form-plugin' ); ?></th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php if ( ! $bws_hide_premium_options_check ) { ?>
										<tr valign="top" >
											<td><?php _e( "Department selectbox", 'contact-form-plugin' ); ?></td>
											<td class="bws_pro_version"></td>
											<td class="bws_pro_version">
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_required_department_field" value="1" />
												<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version"></td>
											<td class="bws_pro_version"></td>
											<td class="bws_pro_version"></td>
										</tr>
									<?php } ?>
									<tr valign="top">
										<td><?php _e( "Name", 'contact-form-plugin' ); ?></td>
										<td>
											<label><input type="checkbox" name="cntctfrm_display_name_field" value="1" <?php if ( '1' == $cntctfrm_options['display_name_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Used", 'contact-form-plugin' ); ?></span></label>
										</td>
										<td>
											<label><input type="checkbox" id="cntctfrm_required_name_field" name="cntctfrm_required_name_field" value="1" <?php if ( '1' == $cntctfrm_options['required_name_field'] ) echo 'checked="checked" '; ?> />
											<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
										</td>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<td class="bws_pro_version">
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_visible_name" value="1" checked="checked" />
												<span class="cntctfrm_mobile_title"><?php _e( "Visible", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_disabled_name" value="1" />
												<span class="cntctfrm_mobile_title"><?php _e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<input disabled="disabled" type="checkbox" name="cntctfrm_default_name" value="1" />
												<?php _e( "Use User's name as a default value if the user is logged in.", 'contact-form-plugin' ); ?><br />
												<span class="bws_info">(<?php _e( "'Visible' and 'Disabled for editing' options will be applied only to logged-in users.", 'contact-form-plugin' ); ?>)</span>
											</td>
										<?php } ?>
									</tr>
									<?php if ( ! $bws_hide_premium_options_check ) { ?>
										<tr valign="top">
											<td><?php _e( "Location selectbox", 'contact-form-plugin' ); ?></td>
											<td class="bws_pro_version">
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_selectbox" value="1" />
												<span class="cntctfrm_mobile_title"><?php _e( "Used", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_required_selectbox" value="1" />
												<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version"></td>
											<td class="bws_pro_version"></td>
											<td class="bws_pro_version">
												<label>
													<span><?php _e( "Field's default value", 'contact-form-plugin' ); ?></span>
													<input disabled="disabled" type="file" name="cntctfrm_default_location" />
												</label>
											</td>
										</tr>
									<?php } ?>
									<tr valign="top">
										<td><?php _e( "Address", 'contact-form-plugin' ); ?></td>
										<td>
											<label><input type="checkbox" id="cntctfrm_display_address_field" name="cntctfrm_display_address_field" value="1" <?php if ( '1' == $cntctfrm_options['display_address_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Used", 'contact-form-plugin' ); ?></span></label>
										</td>
										<td>
											<label><input type="checkbox" id="cntctfrm_required_address_field" name="cntctfrm_required_address_field" value="1" <?php if ( '1' == $cntctfrm_options['required_address_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
										</td>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<td></td>
											<td></td>
											<td></td>
										<?php } ?>
									</tr>
									<tr valign="top">
										<td><?php _e( "Email Address", 'contact-form-plugin' ); ?></td>
										<td></td>
										<td>
											<label><input type="checkbox" id="cntctfrm_required_email_field" name="cntctfrm_required_email_field" value="1" <?php if ( '1' == $cntctfrm_options['required_email_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
										</td>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<td class="bws_pro_version">
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_visible_email" value="1" checked="checked" />
												<span class="cntctfrm_mobile_title"><?php _e( "Visible", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_disabled_email" value="1" />
												<span class="cntctfrm_mobile_title"><?php _e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<input disabled="disabled" type="checkbox" name="cntctfrm_default_email" value="1" />
													<?php _e( "Use User's email as a default value if the user is logged in.", 'contact-form-plugin' ); ?><br />
												<span class="bws_info">(<?php _e( "'Visible' and 'Disabled for editing' options will be applied only to logged-in users.", 'contact-form-plugin' ); ?>)</span>
											</td>
										<?php } ?>
									</tr>
									<tr valign="top">
										<td><?php _e( "Phone number", 'contact-form-plugin' ); ?></td>
										<td>
											<label><input type="checkbox" id="cntctfrm_display_phone_field" name="cntctfrm_display_phone_field" value="1" <?php if ( '1' == $cntctfrm_options['display_phone_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Used", 'contact-form-plugin' ); ?></span></label>
										</td>
										<td>
											<label><input type="checkbox" id="cntctfrm_required_phone_field" name="cntctfrm_required_phone_field" value="1" <?php if ( '1' == $cntctfrm_options['required_phone_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
										</td>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<td></td>
											<td></td>
											<td></td>
										<?php } ?>
									</tr>
									<tr valign="top">
										<td><?php _e( "Subject", 'contact-form-plugin' ); ?></td>
										<td></td>
										<td>
											<label><input type="checkbox" id="cntctfrm_required_subject_field" name="cntctfrm_required_subject_field" value="1" <?php if ( '1' == $cntctfrm_options['required_subject_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
										</td>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<td class="bws_pro_version">
												<label><input class="subject" disabled="disabled" type="checkbox" name="cntctfrm_visible_subject" value="1" checked="checked" />
												<span class="cntctfrm_mobile_title"><?php _e( "Visible", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<label><input class="subject" disabled="disabled" type="checkbox" name="cntctfrm_disabled_subject" value="1" />
												<span class="cntctfrm_mobile_title"><?php _e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<label>
													<span><?php _e( "Field's default value", 'contact-form-plugin' ); ?></span>
													<input class="subject" disabled="disabled" type="text" name="cntctfrm_default_subject" value="" />
												</label>
											</td>
										<?php } ?>
									</tr>
									<tr valign="top">
										<td><?php _e( "Message", 'contact-form-plugin' ); ?></td>
										<td></td>
										<td>
											<label><input type="checkbox" id="cntctfrm_required_message_field" name="cntctfrm_required_message_field" value="1" <?php if ( '1' == $cntctfrm_options['required_message_field'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Required", 'contact-form-plugin' ); ?></span></label>
										</td>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<td class="bws_pro_version">
												<label><input class="message" disabled="disabled" type="checkbox" name="cntctfrm_visible_message" value="1" checked="checked" />
												<span class="cntctfrm_mobile_title"><?php _e( "Visible", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<label><input class="message" disabled="disabled" disabled="disabled" type="checkbox" name="cntctfrm_disabled_message" value="1" />
												<span class="cntctfrm_mobile_title"><?php _e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
											</td>
											<td class="bws_pro_version">
												<label>
													<span><?php _e( "Field's default value", 'contact-form-plugin' ); ?></span>
													<input class="message" disabled="disabled" type="text" name="cntctfrm_default_message" value="" />
												</label>
											</td>
										<?php } ?>
									</tr>
									<?php if ( ! $bws_hide_premium_options_check ) { ?>
										<tr valign="top">
											<td></td>
											<td></td>
											<td></td>
											<td colspan="3" class="bws_pro_version_tooltip bws_pro_version cntctfrm_pro_version_table_block">
												<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=697c5e74f39779ce77850e11dbe21962&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="Contact Form Pro">
													<?php _e( 'Upgrade to Pro', 'contact-form-plugin' ); ?>
												</a>
												<div class="clear"></div>
											</td>
										</tr>
									<?php } ?>
									<tr valign="top">
										<td>
											<?php _e( "Attachment block", 'contact-form-plugin' ); ?>
											<div class="bws_help_box dashicons dashicons-editor-help">
												<div class="bws_hidden_help_text" style="min-width: 200px;"><?php echo __( "Users can attach the following file formats", 'contact-form-plugin' ) . ": html, txt, css, gif, png, jpeg, jpg, tiff, bmp, ai, eps, ps, csv, rtf, pdf, doc, docx, xls, xlsx, zip, rar, wav, mp3, ppt, aar, sce"; ?></div>
											</div>
										</td>
										<td>
											<label><input type="checkbox" id="cntctfrm_attachment" name="cntctfrm_attachment" value="1" <?php if ( '1' == $cntctfrm_options['attachment'] ) echo 'checked="checked" '; ?>/>
											<span class="cntctfrm_mobile_title"><?php _e( "Used", 'contact-form-plugin' ); ?></span></label>
										</td>
										<td></td>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<td></td>
											<td></td>
											<td></td>
										<?php } ?>
									</tr>
								</tbody>
							</table>
							<table class="form-table" style="width:auto;">
								<tr valign="top">
									<th scope="row"><?php _e( "Add to the form", 'contact-form-plugin' ); ?></th>
									<td colspan="3"><fieldset>
										<div>
											<label>
												<input type="checkbox" id="cntctfrm_attachment_explanations" name="cntctfrm_attachment_explanations" value="1" <?php if ( '1' == $cntctfrm_options['attachment_explanations'] && '1' == $cntctfrm_options['attachment'] ) echo 'checked="checked" '; ?>/>
												<?php _e( "Tips below the Attachment", 'contact-form-plugin' ); ?>
											</label>
											<?php echo bws_add_help_box( '<img src="' . plugins_url( 'images/tooltip_attachment_tips.png', __FILE__ ) . '" />', 'bws-hide-for-mobile bws-auto-width' ); ?>
										</div>
										<div>
											<label>
												<input type="checkbox" id="cntctfrm_send_copy" name="cntctfrm_send_copy" value="1" <?php if ( '1' == $cntctfrm_options['send_copy'] ) echo 'checked="checked" '; ?>/>
												<?php _e( "'Send me a copy' block", 'contact-form-plugin' ); ?>
											</label>
											<?php echo bws_add_help_box( '<img src="' . plugins_url( 'images/tooltip_sendme_block.png', __FILE__ ) . '" />', 'bws-hide-for-mobile bws-auto-width' ); ?>
										</div>
										<div style="clear: both;">
											<?php if ( array_key_exists( 'subscriber/subscriber.php', $all_plugins ) || array_key_exists( 'subscriber-pro/subscriber-pro.php', $all_plugins ) ) {
												if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
													if ( ! $contact_form_multi_active ) {
														$display_subscriber = ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] ) ? true : false;
													} else {
														$display_subscriber = ! empty( $cntctfrm_options['display_subscribe'] ) ? true : false;
													}
													if ( ! $contact_form_multi_active || ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] ) ) { ?>
														<label><input type="checkbox" name="cntctfrm_display_subscriber" value="1" <?php if ( $display_subscriber ) echo 'checked="checked"'; ?> /> Subscriber by BestWebSoft</label>
													<?php } else { ?>
														<label><input type="checkbox" name="cntctfrm_display_subscriber" value="1" disabled="disabled" <?php if ( $display_subscriber ) echo 'checked="checked"'; ?> /> Subscriber by BestWebSoft</label>
														<span class="bws_info">&nbsp;(<?php _e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
															<?php printf( '<a href="%s" target="_blank"> Subscriber %s</a>&nbsp;)',
																network_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['subscriber']['settings_page'] ),
																__( 'settings page', 'contact-form-plugin' ) ); ?>
														</span>
													<?php }
												} else { ?>
													<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_subscriber" value="1" <?php if ( isset( $cntctfrm_options['display_subscribe'] ) && 1 == $cntctfrm_options['display_subscribe'] ) echo 'checked="checked"'; ?> /> Subscriber by BestWebSoft</label>
													<span class="bws_info">
														<?php if ( ! is_multisite() ) {
															printf( '<a href="%s" target="_blank"> %s Subscriber</a>', admin_url( 'plugins.php' ), __( 'Activate', 'contact-form-plugin' ) );
														} else {
															printf( '<a href="%s" target="_blank"> %s Subscriber</a>', network_admin_url( 'plugins.php' ), __( 'Activate for network', 'contact-form-plugin' ) );
														} ?>
													</span>
												<?php }
											} else { ?>
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_subscriber" value="1" />	Subscriber by BestWebSoft</label>
												<span class="bws_info">
													<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/subscriber/?k=a9dfd3fa8513784c36622993b350b19e&amp;pn=77&amp;v=%s&amp;wp_v=%s">%s Subscriber</a>', $cntctfrm_plugin_info["Version"], $wp_version, __( 'Download', 'contact-form-plugin' ) ); ?>
												</span>
											<?php } ?>
										</div>
										<div style="clear: both;">
											<?php if ( array_key_exists( 'captcha/captcha.php', $all_plugins ) || array_key_exists( 'captcha-plus/captcha-plus.php', $all_plugins ) || array_key_exists( 'captcha-pro/captcha_pro.php', $all_plugins ) ) {
												if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) ) {
													$captcha_enabled = ! empty( $cntctfrm_related_plugins['captcha']['enabled'] ) ? true : false;

													if ( ! $contact_form_multi_active ) {
														$display_captcha = $captcha_enabled;
													} else {
														$display_captcha = ! empty( $cntctfrm_options['display_captcha'] ) ? true : false;
													}

													if ( ! $contact_form_multi_active ) { ?>
														<label><input type="checkbox" name="cntctfrm_display_captcha" value="1" <?php if ( $display_captcha ) echo 'checked="checked"'; ?> /> Captcha by BestWebSoft </label>
													<?php } else {
														if ( $captcha_enabled ) { ?>
															<label><input type="checkbox" name="cntctfrm_display_captcha" value="1" <?php if ( $display_captcha ) echo 'checked="checked"'; ?> /> Captcha by BestWebSoft </label>
														<?php } else { ?>
															<label>
																<input type="checkbox" name="cntctfrm_display_captcha" value="1" disabled="disabled" <?php if ( $display_captcha ) echo 'checked="checked"'; ?> /> Captcha by BestWebSoft
																<span class="bws_info">&nbsp;(<?php _e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
																	<?php printf( '<a href="%s" target="_blank"> Captcha %s</a>&nbsp;)',
																	self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['captcha']['settings_page'] ),
																	__( 'settings page', 'contact-form-plugin' ) ); ?>
																</span>
															</label>
														<?php }
													}
												} else { ?>
													<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_captcha" value="1" /> Captcha by BestWebSoft</label>
													<span class="bws_info">
														<?php printf( '<a href="%s" target="_blank">%s Captcha</a>', self_admin_url( 'plugins.php' ), __( 'Activate', 'contact-form-plugin' ) ); ?>
													</span>
												<?php }
											} else { ?>
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_captcha" value="1" /> Captcha by BestWebSoft</label>
												<span class="bws_info">
													<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=19ac1e9b23bea947cfc4a9b8e3326c03&amp;pn=77&amp;v=%s&amp;wp_v=%s">%s Captcha</a>', $cntctfrm_plugin_info["Version"], $wp_version, __( 'Download', 'contact-form-plugin' ) ) ?>
												</span>
											<?php } ?>
										</div>
										<div style="clear: both;">
											<?php if ( array_key_exists( 'google-captcha/google-captcha.php', $all_plugins ) || array_key_exists( 'google-captcha-pro/google-captcha-pro.php', $all_plugins ) ) {
												if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) ) {
													if ( ! $contact_form_multi_active ) {
														$display_google_captcha = ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ? true : false;
													} else {
														$display_google_captcha = ! empty( $cntctfrm_options['display_google_captcha'] ) ? true : false;
													}

													if ( ! $contact_form_multi_active || ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) { ?>
														<label><input type="checkbox" name="cntctfrm_display_google_captcha" value="1" <?php if ( $display_google_captcha ) echo 'checked="checked"'; ?> /> Google Captcha (reCaptcha) by BestWebSoft</label>
													<?php } else { ?>
														<label>
															<input type="checkbox" name="cntctfrm_display_google_captcha" value="1" disabled="disabled" <?php if ( $display_google_captcha ) echo 'checked="checked"'; ?> /> Google Captcha (reCaptcha) by BestWebSoft
															<span class="bws_info">&nbsp;(<?php _e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
																<?php printf( '<a href="%s" target="_blank"> Google Captcha %s</a>&nbsp;)',
																self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['google-captcha']['settings_page'] ),
																__( 'settings page', 'contact-form-plugin' ) ); ?>
															</span>
														</label>
													<?php }
												} else { ?>
													<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_google_captcha" value="1" /> Google Captcha (reCaptcha) by BestWebSoft</label>
													<span class="bws_info">
														<?php printf( '<a href="%s" target="_blank">%s Google Captcha</a>', self_admin_url( 'plugins.php' ), __( 'Activate', 'contact-form-plugin' ) ); ?>
													</span>
												<?php }
											} else { ?>
												<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_google_captcha" value="1" /> Google Captcha (reCaptcha) by BestWebSoft</label> <span class="bws_info">
													<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=7d74e61dd1cea23d0e9bf2fa88b5b117&amp;pn=77&amp;v=%s&amp;wp_v=%s">%s Google Captcha</a>', $cntctfrm_plugin_info["Version"], $wp_version, __( 'Download', 'contact-form-plugin' ) ) ?>
													</span>
											<?php } ?>
										</div>
										<?php if ( ! $bws_hide_premium_options_check ) { ?>
											<div class="bws_pro_version_bloc">
												<div class="bws_pro_version_table_bloc">
													<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'contact-form-plugin' ); ?>"></button>
													<div class="bws_table_bg"></div>
													<div class="bws_pro_version">
														<fieldset>
															<label><input disabled="disabled" type="checkbox" value="1" name="cntctfrm_display_privacy_check"> <?php _e( 'Agreement checkbox', 'contact-form-plugin' ); ?> <span class="bws_info">(<?php _e( 'Required checkbox for submitting the form', 'contact-form-plugin' ); ?>)</span></label><br />
															<label><input disabled="disabled" type="checkbox" value="1" name="cntctfrm_display_optional_check"> <?php _e( 'Optional checkbox', 'contact-form-plugin' ); ?> <span class="bws_info">(<?php _e( 'Optional checkbox, the results of which will be displayed in email', 'contact-form-plugin' ); ?>)</span></label>
														</fieldset>
													</div>
													<div style="padding: 10px; ">
														* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'contact-form-plugin' ); ?>
													</div>
												</div>
												<div class="bws_pro_version_tooltip">
													<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=697c5e74f39779ce77850e11dbe21962&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="Contact Form Pro">
														<?php _e( 'Upgrade to Pro', 'contact-form-plugin' ); ?>
													</a>
													<div class="clear"></div>
												</div>
											</div>
										<?php } ?>
									</fieldset></td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Delete an attachment file from the server after the email is sent", 'contact-form-plugin' ); ?> </th>
									<td colspan="3">
										<input type="checkbox" id="cntctfrm_delete_attached_file" name="cntctfrm_delete_attached_file" value="1" <?php if ( '1' == $cntctfrm_options['delete_attached_file'] ) echo 'checked="checked" '; ?>/>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Email in HTML format sending", 'contact-form-plugin' ); ?></th>
									<td colspan="2"><input type="checkbox" name="cntctfrm_html_email" value="1" <?php if ( '1' == $cntctfrm_options['html_email'] ) echo 'checked="checked" '; ?>/></td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Display additional info in the email", 'contact-form-plugin' ); ?></th>
									<td style="width:15px;" class="cntctfrm_td_top_align">
										<input type="checkbox" id="cntctfrm_display_add_info" name="cntctfrm_display_add_info" value="1" <?php if ( '1' == $cntctfrm_options['display_add_info'] ) echo 'checked="checked" '; ?>/>
									</td>
									<td class="cntctfrm_display_add_info_block" <?php if ( '0' == $cntctfrm_options['display_add_info'] ) echo 'style="display:none"'; ?>>
										<fieldset>
											<label><input type="checkbox" id="cntctfrm_display_sent_from" name="cntctfrm_display_sent_from" value="1" <?php if ( '1' == $cntctfrm_options['display_sent_from'] ) echo 'checked="checked" '; ?>/> <?php _e( "Sent from (IP address)", 'contact-form-plugin' ); ?></label> <label class="bws_info"><?php _e( "Example: Sent from (IP address):	127.0.0.1", 'contact-form-plugin' ); ?></label><br />
											<label><input type="checkbox" id="cntctfrm_display_date_time" name="cntctfrm_display_date_time" value="1" <?php if ( '1' == $cntctfrm_options['display_date_time'] ) echo 'checked="checked" '; ?>/> <?php _e( "Date/Time", 'contact-form-plugin' ); ?></label> <label class="bws_info"><?php _e( "Example: Date/Time:	August 19, 2013 8:50 pm", 'contact-form-plugin' ); ?></label><br />
											<label><input type="checkbox" id="cntctfrm_display_coming_from" name="cntctfrm_display_coming_from" value="1" <?php if ( '1' == $cntctfrm_options['display_coming_from'] ) echo 'checked="checked" '; ?>/> <?php _e( "Sent from (referer)", 'contact-form-plugin' ); ?></label> <label class="bws_info"><?php _e( "Example: Sent from (referer):	https://bestwebsoft.com/contacts/contact-us/", 'contact-form-plugin' ); ?></label><br />
											<label><input type="checkbox" id="cntctfrm_display_user_agent" name="cntctfrm_display_user_agent" value="1" <?php if ( '1' == $cntctfrm_options['display_user_agent'] ) echo 'checked="checked" '; ?>/> <?php _e( "Using (user agent)", 'contact-form-plugin' ); ?></label> <label class="bws_info"><?php _e( "Example: Using (user agent):	Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36", 'contact-form-plugin' ); ?></label>
										</fieldset>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Language settings for the field names in the form", 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<select name="cntctfrm_languages" id="cntctfrm_languages" style="max-width: 300px;">
											<?php foreach ( $cntctfrm_lang_codes as $key => $val ) {
												if ( ! empty( $cntctfrm_options['language'] ) && in_array( $key, $cntctfrm_options['language'] ) )
													continue;
												echo '<option value="' . esc_attr( $key ) . '"> ' . esc_html( $val ) . '</option>';
											} ?>
										</select>
										<input type="submit" class="button-secondary" name="cntctfrm_add_language_button" id="cntctfrm_add_language_button" value="<?php _e( 'Add a language', 'contact-form-plugin' ); ?>" />
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Change the names of the contact form fields and error messages", 'contact-form-plugin' ); ?></th>
									<td style="width:15px;" class="cntctfrm_td_top_align">
										<input type="checkbox" id="cntctfrm_change_label" name="cntctfrm_change_label" value="1" <?php if ( $cntctfrm_options['change_label'] == '1' ) echo 'checked="checked" '; ?>/>
									</td>
									<td class="cntctfrm_change_label_block" <?php if ( '0' == $cntctfrm_options['change_label'] ) echo 'style="display:none"'; ?>>
										<div class="cntctfrm_label_language_tab <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? 'cntctfrm_active' : ''; ?>" id="cntctfrm_label_default"><?php _e( 'Default', 'contact-form-plugin' ); ?>
											<noscript>
												<input type="submit" class="cntctfrm_change_tab" value="default" name="cntctfrm_change_tab">
											</noscript>
										</div>
										<?php if ( ! empty( $cntctfrm_options['language'] ) ) {
											foreach ( $cntctfrm_options['language'] as $val ) {
												$active_tab_class = isset( $_POST["cntctfrm_change_tab"] ) && $val == $_POST["cntctfrm_change_tab"] ? "cntctfrm_active" : "";
												echo '<div class="cntctfrm_label_language_tab ' . $active_tab_class . '" id="cntctfrm_label_' . $val . '">' . $cntctfrm_lang_codes[ $val ] . ' <span class="cntctfrm_delete" rel="' . $val . '">X</span><noscript><input type="submit" class="cntctfrm_change_tab" value="' . $val . '" name="cntctfrm_change_tab"><span class="cntctfrm_del_button_wrap"><input type="submit" class="cntctfrm_delete_button" value="' . $val . '" name="cntctfrm_delete_button"></span></noscript></div>';
											}
										} ?>
										<div class="clear"></div>
										<div class="cntctfrm_language_tab cntctfrm_tab_default <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? '' : 'hidden' ?>" style="padding: 1px 3px;">
											<div class="cntctfrm_language_tab_block_mini" style="display:none;"><?php _e( "click to expand/hide the list", 'contact-form-plugin' ); ?></div>
											<div class="cntctfrm_language_tab_block">
												<input type="text" maxlength="250" name="cntctfrm_name_label[default]" value="<?php echo $cntctfrm_options['name_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Name", 'contact-form-plugin' ); ?>:</span><br />
												<input type="text" maxlength="250" name="cntctfrm_address_label[default]" value="<?php echo $cntctfrm_options['address_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Address", 'contact-form-plugin' ); ?>:</span><br />
												<input type="text" maxlength="250" name="cntctfrm_email_label[default]" value="<?php echo $cntctfrm_options['email_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Email Address", 'contact-form-plugin' ); ?>:</span><br />
												<input type="text" maxlength="250" name="cntctfrm_phone_label[default]" value="<?php echo $cntctfrm_options['phone_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Phone number", 'contact-form-plugin' ); ?>:</span><br />
												<input type="text" maxlength="250" name="cntctfrm_subject_label[default]" value="<?php echo $cntctfrm_options['subject_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Subject", 'contact-form-plugin' ); ?>:</span><br />
												<input type="text" maxlength="250" name="cntctfrm_message_label[default]" value="<?php echo $cntctfrm_options['message_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Message", 'contact-form-plugin' ); ?>:</span><br />
												<input type="text" maxlength="250" name="cntctfrm_attachment_label[default]" value="<?php echo $cntctfrm_options['attachment_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Attachment", 'contact-form-plugin' ); ?>:</span><br />
												<input type="text" maxlength="250" name="cntctfrm_attachment_tooltip[default]" value="<?php echo $cntctfrm_options['attachment_tooltip']['default']; ?>" /> <span class="bws_info"><?php _e( "Tips below the Attachment block", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_send_copy_label[default]" value="<?php echo $cntctfrm_options['send_copy_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Send me a copy", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_submit_label[default]" value="<?php echo $cntctfrm_options['submit_label']['default']; ?>" /> <span class="bws_info"><?php _e( "Submit", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_name_error[default]" value="<?php echo $cntctfrm_options['name_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the Name field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_address_error[default]" value="<?php echo $cntctfrm_options['address_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the Address field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_email_error[default]" value="<?php echo $cntctfrm_options['email_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the Email field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_phone_error[default]" value="<?php echo $cntctfrm_options['phone_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the Phone field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_subject_error[default]" value="<?php echo $cntctfrm_options['subject_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the Subject field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_message_error[default]" value="<?php echo $cntctfrm_options['message_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the Message field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_attachment_error[default]" value="<?php echo $cntctfrm_options['attachment_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message about the file type for the Attachment field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_attachment_upload_error[default]" value="<?php echo $cntctfrm_options['attachment_upload_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message while uploading a file for the Attachment field to the server", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_attachment_move_error[default]" value="<?php echo $cntctfrm_options['attachment_move_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message while moving the file for the Attachment field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_attachment_size_error[default]" value="<?php echo $cntctfrm_options['attachment_size_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message when file size limit for the Attachment field is exceeded", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_captcha_error[default]" value="<?php echo $cntctfrm_options['captcha_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the Captcha field", 'contact-form-plugin' ); ?></span><br />
												<input type="text" maxlength="250" name="cntctfrm_form_error[default]" value="<?php echo $cntctfrm_options['form_error']['default']; ?>" /> <span class="bws_info"><?php _e( "Error message for the whole form", 'contact-form-plugin' ); ?></span><br />
											</div>
											<?php if ( ! $contact_form_multi_active ) { ?>
												<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
											<?php } else { ?>
												<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form id=<?php echo $_SESSION['cntctfrmmlt_id_form']; ?>]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
											<?php } ?>
										</div>
										<?php if ( ! empty( $cntctfrm_options['language'] ) ) {
											foreach ( $cntctfrm_options['language'] as $val ) {
												if ( ( isset( $_POST['cntctfrm_change_tab'] ) && $val != $_POST['cntctfrm_change_tab'] ) || ! isset($_POST['cntctfrm_change_tab'] ) )
													$labels_table_class = 'hidden';
												else
													$labels_table_class = ''; ?>
												<div class="cntctfrm_language_tab <?php echo $labels_table_class; ?> cntctfrm_tab_<?php echo $val; ?>">
													<div class="cntctfrm_language_tab_block_mini" style="display:none;"><?php _e( "click to expand/hide the list", 'contact-form-plugin' ); ?></div>
													<div class="cntctfrm_language_tab_block">
														<input type="text" maxlength="250" name="cntctfrm_name_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['name_label'][ $val ] ) ) echo $cntctfrm_options['name_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Name", 'contact-form-plugin' ); ?>:</span><br />
														<input type="text" maxlength="250" name="cntctfrm_address_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['address_label'][ $val ] ) ) echo $cntctfrm_options['address_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Address", 'contact-form-plugin' ); ?>:</span><br />
														<input type="text" maxlength="250" name="cntctfrm_email_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['email_label'][ $val ] ) ) echo $cntctfrm_options['email_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Email Address", 'contact-form-plugin' ); ?>:</span><br />
														<input type="text" maxlength="250" name="cntctfrm_phone_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['phone_label'][ $val ] ) ) echo $cntctfrm_options['phone_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Phone number", 'contact-form-plugin' ); ?>:</span><br />
														<input type="text" maxlength="250" name="cntctfrm_subject_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['subject_label'][ $val ] ) ) echo $cntctfrm_options['subject_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Subject", 'contact-form-plugin' ); ?>:</span><br />
														<input type="text" maxlength="250" name="cntctfrm_message_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['message_label'][ $val ] ) ) echo $cntctfrm_options['message_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Message", 'contact-form-plugin' ); ?>:</span><br />
														<input type="text" maxlength="250" name="cntctfrm_attachment_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['attachment_label'][ $val ] ) ) echo $cntctfrm_options['attachment_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Attachment", 'contact-form-plugin' ); ?>:</span><br />
														<input type="text" maxlength="250" name="cntctfrm_attachment_tooltip[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['attachment_tooltip'][ $val ] ) ) echo $cntctfrm_options['attachment_tooltip'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Tips below the Attachment block", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_send_copy_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['send_copy_label'][ $val ] ) ) echo $cntctfrm_options['send_copy_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Send me a copy", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_submit_label[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['submit_label'][ $val ] ) ) echo $cntctfrm_options['submit_label'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Submit", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_name_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['name_error'][ $val ] ) ) echo $cntctfrm_options['name_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the Name field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_address_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['address_error'][ $val ] ) ) echo $cntctfrm_options['address_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the Address field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_email_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['email_error'][ $val ] ) ) echo $cntctfrm_options['email_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the Email field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_phone_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['phone_error'][ $val ] ) ) echo $cntctfrm_options['phone_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the Phone field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_subject_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['subject_error'][ $val ] ) ) echo $cntctfrm_options['subject_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the Subject field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_message_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['message_error'][ $val ] ) ) echo $cntctfrm_options['message_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the Message field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_attachment_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['attachment_error'][ $val ] ) ) echo $cntctfrm_options['attachment_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message about the file type for the Attachment field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_attachment_upload_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['attachment_upload_error'][ $val ] ) ) echo $cntctfrm_options['attachment_upload_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message while uploading a file for the Attachment field to the server", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_attachment_move_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['attachment_move_error'][ $val ] ) ) echo $cntctfrm_options['attachment_move_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message while moving the file for the Attachment field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_attachment_size_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['attachment_size_error'][ $val ] ) ) echo $cntctfrm_options['attachment_size_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message when file size limit for the Attachment field is exceeded", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_captcha_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['captcha_error'][ $val ] ) ) echo $cntctfrm_options['captcha_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the Captcha field", 'contact-form-plugin' ); ?></span><br />
														<input type="text" maxlength="250" name="cntctfrm_form_error[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['form_error'][ $val ] ) ) echo $cntctfrm_options['form_error'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Error message for the whole form", 'contact-form-plugin' ); ?></span><br />
													</div>
													<?php if ( ! $contact_form_multi_active ) { ?>
														<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val; ?>]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
													<?php } else { ?>
														<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val . ' id=' . $_SESSION['cntctfrmmlt_id_form']; ?>]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
													<?php } ?>
												</div>
											<?php }
										} ?>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( 'Use the changed names of the contact form fields in the email', 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<input type="checkbox" name="cntctfrm_change_label_in_email" value="1" <?php if ( $cntctfrm_options['change_label_in_email'] == '1' ) echo 'checked="checked" '; ?>/>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php _e( "Action after email is sent", 'contact-form-plugin' ); ?></th>
									<td colspan="2" class="cntctfrm_action_after_send_block">
										<label><input type="radio" id="cntctfrm_action_after_send" name="cntctfrm_action_after_send" value="1" <?php if ( '1' == $cntctfrm_options['action_after_send'] ) echo 'checked="checked" '; ?>/> <?php _e( "Display text", 'contact-form-plugin' ); ?></label><br />
										<div class="cntctfrm_label_language_tab <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? 'cntctfrm_active' : ''; ?>" id="cntctfrm_text_default"><?php _e( 'Default', 'contact-form-plugin' ); ?>
											<noscript>
												<input type="submit" class="cntctfrm_change_tab" value="default" name="cntctfrm_change_tab">
											</noscript>
										</div>
										<?php if ( ! empty( $cntctfrm_options['language'] ) ) {
											foreach ( $cntctfrm_options['language'] as $val ) {
												$active_tab_class = isset( $_POST["cntctfrm_change_tab"] ) && $val == $_POST["cntctfrm_change_tab"] ? "cntctfrm_active" : "";
												echo '<div class="cntctfrm_label_language_tab ' . $active_tab_class . '" id="cntctfrm_text_' . $val . '">' . $cntctfrm_lang_codes[ $val ] . ' <span class="cntctfrm_delete" rel="' . $val . '">X</span><noscript><input type="submit" class="cntctfrm_change_tab" value="' . $val . '" name="cntctfrm_change_tab"><span class="cntctfrm_del_button_wrap"><input type="submit" class="cntctfrm_delete_button" value="' . $val . '" name="cntctfrm_delete_button"></span></noscript></div>';
											}
										} ?>
										<div class="clear"></div>
										<div class="cntctfrm_language_tab cntctfrm_tab_default <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? '' : 'hidden' ?>" style="padding: 5px 10px 5px 5px;">
											<label><input type="text" maxlength="250" name="cntctfrm_thank_text[default]" value="<?php echo $cntctfrm_options['thank_text']['default']; ?>" /> <span class="bws_info"><?php _e( "Text", 'contact-form-plugin' ); ?></span></label><br />
											<?php if ( ! $contact_form_multi_active ) { ?>
												<span class="bws_info cntctfrm_shortcode_for_language"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
											<?php } else { ?>
												<span class="bws_info cntctfrm_shortcode_for_language"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form id=<?php echo $_SESSION['cntctfrmmlt_id_form']; ?>]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
											<?php } ?>
										</div>
										<?php if ( ! empty( $cntctfrm_options['language'] ) ) {
											foreach ( $cntctfrm_options['language'] as $val ) {
											if ( ( isset( $_POST['cntctfrm_change_tab'] ) && $val != $_POST['cntctfrm_change_tab'] ) || ! isset($_POST['cntctfrm_change_tab'] ) )
													$labels_table_class = 'hidden';
												else
													$labels_table_class = ''; ?>
												<div class="cntctfrm_language_tab <?php echo $labels_table_class; ?> cntctfrm_tab_<?php echo $val; ?>" style="padding: 5px 10px 5px 5px;">
													<label><input type="text" maxlength="250" name="cntctfrm_thank_text[<?php echo $val; ?>]" value="<?php if ( isset( $cntctfrm_options['thank_text'][ $val ] ) ) echo $cntctfrm_options['thank_text'][ $val ]; ?>" /> <span class="bws_info"><?php _e( "Text", 'contact-form-plugin' ); ?></span></label><br />
													<?php if ( ! $contact_form_multi_active ) { ?>
														<span class="bws_info cntctfrm_shortcode_for_language"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val; ?>]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
													<?php } else { ?>
														<span class="bws_info cntctfrm_shortcode_for_language"><?php _e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val . ' id=' . $_SESSION['cntctfrmmlt_id_form']; ?>]</span> <?php _e( "for this language", 'contact-form-plugin' ); ?></span>
													<?php } ?>
												</div>
											<?php }
										} ?>
										<div id="cntctfrm_before"></div>
										<br />
										<label><input type="radio" id="cntctfrm_action_after_send_url" name="cntctfrm_action_after_send" value="0" <?php if ( '0' == $cntctfrm_options['action_after_send'] ) echo 'checked="checked" '; ?> /> <?php _e( "Redirect to the page", 'contact-form-plugin' ); ?></label><br />
										<label><input type="text" maxlength="250" name="cntctfrm_redirect_url" value="<?php echo $cntctfrm_options['redirect_url']; ?>" /> <span class="bws_info"><?php _e( "Url", 'contact-form-plugin' ); ?></span></label>
									</td>
								</tr>
							</table>
							<?php if ( ! $bws_hide_premium_options_check ) { ?>
								<div class="bws_pro_version_bloc">
									<div class="bws_pro_version_table_bloc">
										<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'contact-form-plugin' ); ?>"></button>
										<div class="bws_table_bg"></div>
										<table class="form-table bws_pro_version">
											<tr valign="top">
												<th scope="row"><?php _e( "Add field 'Reply-To' to the email header", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<input disabled="disabled" type="checkbox" id="cntctfrm_header_reply_to" name="cntctfrm_header_reply_to" value="1"> <span class="bws_info">(<?php _e( "Field 'Reply-To' will be initialized by user email", 'contact-form-plugin' ); ?>)</span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><?php _e( 'Auto Response', 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<input disabled="disabled" type="checkbox" value="1" name="cntctfrm_auto_response" checked="checked"/>
													<textarea class="cntctfrm_auto_response_message" name="cntctfrm_auto_response_message">Dear %%NAME%%, Thank you for contacting us. We have received your message and will reply to it shortly. Regards, %%SITENAME%% Team.</textarea><br/>
													<span class="bws_info" style="margin-left: 45px"><?php _e( "You can use %%NAME%% to display data from the email field and %%MESSAGE%% to display data from the Message field, as well as %%SITENAME%% to display blog name.", 'contact-form-plugin' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" colspan="2">
													* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'contact-form-plugin' ); ?>
												</th>
											</tr>
										</table>
									</div>
									<div class="bws_pro_version_tooltip">
										<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=697c5e74f39779ce77850e11dbe21962&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="Contact Form Pro">
											<?php _e( 'Upgrade to Pro', 'contact-form-plugin' ); ?>
										</a>
										<div class="clear"></div>
									</div>
								</div>
							<?php } ?>
						</div>
						<!-- end of 'Additional' settings -->
						<input type="hidden" name="cntctfrm_form_submit" value="submit" />
						<p class="submit">
							<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'contact-form-plugin' ); ?>" />
						</p>
						<?php wp_nonce_field( $plugin_basename, 'cntctfrm_nonce_name' ); ?>
					</form>
					<?php bws_form_restore_default_settings( $plugin_basename );
				}
			} elseif ( 'appearance' == $_GET['action'] ) {
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else { ?>
					<noscript>
						<div class="error">
							<p>
								<strong><?php printf( __( "Please enable JavaScript to change '%s', '%s' options and for fields sorting.", 'contact-form-plugin' ), __( "Form layout", 'contact-form-plugin' ), __( "Submit position", 'contact-form-plugin' ) ); ?></strong>
							</p>
						</div>
					</noscript>
					<form id="cntctfrm_settings_form" class="bws_form" method="post" action="">
						<div id="cntctfrm_appearance_wrap" class="cntctfrm_appearance_<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
							<div id="<?php echo is_rtl() ? 'cntctfrm_right_table' : 'cntctfrm_left_table'; ?>">
								<table class="form-table" style="width:auto;">
									<tr valign="top">
										<th scope="row"><?php _e( "Form layout", 'contact-form-plugin' ); ?></th>
										<td colspan="2">
											<fieldset>
												<input id="cntctfrm_layout_one_column" name="cntctfrm_layout" type="radio" value="1" <?php if ( $cntctfrm_options['layout'] === 1 ) echo 'checked="checked"' ?>>
												<label for="cntctfrm_layout_one_column"><?php _e( 'One column', 'contact-form-plugin' ); ?></label>
												<br/>
												<input id="cntctfrm_layout_two_columns" name="cntctfrm_layout" type="radio" value="2" <?php if ( $cntctfrm_options['layout'] === 2 ) echo 'checked="checked"' ?>>
												<label for="cntctfrm_layout_two_columns"><?php _e( 'Two columns', 'contact-form-plugin' ); ?></label>
											</fieldset>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><?php _e( "Submit position", 'contact-form-plugin' ); ?></th>
										<td colspan="2">
											<fieldset>
												<input id="cntctfrm_submit_position_left" name="cntctfrm_submit_position" type="radio" value="left" <?php if ( $cntctfrm_options['submit_position'] == 'left' ) echo 'checked="checked"' ?>>
												<label for="cntctfrm_submit_position_left"><?php _e( 'Left', 'contact-form-plugin' ); ?></label>
												<br/>
												<input id="cntctfrm_submit_position_right" name="cntctfrm_submit_position" type="radio" value="right" <?php if ( $cntctfrm_options['submit_position'] == 'right' ) echo 'checked="checked"' ?>>
												<label for="cntctfrm_submit_position_right"><?php _e( 'Right', 'contact-form-plugin' ); ?></label>
											</fieldset>
										</td>
									</tr>
								</table>
								<?php if ( ! $bws_hide_premium_options_check ) { ?>
									<div class="bws_pro_version_bloc">
										<div class="bws_pro_version_table_bloc">
											<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'contact-form-plugin' ); ?>"></button>
											<div class="bws_table_bg"></div>
											<table class="form-table bws_pro_version">
												<tr valign="top">
													<th scope="row"><?php _e( "Form align", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<fieldset>
															<input disabled="disabled" name="cntctfrm_form_align" type="radio" value="left" checked="checked">
															<label for="cntctfrm_form_align_left"><?php _e( 'Left', 'contact-form-plugin' ); ?></label>
															<br/>
															<input disabled="disabled" name="cntctfrm_form_align" type="radio" value="center">
															<label for="cntctfrm_form_align_center"><?php _e( 'Center', 'contact-form-plugin' ); ?></label>
															<br/>
															<input disabled="disabled" name="cntctfrm_form_align" type="radio" value="right">
															<label for="cntctfrm_form_align_right"><?php _e( 'Right', 'contact-form-plugin' ); ?></label>
														</fieldset>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e( "Labels position", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<fieldset>
															<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="top" checked="checked">
															<label for="cntctfrm_labels_position_top"><?php _e( 'Top', 'contact-form-plugin' ); ?></label>
															<br/>
															<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="left">
															<label for="cntctfrm_labels_position_left"><?php _e( 'Left', 'contact-form-plugin' ); ?></label>
															<br/>
															<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="right">
															<label for="cntctfrm_labels_position_right"><?php _e( 'Right', 'contact-form-plugin' ); ?></label>
															<br/>
															<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="bottom">
															<label for="cntctfrm_labels_position_bottom"><?php _e( 'Bottom', 'contact-form-plugin' ); ?></label>
														</fieldset>
													</td>
												</tr>
												<tr valign="top" id="cntctfrm_labels_align" class="cntctfrm_labels_align_show">
													<th scope="row"><?php _e( "Labels align", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<fieldset>
															<input disabled="disabled" name="cntctfrm_labels_align" type="radio" value="left" checked="checked">
															<label for="cntctfrm_labels_align_left"><?php _e( 'Left', 'contact-form-plugin' ); ?></label>
															<br/>
															<input disabled="disabled" name="cntctfrm_labels_align" type="radio" value="center">
															<label for="cntctfrm_labels_align_center"><?php _e( 'Center', 'contact-form-plugin' ); ?></label>
															<br/>
															<input disabled="disabled" name="cntctfrm_labels_align" type="radio" value="right">
															<label for="cntctfrm_labels_align_right"><?php _e( 'Right', 'contact-form-plugin' ); ?></label>
														</fieldset>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e( "Errors output", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<select name="cntctfrm_error_displaying" disabled='disabled' style="max-width:400px; width:100%;">
															<option value="labels"><?php _e( "Display error messages", 'contact-form-plugin' ); ?></option>
															<option value="input_colors"><?php _e( "Color of the input field errors.", 'contact-form-plugin' ); ?></option>
															<option value="both" selected="selected"><?php _e( "Display error messages & color of the input field errors", 'contact-form-plugin' ); ?></option>
														</select>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e( "Add placeholder to the input blocks", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<input disabled='disabled' type="checkbox" name="cntctfrm_placeholder" value="1" />
													</td>
												</tr>
												<tr valign="top">
													<th scope="row"><?php _e( "Add tooltips", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_name" value="1" />
															<label for="cntctfrm_tooltip_display_name"><?php _e( "Name", 'contact-form-plugin' ); ?></label>
														</div>
														<?php if ( '1' == $cntctfrm_options['display_address_field'] ) { ?>
															<div>
																<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_address" value="1" />
																<label for="cntctfrm_tooltip_display_address"><?php _e( "Address", 'contact-form-plugin' ); ?></label>
															</div>
														<?php } ?>
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_email" value="1" />
															<label for="cntctfrm_tooltip_display_email"><?php _e( "Email address", 'contact-form-plugin' ); ?></label>
														</div>
														<?php if ( '1' == $cntctfrm_options['display_phone_field'] ) { ?>
															<div>
																<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_phone" value="1" />
																<label for="cntctfrm_tooltip_display_phone"><?php _e( "Phone Number", 'contact-form-plugin' ); ?></label>
															</div>
														<?php } ?>
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_subject" value="1" />
															<label for="cntctfrm_tooltip_display_subject"><?php _e( "Subject", 'contact-form-plugin' ); ?></label>
														</div>
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_message" value="1" />
															<label for="cntctfrm_tooltip_display_message"><?php _e( "Message", 'contact-form-plugin' ); ?></label>
														</div>
														<?php if ( '1' == $cntctfrm_options['attachment_explanations'] ) { ?>
															<div>
																<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_attachment" value="1" />
																<label for="cntctfrm_tooltip_display_attachment"><?php _e( "Attachment", 'contact-form-plugin' ); ?></label>
															</div>
														<?php } ?>
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_captcha" value="1" />
															<label for="cntctfrm_tooltip_display_captcha">Captcha by BestWebSoft</label>
														</div>
													</td>
												</tr>
												<tr valign="top">
													<th colspan="3" scope="row">
														<input disabled='disabled' type="checkbox" name="cntctfrm_style_options" value="1" checked="checked" />
														<?php _e( "Style options", 'contact-form-plugin' ); ?>
													</th>
												</tr>
												<tr valign="top" class="cntctfrm_style_block">
													<th scope="row"><?php _e( "Text color", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_label_color" value="" />
															<div class="cntctfrm_label_block"><?php _e( 'Label text color', 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_input_placeholder_color" value="" />
															<div class="cntctfrm_label_block"><?php _e( "Placeholder color", 'contact-form-plugin' ); ?></div>
														</div>
													</td>
												</tr>
												<tr valign="top" class="cntctfrm_style_block">
													<th scope="row"><?php _e( "Errors color", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_error_color" value="" />
															<div class="cntctfrm_label_block"><?php _e( 'Error text color', 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_error_input_color" value="" />
															<div class="cntctfrm_label_block"><?php _e( 'Background color of the input field errors', 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_error_input_border_color" value="" />
															<div class="cntctfrm_label_block"><?php _e( 'Border color of the input field errors', 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" id="" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_input_placeholder_error_color" value=""/>
															<div class="cntctfrm_label_block"><?php _e( "Placeholder color of the input field errors", 'contact-form-plugin' ); ?></div>
														</div>
													</td>
												</tr>
												<tr valign="top" class="cntctfrm_style_block">
													<th scope="row"><?php _e( "Input fields", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_input_background" value="" />
															<div class="cntctfrm_label_block"><?php _e( "Input fields background color", 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_input_color" value="" />
															<div class="cntctfrm_label_block"><?php _e( "Text fields color", 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' size="8" type="text" value="" name="cntctfrm_border_input_width" />
															<div class="cntctfrm_label_block"><?php _e( 'Border width in px, numbers only', 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_border_input_color" value="" />
															 <div class="cntctfrm_label_block"><?php _e( 'Border color', 'contact-form-plugin' ); ?></div>
														</div>
													</td>
												</tr>
												<tr valign="top" class="cntctfrm_style_block">
													<th scope="row"><?php _e( "Submit button", 'contact-form-plugin' ); ?></th>
													<td colspan="2">
														<div>
															<input disabled='disabled' size="8" type="text" value="" name="cntctfrm_button_width" />
															<div class="cntctfrm_label_block"><?php _e( 'Width in px, numbers only', 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_button_backgroud" value="" />
															 <div class="cntctfrm_label_block"><?php _e( 'Button color', 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_button_color" value="" />
															<div class="cntctfrm_label_block"><?php _e( "Button text color", 'contact-form-plugin' ); ?></div>
														</div>
														<div>
															<input disabled='disabled' type="button" value="<?php _e( 'Default', 'contact-form-plugin' ); ?>" />
															<input disabled='disabled' type="text" size="8" name="cntctfrm_border_button_color" value="" />
															 <div class="cntctfrm_label_block"><?php _e( 'Border color', 'contact-form-plugin' ); ?></div>
														</div>
													</td>
												</tr>
												<tr valign="top">
													<th scope="row" colspan="2">
														* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'contact-form-plugin' ); ?>
													</th>
												</tr>
											</table>
										</div>
										<div class="bws_pro_version_tooltip">
											<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=697c5e74f39779ce77850e11dbe21962&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" target="_blank" title="Contact Form Pro">
												<?php _e( 'Upgrade to Pro', 'contact-form-plugin' ); ?>
											</a>
											<div class="clear"></div>
										</div>
									</div>
								<?php } ?>
							</div>
							<?php if ( $bws_hide_premium_options_check ) { ?>
								<div class="clear"></div>
							<?php } ?>
							<div id="<?php echo is_rtl() ? 'cntctfrm_left_table' : 'cntctfrm_right_table' ?>">
								<h3><?php _e( 'Contact Form | Preview', 'contact-form-plugin' ); ?></h3>
								<span class="bws_info"><?php _e( 'Drag the necessary field to sort fields.', 'contact-form-plugin' ); ?></span>
								<?php
									$classes = ( $cntctfrm_options['layout'] === 1 ) ? ' cntctfrm_one_column' : ' cntctfrm_two_columns';
									$classes .= is_rtl() ? ' cntctfrm_rtl' : ' cntctfrm_ltr';
								?>
								<div id="cntctfrm_contact_form" class="cntctfrm_contact_form<?php echo $classes; ?>">
									<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['form_error']['default']; ?></div>
									<div id="cntctfrm_wrap">
										<?php $cntctfrm_ordered_fields = cntctfrm_get_ordered_fields();
										for ( $i = 1; $i <= 2; $i++ ) {
											$column = ( $i == 1 ) ? 'first_column' : 'second_column'; ?>
											<ul id="cntctfrm_<?php echo $column; ?>" class="cntctfrm_column" <?php if ( $i == 2 && $cntctfrm_options['layout'] === 1 ) echo 'style="display: none;"'; ?>>
												<?php foreach ( $cntctfrm_ordered_fields[ $column ] as $cntctfrm_field ) {
													switch( $cntctfrm_field ) {
														case 'cntctfrm_contact_name':
															if ( 1 == $cntctfrm_options['display_name_field'] ) { ?>
																<li class="cntctfrm_field_wrap">
																	<div class="cntctfrm_label cntctfrm_label_name">
																		<label for="cntctfrm_contact_name"><?php echo $cntctfrm_options['name_label']['default']; if ( 1 == $cntctfrm_options['required_name_field'] ) echo '<span class="required"> ' . $cntctfrm_options['required_symbol'] . '</span>'; ?></label>
																	</div>
																	<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['name_error']['default']; ?></div>
																	<div class="cntctfrm_input cntctfrm_input_name">
																		<div class="cntctfrm_drag_wrap"></div>
																		<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_name" id="cntctfrm_contact_name" />
																	</div>
																</li>
															<?php }
															break;
														case 'cntctfrm_contact_address':
															if ( 1 == $cntctfrm_options['display_address_field'] ) { ?>
																<li class="cntctfrm_field_wrap">
																	<div class="cntctfrm_label cntctfrm_label_address">
																		<label for="cntctfrm_contact_address"><?php echo $cntctfrm_options['address_label']['default']; if ( 1 == $cntctfrm_options['required_address_field'] ) echo '<span class="required"> ' . $cntctfrm_options['required_symbol'] . '</span>'; ?></label>
																	</div>
																	<?php if ( 1 == $cntctfrm_options['required_address_field'] ) { ?>
																		<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['address_error']['default']; ?></div>
																	<?php } ?>
																	<div class="cntctfrm_input cntctfrm_input_address">
																		<div class="cntctfrm_drag_wrap"></div>
																		<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_address" id="cntctfrm_contact_address" />
																	</div>
																</li>
															<?php }
															break;
														case 'cntctfrm_contact_email': ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_label cntctfrm_label_email">
																	<label for="cntctfrm_contact_email"><?php echo $cntctfrm_options['email_label']['default']; if ( 1 == $cntctfrm_options['required_email_field'] ) echo '<span class="required"> ' . $cntctfrm_options['required_symbol'] . '</span>'; ?></label>
																</div>
																<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['email_error']['default']; ?></div>
																<div class="cntctfrm_input cntctfrm_input_email">
																	<div class="cntctfrm_drag_wrap"></div>
																	<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_email" id="cntctfrm_contact_email" />
																</div>
															</li>
															<?php break;
														case 'cntctfrm_contact_phone':
															if ( 1 == $cntctfrm_options['display_phone_field'] ) { ?>
																<li class="cntctfrm_field_wrap">
																	<div class="cntctfrm_label cntctfrm_label_phone">
																		<label for="cntctfrm_contact_phone"><?php echo $cntctfrm_options['phone_label']['default']; if ( 1 == $cntctfrm_options['required_phone_field'] ) echo '<span class="required"> ' . $cntctfrm_options['required_symbol'] . '</span>'; ?></label>
																	</div>
																	<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['phone_error']['default']; ?></div>
																	<div class="cntctfrm_input cntctfrm_input_phone">
																		<div class="cntctfrm_drag_wrap"></div>
																		<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_phone" id="cntctfrm_contact_phone" />
																	</div>
																</li>
															<?php }
															break;
														case 'cntctfrm_contact_subject': ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_label cntctfrm_label_subject">
																	<label for="cntctfrm_contact_subject"><?php echo $cntctfrm_options['subject_label']['default']; if ( 1 == $cntctfrm_options['required_subject_field'] ) echo '<span class="required"> ' . $cntctfrm_options['required_symbol'] . '</span>'; ?></label>
																</div>
																<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['subject_error']['default']; ?></div>
																<div class="cntctfrm_input cntctfrm_input_subject">
																	<div class="cntctfrm_drag_wrap"></div>
																	<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_subject" id="cntctfrm_contact_subject" />
																</div>
															</li>
															<?php break;
														case 'cntctfrm_contact_message': ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_label cntctfrm_label_message">
																	<label for="cntctfrm_contact_message"><?php echo $cntctfrm_options['message_label']['default']; if ( 1 == $cntctfrm_options['required_message_field'] ) echo '<span class="required"> ' . $cntctfrm_options['required_symbol'] . '</span>'; ?></label>
																</div>
																<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['message_error']['default']; ?></div>
																<div class="cntctfrm_input cntctfrm_input_message">
																	<div class="cntctfrm_drag_wrap"></div>
																	<textarea class="bws_no_bind_notice" rows="5" cols="30" name="cntctfrm_contact_message" id="cntctfrm_contact_message"></textarea>
																</div>
															</li>
															<?php break;
														case 'cntctfrm_contact_attachment':
															if ( 1 == $cntctfrm_options['attachment'] ) { ?>
																<li class="cntctfrm_field_wrap">
																	<div class="cntctfrm_label cntctfrm_label_attachment">
																		<label for="cntctfrm_contact_attachment"><?php echo $cntctfrm_options['attachment_label']['default']; ?></label>
																	</div>
																	<div class="cntctfrm_error_text hidden"><?php echo $cntctfrm_options['attachment_error']['default']; ?></div>
																	<div class="cntctfrm_input cntctfrm_input_attachment">
																		<div class="cntctfrm_drag_wrap"></div>
																		<input class="bws_no_bind_notice" type="file" name="cntctfrm_contact_attachment" id="cntctfrm_contact_attachment" />
																		<?php if ( 1 == $cntctfrm_options['attachment_explanations'] ) { ?>
																				<label class="cntctfrm_contact_attachment_extensions"><?php echo  $cntctfrm_options['attachment_tooltip'][ 'default' ]; ?></label>
																		<?php } ?>
																	</div>
																</li>
															<?php }
															break;
														case 'cntctfrm_contact_send_copy':
															if ( 1 == $cntctfrm_options['send_copy'] ) { ?>
																<li class="cntctfrm_field_wrap">
																	<div class="cntctfrm_checkbox cntctfrm_checkbox_send_copy">
																		<div class="cntctfrm_drag_wrap"></div>
																		<input type="checkbox" value="1" name="cntctfrm_contact_send_copy" id="cntctfrm_contact_send_copy" class="bws_no_bind_notice" style="margin: 0;" />
																		<label for="cntctfrm_contact_send_copy"><?php echo $cntctfrm_options['send_copy_label']['default']; ?></label>
																	</div>
																</li>
															<?php }
															break;
														case 'cntctfrm_subscribe':
															if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
																if ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] ) ) || ! empty( $cntctfrm_options['display_subscribe'] ) ) {  ?>
																	<li class="cntctfrm_field_wrap">
																		<div class="cntctfrm_checkbox cntctfrm_checkbox_subscribe">
																			<div class="cntctfrm_drag_wrap"></div>
																			<input type="hidden" value="1" name="cntctfrm_subscribe"/>
																			<?php $cntctfrm_sbscrbr_checkbox = apply_filters( 'sbscrbr_cntctfrm_checkbox_add', array() );
																				if ( isset( $cntctfrm_sbscrbr_checkbox['content'] ) ) {
																					echo $cntctfrm_sbscrbr_checkbox['content'];
																				} ?>
																		</div>
																	</li>
																<?php }
															}
															break;
														case 'cntctfrm_captcha':
															if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) ||
																array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) ) {

																$display_captcha_label = '';

																if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) &&
																	( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['enabled'] ) ) || ! empty( $cntctfrm_options['display_captcha'] ) ) ) {

																	$display_captcha = true;

																	$captcha_label = isset( $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['label'] ] ) ? $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['label'] ] : '';
																	if ( ! empty( $captcha_label ) ) {
																		$captcha_required_symbol = sprintf( ' <span class="required">%s</span>', ( isset( $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['required_symbol'] ] ) ) ? $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['required_symbol'] ] : '' );
																		$display_captcha_label = $captcha_label . $captcha_required_symbol;
																	}
																}

																if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) &&
																	( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_google_captcha'] ) ) ) )
																	$display_google_captcha = true;

																if ( isset( $display_google_captcha ) || isset( $display_captcha ) ) { ?>
																	<li class="cntctfrm_field_wrap">
																		<div class="cntctfrm_label cntctfrm_label_captcha">
																			<label><?php echo $display_captcha_label; ?></label>
																		</div>
																		<div class="cntctfrm_input cntctfrm_input_captcha">
																			<input type="hidden" value="1" name="cntctfrm_captcha"/>
																			<?php if ( isset( $display_captcha ) ) { ?>
																				<img src="<?php echo plugins_url( 'images/cptch.png', __FILE__ ); ?>">
																			<?php }
																			if ( isset( $display_google_captcha ) ) { ?>
																				<img src="<?php echo plugins_url( 'images/google-captcha.png', __FILE__ ); ?>">
																			<?php } ?>
																		</div>
																	</li>
																<?php }
															}
															break;
														default:
															break;
													}
												} ?>
											</ul>
										<?php } ?>
										<div class="clear"></div>
									</div>
									<div class="cntctfrm_submit_wrap">
										<?php $cntctfrm_direction = is_rtl() ? 'rtl' : 'ltr';
										$submit_position_value = array(
											'ltr' => array(
												'left'  => 1,
												'right' => 2
											),
											'rtl' => array(
												'left'  => 2,
												'right' => 1
											)
										);
										for ( $i = 1; $i <= 2; $i++ ) {
											$column = ( $i == 1 ) ? 'first_column' : 'second_column'; ?>
											<div id="cntctfrm_submit_<?php echo $column; ?>" class="cntctfrm_column">
												<?php if ( $i == $submit_position_value[ $cntctfrm_direction ][ $cntctfrm_options['submit_position'] ] ) { ?>
													<div class="cntctfrm_input cntctfrm_input_submit" style="<?php printf( 'text-align: %s !important;', $cntctfrm_options['submit_position'] ); ?>">
														<input type="button" value="<?php echo $cntctfrm_options['submit_label']['default']; ?>" class="bws_no_bind_notice" style="cursor: pointer; margin: 0; text-align: center;" />
													</div>
												<?php } ?>
											</div>
										<?php } ?>
										<div class="clear"></div>
									</div>
								</div>
								<div id="cntctfrm_shortcode" class="cntctfrm_one_column">
									<?php _e( "If you would like to add the Contact Form to your website, just copy and paste this shortcode to your post or page or widget", 'contact-form-plugin' ); ?>:<br/>
									<div>
										<div id="cntctfrm_shortcode_code">
											<span class="cntctfrm_shortcode">[bestwebsoft_contact_form<?php if ( $contact_form_multi_active ) printf( ' id=%s', $_SESSION['cntctfrmmlt_id_form'] ); ?>]</span>
										</div>
									</div>
								</div>
							</div>
							<div class="clear"></div>
							<input type="hidden" name="cntctfrm_form_appearance_submit" value="submit" />
							<input type="hidden" id="cntctfrm_layout_first_column" name="cntctfrm_layout_first_column" value="<?php echo implode( ',', $cntctfrm_options['order_fields']['first_column'] ); ?>" />
							<input type="hidden" id="cntctfrm_layout_second_column" name="cntctfrm_layout_second_column" value="<?php echo implode( ',', $cntctfrm_options['order_fields']['second_column'] ); ?>" />
							<p class="submit">
								<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'contact-form-plugin' ); ?>" />
							</p>
							<?php wp_nonce_field( $plugin_basename, 'cntctfrm_nonce_name' ); ?>
						</div>
					</form>
					<?php bws_form_restore_default_settings( $plugin_basename );
				}
			} elseif ( 'custom_code' == $_GET['action'] ) {
				bws_custom_code_tab();
			} elseif ( 'go_pro' == $_GET['action'] ) {
				bws_go_pro_tab_show( $bws_hide_premium_options_check, $cntctfrm_plugin_info, $plugin_basename, 'contact_form.php', 'contact_form_pro.php', 'contact-form-pro/contact_form_pro.php', 'contact-form-plugin', '697c5e74f39779ce77850e11dbe21962', '77', isset( $go_pro_result['pro_plugin_is_activated'] ) );
			}
			bws_plugin_reviews_block( $cntctfrm_plugin_info['Name'], 'contact-form-plugin' ); ?>
		</div>
	<?php }
}

/* Display contact form in front end - page or post */
if ( ! function_exists( 'cntctfrm_display_form' ) ) {
	function cntctfrm_display_form( $atts = array( 'lang' => 'default' ) ) {
		global $cntctfrm_error_message, $cntctfrm_options, $cntctfrm_result, $cntctfrmmlt_ide, $cntctfrmmlt_active_plugin, $cntctfrm_form_count, $cntctfrm_related_plugins;

		if ( empty( $cntctfrm_related_plugins ) )
			cntctfrm_related_plugins();

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( ! wp_script_is( 'cntctfrm_frontend_script', 'registered' ) )
			wp_register_script( 'cntctfrm_frontend_script', plugins_url( 'js/cntctfrm.js', __FILE__ ), array( 'jquery' ), false, true  );

		$cntctfrm_form_count = empty( $cntctfrm_form_count ) ? 1 : ++$cntctfrm_form_count;
		$form_countid = ( $cntctfrm_form_count == 1 ? '' : '_' . $cntctfrm_form_count );
		$content = "";
		/* Get options for the form with a definite identifier */
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( $contact_form_multi_active ) {
			extract( shortcode_atts( array( 'id' => $cntctfrmmlt_ide, 'lang' => 'default' ), $atts ) );
			if ( isset( $atts['id'] ) ) {
				cntctfrm_settings( $atts['id'] );
				$cntctfrm_options = get_option( 'cntctfrmmlt_options_' . $atts['id'] );
				$options_name = 'cntctfrmmlt_options_' . $atts['id'];
				/* if no options with the specified id */
				if ( ! $cntctfrm_options ) {
					$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
					$options_name = 'cntctfrmmlt_options';
				} elseif ( empty( $options_name ) ) {
					$options_name = 'cntctfrmmlt_options';
				}
			} else {
				cntctfrm_settings();
				if ( 'pro' == $contact_form_multi_active && $multi_options_main = get_option( 'cntctfrmmltpr_options_main' ) ) {
					/**/
				} else {
					$multi_options_main = get_option( 'cntctfrmmlt_options_main' );
				}

				if ( ! empty( $multi_options_main ) ) {
					reset( $multi_options_main['name_id_form'] );
					$id = key( $multi_options_main['name_id_form'] );
					$cntctfrm_options = get_option( 'cntctfrmmlt_options_' . $id );
					$options_name = 'cntctfrmmlt_options_' . $id;
					if ( empty( $cntctfrm_options ) ) {
						$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
						$options_name = 'cntctfrmmlt_options';
					}
				} else {
					$options_name = 'cntctfrmmlt_options';
				}
			}
		} else {
			cntctfrm_settings();
			$cntctfrm_options = get_option( 'cntctfrm_options' );
			extract( shortcode_atts( array( 'lang' => 'default' ), $atts ) );
			$options_name = 'cntctfrm_options';
		}
		/* check lang and replace with en default if need */
		foreach ( $cntctfrm_options as $key => $value ) {
			if ( is_array( $value ) && array_key_exists( 'default', $value ) && ( ! array_key_exists( $lang, $value ) || ( isset( $cntctfrm_options[ $key ][ $lang ] ) && $cntctfrm_options[ $key ][ $lang ] == '' ) ) ) {
				$cntctfrm_options[ $key ][ $lang ] = $cntctfrm_options[ $key ]['default'];
			}
		}

		$page_url = esc_url( add_query_arg( array() ) . '#cntctfrm_contact_form' );

		/* If contact form submited */

		$form_submited = isset( $_POST['cntctfrm_form_submited'] ) ? $_POST['cntctfrm_form_submited'] : 0;

		$name = ( isset( $_POST['cntctfrm_contact_name'] ) && $cntctfrm_form_count == $form_submited ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_name'] ) ) : "";
		$address = ( isset( $_POST['cntctfrm_contact_address'] ) && $cntctfrm_form_count == $form_submited ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_address'] ) ) : "";
		$email = ( isset( $_POST['cntctfrm_contact_email'] ) && $cntctfrm_form_count == $form_submited ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_email'] ) ) : "";
		$subject = ( isset( $_POST['cntctfrm_contact_subject'] ) && $cntctfrm_form_count == $form_submited ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_subject'] ) ) : "";
		$message = ( isset( $_POST['cntctfrm_contact_message'] ) && $cntctfrm_form_count == $form_submited ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_message'] ) ) : "";
		$phone = ( isset( $_POST['cntctfrm_contact_phone'] ) && $cntctfrm_form_count == $form_submited ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_phone'] ) ) : "";

		$name = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $name ) ) );
		$address = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $address ) ) );
		$email = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $email ) ) );
		$subject = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $subject ) ) );
		$message = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $message ) ) );
		$phone = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $phone ) ) );

		$send_copy = ( isset( $_POST['cntctfrm_contact_send_copy'] ) && $cntctfrm_form_count == $form_submited ) ? $_POST['cntctfrm_contact_send_copy'] : "";
		/* If it is good */

		if ( true === $cntctfrm_result && $cntctfrm_form_count == $form_submited ) {
			$_SESSION['cntctfrm_send_mail'] = true;

			if ( 1 == $cntctfrm_options['action_after_send'] )
				$content .= '<div id="cntctfrm_contact_form' . $form_countid . '"><div id="cntctfrm_thanks">' . $cntctfrm_options['thank_text'][ $lang ] . '</div></div>';
			else
				$content .= "<script type='text/javascript'>window.location.href = '" . $cntctfrm_options['redirect_url'] . "';</script>";

		} elseif ( false === $cntctfrm_result && $cntctfrm_form_count == $form_submited ) {
			/* If email not be delivered */
			$cntctfrm_error_message['error_form'] = __( "Sorry, email message could not be delivered.", 'contact-form-plugin' );
		}

		if ( true !== $cntctfrm_result || $cntctfrm_form_count != $form_submited ) {
			$_SESSION['cntctfrm_send_mail'] = false;

			$classes = ( $cntctfrm_options['layout'] === 1 ) ? ' cntctfrm_one_column' : ' cntctfrm_two_columns';
			$classes .= is_rtl() ? ' cntctfrm_rtl' : ' cntctfrm_ltr';

			/* Output form */
			$content .= '<form method="post" id="cntctfrm_contact_form' . $form_countid . '" class="cntctfrm_contact_form' . $classes . '"';
			$content .= ' action="' . $page_url .  $form_countid . '" enctype="multipart/form-data">';
			if ( isset( $cntctfrm_error_message['error_form'] ) && $cntctfrm_form_count == $form_submited ) {
				$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_form'] . '</div>';
			}

			$cntctfrm_ordered_fields = cntctfrm_get_ordered_fields();

			for ( $i = 1; $i <= $cntctfrm_options['layout']; $i++ ) {

				$column = ( $i == 1 ) ? 'first_column' : 'second_column';
				$content .= '<div id="cntctfrm_' . $column . '" class="cntctfrm_column">';

				foreach ( $cntctfrm_ordered_fields[ $column ] as $cntctfrm_field ) {
					switch( $cntctfrm_field ) {
						case 'cntctfrm_contact_name':
							if ( 1 == $cntctfrm_options['display_name_field'] ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_name_wrap">';
									$content .= '<div class="cntctfrm_label cntctfrm_label_name">
										<label for="cntctfrm_contact_name' . $form_countid . '">' . $cntctfrm_options['name_label'][ $lang ] . ( $cntctfrm_options['required_name_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' );
									$content .= '</div>';
									if ( isset( $cntctfrm_error_message['error_name'] ) && $cntctfrm_form_count == $form_submited ) {
										$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_name'] . '</div>';
									}
									$content .= '<div class="cntctfrm_input cntctfrm_input_name">
										<input class="text" type="text" size="40" value="' . $name . '" name="cntctfrm_contact_name" id="cntctfrm_contact_name' . $form_countid . '" />';
									$content .= '</div>';
								$content .= '</div>';
							}
							break;
						case 'cntctfrm_contact_address':
							if ( 1 == $cntctfrm_options['display_address_field'] ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_address_wrap">';
									$content .= '<div class="cntctfrm_label cntctfrm_label_address">
											<label for="cntctfrm_contact_address' . $form_countid . '">' . $cntctfrm_options['address_label'][ $lang ] . ( $cntctfrm_options['required_address_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '</div>';
									if ( isset( $cntctfrm_error_message['error_address'] ) && $cntctfrm_form_count == $form_submited ) {
										$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_address'] . '</div>';
									}
									$content .= '<div class="cntctfrm_input cntctfrm_input_address">
										<input class="text" type="text" size="40" value="' . $address . '" name="cntctfrm_contact_address" id="cntctfrm_contact_address' . $form_countid . '" />';
									$content .= '</div>';
								$content .= '</div>';
							}
							break;
						case 'cntctfrm_contact_email':
							$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_email_wrap">';
								$content .= '<div class="cntctfrm_label cntctfrm_label_email">
										<label for="cntctfrm_contact_email' . $form_countid . '">' . $cntctfrm_options['email_label'][ $lang ] . ( $cntctfrm_options['required_email_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '
									</div>';
								if ( isset( $cntctfrm_error_message['error_email'] ) && $cntctfrm_form_count == $form_submited ) {
									$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_email'] . '</div>';
								}
								$content .= '<div class="cntctfrm_input cntctfrm_input_email">
										<input class="text" type="text" size="40" value="' . $email . '" name="cntctfrm_contact_email" id="cntctfrm_contact_email' . $form_countid . '" />';
								$content .= '</div>';
							$content .= '</div>';
							break;
						case 'cntctfrm_contact_phone':
							if ( 1 == $cntctfrm_options['display_phone_field'] ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_phone_wrap">';
									$content .= '<div class="cntctfrm_label cntctfrm_label_phone">
											<label for="cntctfrm_contact_phone' . $form_countid . '">' . $cntctfrm_options['phone_label'][ $lang ] . ( $cntctfrm_options['required_phone_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '
										</div>';
									if ( isset( $cntctfrm_error_message['error_phone'] ) && $cntctfrm_form_count == $form_submited ) {
										$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_phone'] . '</div>';
									}
									$content .= '<div class="cntctfrm_input cntctfrm_input_phone">
										<input class="text" type="text" size="40" value="' . $phone . '" name="cntctfrm_contact_phone" id="cntctfrm_contact_phone' . $form_countid . '" />';
									$content .= '</div>';
								$content .= '</div>';
							}
							break;
						case 'cntctfrm_contact_subject':
							$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_subject_wrap">';
								$content .= '<div class="cntctfrm_label cntctfrm_label_subject">
										<label for="cntctfrm_contact_subject' . $form_countid . '">' . $cntctfrm_options['subject_label'][ $lang ] . ( $cntctfrm_options['required_subject_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '
									</div>';
								if ( isset( $cntctfrm_error_message['error_subject'] ) && $cntctfrm_form_count == $form_submited ) {
									$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_subject'] . '</div>';
								}
								$content .= '<div class="cntctfrm_input cntctfrm_input_subject">
									<input class="text" type="text" size="40" value="' . $subject . '" name="cntctfrm_contact_subject" id="cntctfrm_contact_subject' . $form_countid . '" />';
								$content .= '</div>';
							$content .= '</div>';
							break;
						case 'cntctfrm_contact_message':
							$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_message_wrap">';
								$content .= '<div class="cntctfrm_label cntctfrm_label_message">
									<label for="cntctfrm_contact_message' . $form_countid . '">' . $cntctfrm_options['message_label'][ $lang ] . ( $cntctfrm_options['required_message_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '
								</div>';
								if ( isset( $cntctfrm_error_message['error_message'] ) && $cntctfrm_form_count == $form_submited ) {
									$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_message'] . '</div>';
								}
								$content .= '<div class="cntctfrm_input cntctfrm_input_message">
									<textarea rows="5" cols="30" name="cntctfrm_contact_message" id="cntctfrm_contact_message' . $form_countid . '">' . $message . '</textarea>';
								$content .= '</div>';
							$content .= '</div>';
							break;
						case 'cntctfrm_contact_attachment':
							if ( 1 == $cntctfrm_options['attachment'] ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_attachment_wrap">';
									$content .= '<div class="cntctfrm_label cntctfrm_label_attachment">
											<label for="cntctfrm_contact_attachment' . $form_countid . '">' . $cntctfrm_options['attachment_label'][ $lang ] . '</label>
										</div>';
									if ( isset( $cntctfrm_error_message['error_attachment'] ) && $cntctfrm_form_count == $form_submited ) {
										$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_attachment'] . '</div>';
									}
									$content .= '<div class="cntctfrm_input cntctfrm_input_attachment">
											<input type="file" name="cntctfrm_contact_attachment" id="cntctfrm_contact_attachment' . $form_countid . '"' . ( isset( $cntctfrm_error_message['error_attachment'] ) ? "class='error'": "" ) . ' />';
									if ( 1 == $cntctfrm_options['attachment_explanations'] ) {
											$content .= '<label class="cntctfrm_contact_attachment_extensions"><br />' . $cntctfrm_options['attachment_tooltip'][ $lang ] . '</label>';
									}
									$content .= '</div>';
								$content .= '</div>';
							}
							break;
						case 'cntctfrm_contact_send_copy':
							if ( 1 == $cntctfrm_options['send_copy'] ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_attachment_wrap">';
									$content .= '<div class="cntctfrm_checkbox cntctfrm_checkbox_send_copy">
										<input type="checkbox" value="1" name="cntctfrm_contact_send_copy" id="cntctfrm_contact_send_copy"' . ( $send_copy == '1' ? ' checked="checked" ' : "" ) . ' />
										<label for="cntctfrm_contact_send_copy">' . $cntctfrm_options['send_copy_label'][ $lang ] . '</label>';
									$content .= '</div>';
								$content .= '</div>';
							}
							break;
						case 'cntctfrm_subscribe':
							if ( has_filter( 'sbscrbr_cntctfrm_checkbox_add' ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] ) ) || ! empty( $cntctfrm_options['display_subscribe'] ) ) ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_checkbox_subscribe_wrap">';
									$content .= '<div class="cntctfrm_checkbox cntctfrm_checkbox_subscribe">';
										$cntctfrm_sbscrbr_checkbox = apply_filters( 'sbscrbr_cntctfrm_checkbox_add', array(
											'form_id' => 'cntctfrm_' . $cntctfrm_form_count,
											'display' => ( isset( $cntctfrm_error_message['error_sbscrbr'] ) && $cntctfrm_form_count == $form_submited ) ? $cntctfrm_error_message['error_sbscrbr'] : false ) );
										if ( isset( $cntctfrm_sbscrbr_checkbox['content'] ) ) {
											$content .= $cntctfrm_sbscrbr_checkbox['content'];
										}
									$content .= '</div>';
								$content .= '</div>';
							}
							break;
						case 'cntctfrm_captcha':
							$removed_filters = cntctfrm_handle_captcha_filters( 'remove_filters' );
							if ( has_filter( 'cntctfrm_display_captcha' ) ) {
								$display_captcha_label = '';
								if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['enabled'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_captcha'] ) ) ) ) {
									$display_captcha = true;
								}
								if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_google_captcha'] ) ) ) ) {
									$display_google_captcha = true;
								}

								if ( ! empty( $display_captcha ) ) {
									if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) ) {
										$captcha_label = isset( $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['label'] ] ) ? $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['label'] ] : '';
										if ( ! empty( $captcha_label ) ) {
											$captcha_required_symbol = sprintf( '<span class="required">%s</span>', ( ! empty( $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['required_symbol'] ] ) ) ? $cntctfrm_related_plugins['captcha']['options'][ $cntctfrm_related_plugins['captcha']['required_symbol'] ] : '' );
											$display_captcha_label = $captcha_label . $captcha_required_symbol;
										}
									}
								}

								if ( ! empty( $display_captcha ) || ! empty( $display_google_captcha ) ) {
									$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_captcha_wrap">';
										$content .= '<div class="cntctfrm_label cntctfrm_label_captcha">
											<label>' . $display_captcha_label . '</label>
										</div>';
										if ( isset( $cntctfrm_error_message['error_captcha'] ) && $cntctfrm_form_count == $form_submited )
											$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_captcha'] . '</div>';
										$content .= '<div class="cntctfrm_input cntctfrm_input_captcha">';
										$content .= apply_filters( 'cntctfrm_display_captcha', ( $cntctfrm_form_count == $form_submited ) ? $cntctfrm_error_message : false, '', 'bws_contact' );
										$content .= '</div>';
									$content .= '</div>';
								}
							}
							cntctfrm_handle_captcha_filters( 'add_filters', $removed_filters );
							break;
						default:
							break;
					}
				}

				$content .= '</div>';
			}

			$content .= '<div class="clear"></div>';

			$cntctfrm_direction = is_rtl() ? 'rtl' : 'ltr';
			$submit_position_value = array(
				'ltr' => array(
					'left'  => 1,
					'right' => 2
				),
				'rtl' => array(
					'left'  => 2,
					'right' => 1
				),
			);

			$content .= '<div class="cntctfrm_submit_wrap">';
				for ( $i = 1; $i <= 2; $i++ ) {
					$column = ( $i == 1 ) ? 'first_column' : 'second_column';
					$content .= '<div id="cntctfrm_submit_' . $column . '" class="cntctfrm_column">';
					if ( $i == $submit_position_value[ $cntctfrm_direction ][ $cntctfrm_options['submit_position'] ] ) {
						$content .= '<div class="cntctfrm_input cntctfrm_input_submit" style="text-align: ' . $cntctfrm_options['submit_position'] . ' !important;">';
						if ( isset( $atts['id'] ) )
							$content .= '<input type="hidden" value="' . esc_attr( $atts['id'] ) . '" name="cntctfrmmlt_shortcode_id">';
						$content .= '<input type="hidden" value="send" name="cntctfrm_contact_action"><input type="hidden" value="Version: 3.30" />
							<input type="hidden" value="' . esc_attr( $lang ) . '" name="cntctfrm_language">
							<input type="hidden" value="' . $cntctfrm_form_count . '" name="cntctfrm_form_submited">
							<input type="hidden" value="' . $options_name . '" name="cntctfrm_options_name">
							<input type="submit" value="' . $cntctfrm_options['submit_label'][ $lang ] . '" class="cntctfrm_contact_submit" />
						</div>';
					}
					$content .= '</div>';
				}
			$content .= '<div class="clear"></div>
			</div>
			</form>';
		}
		return $content ;
	}
}

/**
* add or remove filters for compatibility with Captcha and Google Captcha
* $action            	string    can be 'remove_filters' or 'add_filters'
* $removed_filters    	array of existed filters (returned from this function when 'remove_filters' action)
* @return  				array of existed filters for 'remove_filters' or 'false' for 'add_filters'
*/
if ( ! function_exists( 'cntctfrm_handle_captcha_filters' ) ) {
	function cntctfrm_handle_captcha_filters( $action, $removed_filters = false ) {
		global $cntctfrm_options, $cntctfrm_related_plugins;

		if ( 'remove_filters' == $action ) {

			if ( empty( $cntctfrm_related_plugins ) )
				cntctfrm_related_plugins();

			$contact_form_multi_active = cntctfrm_check_cf_multi_active();

			$removed_filters = $remove_captcha = array();

			if ( ! ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['enabled'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_captcha'] ) ) ) ) )
				$remove_captcha[] = 'captcha';

			if ( ! ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_google_captcha'] ) ) ) ) )
				$remove_captcha[] = 'google-captcha';

			$filters = array(
				'google-captcha'	=> array(
					'gglcptch_cf_display'	=> 'gglcptch_recaptcha_check',
					'gglcptchpr_cf_display'	=> 'gglcptchpr_recaptcha_check'
				),
				'captcha'			=> array(
					'cptch_custom_form'			=> 'cptch_check_custom_form',
					'cptchpls_custom_form'		=> 'cptchpls_check_custom_form',
					'cptchpr_custom_form'		=> 'cptchpr_check_custom_form',
				)
			);

			if ( ! empty( $remove_captcha ) ) {
				foreach ( $remove_captcha as $remove ) {
					foreach ( $filters[ $remove ] as $display_filter => $check_filter ) {
						if ( has_filter( 'cntctfrm_display_captcha', $display_filter ) ) {
							remove_filter( 'cntctfrm_display_captcha', $display_filter );
							$removed_filters[] = array( 'cntctfrm_display_captcha' => $display_filter );
						}

						if ( has_filter( 'cntctfrm_check_form', $check_filter ) ) {
							remove_filter( 'cntctfrm_check_form', $check_filter );
							$removed_filters[] = array( 'cntctfrm_check_form' => $check_filter );
						}
					}
				}
			}
			return $removed_filters;
		} elseif ( 'add_filters' == $action && ! empty( $removed_filters ) ) {
			foreach ( $removed_filters as $function_array ) {
				foreach ( $function_array as $tag => $function ) {
					add_filter( $tag, $function );
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'cntctfrm_check_and_send' ) ) {
	function cntctfrm_check_and_send() {
		global $cntctfrm_result, $cntctfrm_options;
		if ( ( isset( $_POST['cntctfrm_contact_action'] ) && isset( $_POST['cntctfrm_language'] ) ) || true === $cntctfrm_result ) {
			$cntctfrm_options = get_option( $_POST['cntctfrm_options_name'] );

			if ( isset( $_POST['cntctfrm_contact_action'] ) ) {
				/* Check all input data */
				$cntctfrm_result = cntctfrm_check_form();
			}
			/* If it is good */
			if ( true === $cntctfrm_result ) {
				$_SESSION['cntctfrm_send_mail'] = true;
				if ( 0 == $cntctfrm_options['action_after_send'] ) {
					wp_redirect( $cntctfrm_options['redirect_url'] );
					exit;
				}
			}
		}
	}
}

/* Check all input data */
if ( ! function_exists( 'cntctfrm_check_form' ) ) {
	function cntctfrm_check_form() {
		global $cntctfrm_error_message, $cntctfrm_options, $cntctfrm_related_plugins;

		if ( empty( $cntctfrm_related_plugins ) )
			cntctfrm_related_plugins();

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		$removed_filters = cntctfrm_handle_captcha_filters( 'remove_filters' );

		$language = isset( $_POST['cntctfrm_language'] ) ? $_POST['cntctfrm_language'] : 'default';
		$cntctfrm_path_of_uploaded_file = $cntctfrm_result = "";
		/* Error messages array */
		$cntctfrm_error_message = array();
		$name = isset( $_POST['cntctfrm_contact_name'] ) ?  htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_name'] ) ) : "";
		$address = isset( $_POST['cntctfrm_contact_address'] ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_address'] ) ) : "";
		$email = isset( $_POST['cntctfrm_contact_email'] ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_email'] ) ) : "";
		$subject = isset( $_POST['cntctfrm_contact_subject'] ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_subject'] ) ) : "";
		$message = isset( $_POST['cntctfrm_contact_message'] ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_message'] ) ) : "";
		$phone = isset( $_POST['cntctfrm_contact_phone'] ) ? htmlspecialchars( stripslashes( $_POST['cntctfrm_contact_phone'] ) ) : "";

		$name = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $name ) ) );
		$address = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $address ) ) );
		$email = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $email ) ) );
		$subject = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $subject ) ) );
		$message = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $message ) ) );
		$phone = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $phone ) ) );

		/* check language and replace with en default if need */
		if ( ! in_array( $language, $cntctfrm_options['language'] ) ) {
			foreach ( $cntctfrm_options as $key => $value ) {
				if ( is_array( $value ) && array_key_exists( 'default', $value ) && ( ! array_key_exists( $language, $value ) || ( isset( $cntctfrm_options[ $key ][ $language ] ) && $cntctfrm_options[ $key ][ $language ] == '' ) ) ) {
					$cntctfrm_options[ $key ][ $language ] = $cntctfrm_options[ $key ]['default'];
				}
			}
		}

		if ( 1 == $cntctfrm_options['required_name_field'] && 1 == $cntctfrm_options['display_name_field'] )
			$cntctfrm_error_message['error_name'] = $cntctfrm_options['name_error'][ $language ];
		if ( 1 == $cntctfrm_options['required_address_field'] && 1 == $cntctfrm_options['display_address_field'] )
			$cntctfrm_error_message['error_address'] = $cntctfrm_options['address_error'][ $language ];
		if ( 1 == $cntctfrm_options['required_email_field'] )
			$cntctfrm_error_message['error_email'] = $cntctfrm_options['email_error'][ $language ];
		if ( 1 == $cntctfrm_options['required_subject_field'] )
			$cntctfrm_error_message['error_subject'] = $cntctfrm_options['subject_error'][ $language ];
		if ( 1 == $cntctfrm_options['required_message_field'] )
			$cntctfrm_error_message['error_message'] = $cntctfrm_options['message_error'][ $language ];
		if ( 1 == $cntctfrm_options['required_phone_field'] && 1 == $cntctfrm_options['display_phone_field'] )
			$cntctfrm_error_message['error_phone'] = $cntctfrm_options['phone_error'][ $language ];
		$cntctfrm_error_message['error_form'] = $cntctfrm_options['form_error'][ $language ];
		if ( 1 == $cntctfrm_options['attachment'] ) {
			global $cntctfrm_path_of_uploaded_file, $cntctfrm_mime_type;
			$cntctfrm_mime_type= array(
				'html'=>'text/html',
				'htm'=>'text/html',
				'txt'=>'text/plain',
				'css'=>'text/css',
				'gif'=>'image/gif',
				'png'=>'image/x-png',
				'jpeg'=>'image/jpeg',
				'jpg'=>'image/jpeg',
				'jpe'=>'image/jpeg',
				'tiff'=>'image/tiff',
				'tif'=>'image/tiff',
				'bmp'=>'image/x-ms-bmp',
				'ai'=>'application/postscript',
				'eps'=>'application/postscript',
				'ps'=>'application/postscript',
				'csv'=>'text/csv',
				'rtf'=>'application/rtf',
				'pdf'=>'application/pdf',
				'doc'=>'application/msword',
				'docx'=>'application/msword',
				'xls'=>'application/vnd.ms-excel',
				'xlsx'=>'application/vnd.ms-excel',
				'zip'=>'application/zip',
				'rar'=>'application/rar',
				'wav'=>'audio/wav',
				'mp3'=>'audio/mp3',
				'ppt'=>'application/vnd.ms-powerpoint',
				'aar'=>'application/sb-replay',
				'sce'=>'application/sb-scenario'
			);
			$cntctfrm_error_message['error_attachment'] = $cntctfrm_options['attachment_error'][ $language ];
		}
		/* Check information wich was input in fields */
		if ( 1 == $cntctfrm_options['display_name_field'] && 1 == $cntctfrm_options['required_name_field'] && "" != $name )
			unset( $cntctfrm_error_message['error_name'] );
		if ( 1 == $cntctfrm_options['display_address_field'] && 1 == $cntctfrm_options['required_address_field'] && "" != $address )
			unset( $cntctfrm_error_message['error_address'] );
		if ( 1 == $cntctfrm_options['required_email_field'] && "" != $email && is_email( trim( stripslashes( $email ) ) ) )
			unset( $cntctfrm_error_message['error_email'] );
		if ( 1 == $cntctfrm_options['display_phone_field'] && 1 == $cntctfrm_options['required_phone_field'] && "" != $phone )
			unset( $cntctfrm_error_message['error_phone'] );
		if ( 1 == $cntctfrm_options['required_subject_field'] && "" != $subject )
			unset( $cntctfrm_error_message['error_subject'] );
		if ( 1 == $cntctfrm_options['required_message_field'] && "" != $message )
			unset( $cntctfrm_error_message['error_message'] );

		/* If captcha plugin exists */
		$result = true;
		if ( has_filter( 'cntctfrm_check_form' ) )
			$result = apply_filters( 'cntctfrm_check_form', true );

		cntctfrm_handle_captcha_filters( 'add_filters', $removed_filters );

		if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['enabled'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_captcha'] ) ) ) ) {
			if ( false === $result ) { /* for CAPTCHA older than PRO - v1.0.7, PLUS - v1.1.0 v FREE - 1.2.5 */
				$cntctfrm_error_message['error_captcha'] = $cntctfrm_options['captcha_error'][ $language ];
			} else if ( ! empty( $result ) && ( is_string( $result ) || is_wp_error( $result ) ) ) {
				$cntctfrm_error_message['error_captcha'] = is_string( $result ) ? $result : $result->get_error_message();
			}
		}
		if ( isset( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && "" != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) {

			$new_filename = 'cntctfrm_' . md5( sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] ) . time() . $email . mt_rand() ) . '_' . sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] );

			if ( is_multisite() ) {
				if ( defined('UPLOADS') ) {
					if ( ! is_dir( ABSPATH . UPLOADS ) ) {
						wp_mkdir_p( ABSPATH . UPLOADS );
					}
					$cntctfrm_path_of_uploaded_file = ABSPATH . UPLOADS . $new_filename;
				} else if ( defined( 'BLOGUPLOADDIR' ) ) {
					if ( ! is_dir( BLOGUPLOADDIR ) ) {
						wp_mkdir_p( BLOGUPLOADDIR );
					}
					$cntctfrm_path_of_uploaded_file = BLOGUPLOADDIR . $new_filename;
				} else {
					$uploads = wp_upload_dir();
					if ( ! isset( $uploads['path'] ) && isset( $uploads['error'] ) )
						$cntctfrm_error_message['error_attachment'] = $uploads['error'];
					else
						$cntctfrm_path_of_uploaded_file = $uploads['path'] . "/" . $new_filename;
				}
			} else {
				$uploads = wp_upload_dir();
				if ( ! isset( $uploads['path'] ) && isset( $uploads['error'] ) )
					$cntctfrm_error_message['error_attachment'] = $uploads['error'];
				else
					$cntctfrm_path_of_uploaded_file = $uploads['path'] . "/" . $new_filename;
			}

			$tmp_path = $_FILES["cntctfrm_contact_attachment"]["tmp_name"];
			$path_info = pathinfo( $cntctfrm_path_of_uploaded_file );

			if ( array_key_exists( strtolower( $path_info['extension'] ), $cntctfrm_mime_type ) ) {
				if ( is_uploaded_file( $tmp_path ) ) {
					if ( move_uploaded_file( $tmp_path, $cntctfrm_path_of_uploaded_file ) ) {
						do_action( 'cntctfrm_get_attachment_data', $cntctfrm_path_of_uploaded_file );
						unset( $cntctfrm_error_message['error_attachment'] );
					} else {
						$letter_upload_max_size = substr( ini_get( 'upload_max_filesize' ), -1 );
						/* $upload_max_size = substr( ini_get('upload_max_filesize'), 0, -1 ); */
						$upload_max_size = '1';
						switch( strtoupper( $letter_upload_max_size ) ) {
							case 'P':
								$upload_max_size *= 1024;
							case 'T':
								$upload_max_size *= 1024;
							case 'G':
								$upload_max_size *= 1024;
							case 'M':
								$upload_max_size *= 1024;
							case 'K':
								$upload_max_size *= 1024;
								break;
						}
						if ( isset( $_FILES["cntctfrm_contact_attachment"]["size"] ) && $_FILES["cntctfrm_contact_attachment"]["size"] <= $upload_max_size ) {
							$cntctfrm_error_message['error_attachment'] = $cntctfrm_options['attachment_move_error'][ $language ];
						} else {
							$cntctfrm_error_message['error_attachment'] = $cntctfrm_options['attachment_size_error'][ $language ];
						}
					}
				} else {
					$cntctfrm_error_message['error_attachment'] = $cntctfrm_options['attachment_upload_error'][ $language ];
				}
			}
		} else {
			unset( $cntctfrm_error_message['error_attachment'] );
		}
		if ( 1 == count( $cntctfrm_error_message ) ) {
			if ( has_filter( 'sbscrbr_cntctfrm_checkbox_check' ) ) {
				$cntctfrm_sbscrbr_check = apply_filters( 'sbscrbr_cntctfrm_checkbox_check', array(
					'form_id' => 'cntctfrm_' . $_POST['cntctfrm_form_submited'],
					'email'   => $email,
					'name'    => $name
				) );
				if ( isset( $cntctfrm_sbscrbr_check['response'] ) && $cntctfrm_sbscrbr_check['response']['type'] == 'error' ) {
					$cntctfrm_error_message['error_sbscrbr'] = $cntctfrm_sbscrbr_check['response'];
					return $cntctfrm_result;
				}
			}
			unset( $cntctfrm_error_message['error_form'] );
			/* If all is good - send mail */
			$cntctfrm_result = cntctfrm_send_mail();

			$save_emails  = false;
			if ( ! $contact_form_multi_active && array_key_exists( 'contact-form-to-db' , $cntctfrm_related_plugins ) )
				$save_emails = ! empty( $cntctfrm_related_plugins['contact-form-to-db']['options'][ $cntctfrm_related_plugins['contact-form-to-db']['save_option'] ] );
			else
				$save_emails = ! empty( $cntctfrm_options['save_email_to_db'] );

			if ( $save_emails )
				do_action( 'cntctfrm_check_dispatch', $cntctfrm_result );
		}
		return $cntctfrm_result;
	}
}

/* Send mail function */
if ( ! function_exists( 'cntctfrm_send_mail' ) ) {
	function cntctfrm_send_mail() {
		global $cntctfrm_options, $cntctfrm_path_of_uploaded_file, $wp_version, $wpdb;
		$to = $headers  = "";

		$lang = isset( $_POST['cntctfrm_language'] ) ? $_POST['cntctfrm_language'] : 'default';

		$name = isset( $_POST['cntctfrm_contact_name'] ) ? $_POST['cntctfrm_contact_name'] : "";
		$address = isset( $_POST['cntctfrm_contact_address'] ) ? $_POST['cntctfrm_contact_address'] : "";
		$email = isset( $_POST['cntctfrm_contact_email'] ) ? stripslashes( $_POST['cntctfrm_contact_email'] ) : "";
		$subject = isset( $_POST['cntctfrm_contact_subject'] ) ? $_POST['cntctfrm_contact_subject'] : "";
		$message = isset( $_POST['cntctfrm_contact_message'] ) ? $_POST['cntctfrm_contact_message'] : "";
		$phone = isset( $_POST['cntctfrm_contact_phone'] ) ? $_POST['cntctfrm_contact_phone'] : "";
		$user_agent = cntctfrm_clean_input( $_SERVER['HTTP_USER_AGENT'] );

		$name = stripslashes( strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $name ) ) ) );
		$address = stripslashes( strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $address ) ) ) );
		$email = stripslashes( strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $email ) ) ) );
		$subject = stripslashes( strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $subject ) ) ) );
		$message = stripslashes( strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $message ) ) ) );
		$phone = stripslashes( strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', $phone ) ) ) );

		if ( isset( $_SESSION['cntctfrm_send_mail'] ) && true == $_SESSION['cntctfrm_send_mail'] )
			return true;

		if ( 'user' == $cntctfrm_options['select_email'] ) {
			if ( false !== $user = get_user_by( 'login', $cntctfrm_options['user_email'] ) )
				$to = $user->user_email;
		} elseif ( $cntctfrm_options['select_email'] == 'custom' ) {
			$to = $cntctfrm_options['custom_email'];
		}

		/* If email options are not certain choose admin email */
		if ( "" == $to )
			$to = get_option( "admin_email" );

		if ( "" != $to ) {
			$user_info_string = $userdomain = '';
			$attachments = array();

			if ( 'on' == strtolower( getenv('HTTPS') ) ) {
				$form_action_url = esc_url( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			} else {
				$form_action_url = esc_url( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			}

			if ( 1 == $cntctfrm_options['display_add_info'] ) {
				$cntctfrm_remote_addr = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP );
				$userdomain = @gethostbyaddr( $cntctfrm_remote_addr );
				if ( 1 == $cntctfrm_options['display_add_info'] ||
						1 == $cntctfrm_options['display_sent_from'] ||
						1 == $cntctfrm_options['display_coming_from'] ||
						1 == $cntctfrm_options['display_user_agent'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td><br /></td><td><br /></td></tr>';
				}
				if ( 1 == $cntctfrm_options['display_sent_from'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td>' . __( 'Sent from (ip address)', 'contact-form-plugin' ) . ':</td><td>' . $cntctfrm_remote_addr . " ( " . $userdomain . " )" . '</td></tr>';
					else
						$user_info_string .= __( 'Sent from (ip address)', 'contact-form-plugin' ) . ': ' . $cntctfrm_remote_addr . " ( " . $userdomain . " )" . "\n";
				}
				if ( 1 == $cntctfrm_options['display_date_time'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td>' . __('Date/Time', 'contact-form-plugin') . ':</td><td>' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( current_time( 'mysql' ) ) ) . '</td></tr>';
					else
						$user_info_string .= __( 'Date/Time', 'contact-form-plugin' ) . ': ' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( current_time( 'mysql' ) ) ) . "\n";
				}
				if ( 1 == $cntctfrm_options['display_coming_from'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td>' . __( 'Sent from (referer)', 'contact-form-plugin' ) . ':</td><td>' . $form_action_url . '</td></tr>';
					else
						$user_info_string .= __( 'Sent from (referer)', 'contact-form-plugin' ) . ': ' . $form_action_url . "\n";
				}
				if ( 1 == $cntctfrm_options['display_user_agent'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td>' . __( 'Using (user agent)', 'contact-form-plugin' ) . ':</td><td>' . $user_agent . '</td></tr>';
					else
						$user_info_string .= __( 'Using (user agent)', 'contact-form-plugin' ) . ': ' . $user_agent . "\n";
				}
			}

			/* Message */
			$message_order_fields = array_merge( $cntctfrm_options['order_fields']['first_column'], $cntctfrm_options['order_fields']['second_column'] );

			if ( 1 == $cntctfrm_options['html_email'] ) {
				$message_text = '<html>
					<head>
						<title>'. __( "Contact from", 'contact-form-plugin' ) . ' ' . get_bloginfo( 'name' ) . '</title>
					</head>
					<body>
						<table>
							<tr>
								<td>' . __( "Site", 'contact-form-plugin' ) . '</td>
								<td>' . get_bloginfo( "url" ) . '</td>
							</tr>';
				foreach ( $message_order_fields as $field ){
					$field = str_replace( 'cntctfrm_contact_', '', $field );
					switch ( $field ) {
						case "name":
							if ( 1 == $cntctfrm_options['display_name_field'] ) {
								$message_text .= '<tr><td width="160">';
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['name_label'][ $lang ] : __( "Name", 'contact-form-plugin' );
								$message_text .= '</td><td>'. $name .'</td></tr>';
							}
							break;
						case "address":
							if ( 1 == $cntctfrm_options['display_address_field'] ) {
								$message_text .= '<tr><td>';
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['address_label'][ $lang ] : __( "Address", 'contact-form-plugin' );
								$message_text .= '</td><td>'. $address .'</td></tr>';
							}
							break;
						case "email":
							$message_text .= '<tr><td>';
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['email_label'][ $lang ] : __( "Email", 'contact-form-plugin' );
							$message_text .= '</td><td>'. $email .'</td></tr>';
							break;
						case "subject":
							$message_text .= '<tr><td>';
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['subject_label'][ $lang ] : __( "Subject", 'contact-form-plugin' );
							$message_text .= '</td><td>' . $subject .'</td></tr>';
							break;
						case "message":
							$message_text .= '<tr><td>';
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['message_label'][ $lang ] : __( "Message", 'contact-form-plugin' );
							$message_text .= '</td><td>' . $message .'</td></tr>';
							break;
						case "phone":
							if ( 1 == $cntctfrm_options['display_phone_field'] ) {
								$message_text .= '<tr><td>';
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['phone_label'][ $lang ] : __( "Phone Number", 'contact-form-plugin' );
								$message_text .= '</td><td>'. $phone .'</td></tr>';
							}
							break;
					}
				}
				$message_text .= '<tr><td><br /></td><td><br /></td></tr>';

				$message_text_for_user = $message_text . '</table></body></html>';
				$message_text .= $user_info_string . '</table></body></html>';
			} else {
				$message_text = __( "Site", 'contact-form-plugin' ) . ': ' . get_bloginfo("url") . "\n";;
				foreach ( $message_order_fields as $field ) {
					$field = str_replace( 'cntctfrm_contact_', '', $field );
					switch ( $field ) {
						case "name":
							if ( 1 == $cntctfrm_options['display_name_field'] ) {
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['name_label'][ $lang ] : __( "Name", 'contact-form-plugin' );
								$message_text .= ': '. $name . "\n";
							}
							break;
						case "address":
							if ( 1 == $cntctfrm_options['display_address_field'] ) {
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['address_label'][ $lang ] : __( "Address", 'contact-form-plugin' );
								$message_text .= ': '. $address . "\n";
							}
							break;
						case "email":
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['email_label'][ $lang ] : __( "Email", 'contact-form-plugin' );
							$message_text .= ': ' . $email . "\n";
							break;
						case "subject":
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['subject_label'][ $lang ] : __( "Subject", 'contact-form-plugin' );
							$message_text .= ': ' . $subject . "\n";
							break;
						case "message":
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['message_label'][ $lang ] : __( "Message", 'contact-form-plugin' );
							$message_text .= ': ' . $message ."\n";
							break;
						case "phone":
							if ( 1 == $cntctfrm_options['display_phone_field'] ) {
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['phone_label'][ $lang ] : __( "Phone Number", 'contact-form-plugin' );
								$message_text .= ': '. $phone . "\n";
							}
							break;
					}
				}
				$message_text .= "\n";

				$message_text_for_user = $message_text;
				$message_text .= $user_info_string;
			}

			do_action( 'cntctfrm_get_mail_data', array( 'sendto' => $to, 'refer' => $form_action_url, 'useragent' => $user_agent ) );

			if ( ! function_exists( 'is_plugin_active' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			/* 'from' name */
			$from_field_name = ( 'custom' == $cntctfrm_options['select_from_field'] ) ? stripslashes( $cntctfrm_options['from_field'] ) : $name;
			/* 'from' email */
			$from_email = ( 'custom' == $cntctfrm_options['from_email'] ) ? stripslashes( $cntctfrm_options['custom_from_email'] ) : stripslashes( $email );
			if ( $from_email == "" || ! is_email( $from_email ) ) {
				$sitename = strtolower( filter_var( $_SERVER['SERVER_NAME'], FILTER_SANITIZE_STRING ) );
				if ( substr( $sitename, 0, 4 ) == 'www.' ) {
					$sitename = substr( $sitename, 4 );
				}
				$from_email = 'wordpress@' . $sitename;
			}

			if ( ( is_plugin_active( 'email-queue/email-queue.php' ) || is_plugin_active( 'email-queue-pro/email-queue-pro.php' ) ) && function_exists( 'mlq_if_mail_plugin_is_in_queue' ) && mlq_if_mail_plugin_is_in_queue( plugin_basename( __FILE__ ) ) ) {
				/* if email-queue plugin is active and this plugin's "in_queue" status is 'ON' */
				/* attachment path */
				$attachment_file = ( 1 == $cntctfrm_options['attachment'] && isset( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && "" != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) ? $cntctfrm_path_of_uploaded_file : '';
				/* headers */
				/* content type */
				$headers .= ( 1 == $cntctfrm_options['html_email'] ) ? 'Content-type: text/html; charset=utf-8' . "\n" : 'Content-type: text/plain; charset=utf-8' . "\n" ;
				/* 'from' name & email */
				$headers .= 'From: ' . $from_field_name . ' <' . $from_email . '>';
				/* send copy */
				if ( isset( $_POST['cntctfrm_contact_send_copy'] ) && 1 == $_POST['cntctfrm_contact_send_copy'] ) {
					do_action( 'cntctfrm_get_mail_data_for_mlq', plugin_basename( __FILE__ ), $email, $subject, $message_text_for_user, $attachment_file, $headers );
				}
				global $mlq_mail_result;
				do_action( 'cntctfrm_get_mail_data_for_mlq', plugin_basename( __FILE__ ), $to, $subject, $message_text, $attachment_file, $headers );
				/* return $mail_result = true if email-queue has successfully inserted mail in its DB; in other case - return false */
				return $mail_result = $mlq_mail_result;
			} else {
				if ( 'wp-mail' == $cntctfrm_options['mail_method'] ) {
					/* To send HTML mail, the Content-type header must be set */
					if ( 1 == $cntctfrm_options['html_email'] )
						$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
					else
						$headers .= 'Content-type: text/plain; charset=utf-8' . "\n";

					/* Additional headers */
					$headers .= 'From: ' . $from_field_name . ' <' . $from_email . '>';

					if ( 1 == $cntctfrm_options['attachment'] && isset( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && "" != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) {
						$path_parts = pathinfo( $cntctfrm_path_of_uploaded_file );
						$cntctfrm_path_of_uploaded_file_changed = $path_parts['dirname'] . '/' . preg_replace( '/^cntctfrm_[A-Z,a-z,0-9]{32}_/i', '', $path_parts['basename'] );

						if ( ! @copy( $cntctfrm_path_of_uploaded_file, $cntctfrm_path_of_uploaded_file_changed ) )
							$cntctfrm_path_of_uploaded_file_changed = $cntctfrm_path_of_uploaded_file;

						$attachments = array( $cntctfrm_path_of_uploaded_file_changed );
					}

					if ( isset( $_POST['cntctfrm_contact_send_copy'] ) && 1 == $_POST['cntctfrm_contact_send_copy'] )
						wp_mail( $email, $subject, $message_text_for_user, $headers, $attachments );

					/* Mail it */
					$mail_result = wp_mail( $to, $subject, $message_text, $headers, $attachments );
					/* Delete attachment */
					if ( 1 == $cntctfrm_options['attachment'] && isset( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && "" != $_FILES["cntctfrm_contact_attachment"]["tmp_name"]
						&& $cntctfrm_path_of_uploaded_file_changed != $cntctfrm_path_of_uploaded_file ) {
						@unlink( $cntctfrm_path_of_uploaded_file_changed );
					}
					if ( 1 == $cntctfrm_options['attachment'] && isset( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && "" != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] && '1' == $cntctfrm_options['delete_attached_file'] ) {
						@unlink( $cntctfrm_path_of_uploaded_file );
					}
					return $mail_result;
				} else {
					/* Set headers */
					$headers  .= 'MIME-Version: 1.0' . "\n";

					if ( 1 == $cntctfrm_options['attachment'] && isset( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && "" != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) {
						$message_block = $message_text;
						$message_block_for_user = $message_text_for_user;

						/* Additional headers */
						$headers .= 'From: ' . $from_field_name . ' <' . $from_email . '>' . "\n";

						$bound_text = "jimmyP123";

						$bound = "--" . $bound_text . "";

						$bound_last = "--" . $bound_text . "--";

						$headers .= "Content-Type: multipart/mixed; boundary=\"$bound_text\"";

						$message_text = $message_text_for_user = __( "If you can see this MIME, it means that the MIME type is not supported by your email client!", 'contact-form-plugin' ) . "\n";

						if ( 1 == $cntctfrm_options['html_email'] ) {
							$message_text .= $bound . "\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message_block . "\n\n";
							$message_text_for_user .= $bound . "\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message_block_for_user . "\n\n";
						} else {
							$message_text .= $bound . "\n" . "Content-Type: text/plain; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message_block . "\n\n";
							$message_text_for_user .= $bound . "\n" . "Content-Type: text/plain; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message_block_for_user . "\n\n";
						}

						$file = file_get_contents( $cntctfrm_path_of_uploaded_file );

						$message_text .= $bound . "\n" .
							"Content-Type: application/octet-stream; name=\"" . sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] ) . "\"\n" .
							"Content-Description: " . basename( $cntctfrm_path_of_uploaded_file ) . "\n" .
							"Content-Disposition: attachment;\n" . " filename=\"" . sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] ) ."\"; size=" . filesize( $cntctfrm_path_of_uploaded_file ) . ";\n" .
							"Content-Transfer-Encoding: base64\n\n" . chunk_split( base64_encode( $file ) ) . "\n\n" .
							$bound_last;
						$message_text_for_user .= $bound . "\n" .
							"Content-Type: application/octet-stream; name=\"" . sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] ) . "\"\n" .
							"Content-Description: " . basename( $cntctfrm_path_of_uploaded_file ) . "\n" .
							"Content-Disposition: attachment;\n" . " filename=\"" . sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] ) ."\"; size=" . filesize( $cntctfrm_path_of_uploaded_file ) . ";\n" .
							"Content-Transfer-Encoding: base64\n\n" . chunk_split( base64_encode( $file ) ) . "\n\n" .
							$bound_last;
					} else {
						/* To send HTML mail, header must be set */
						if ( 1 == $cntctfrm_options['html_email'] )
							$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
						else
							$headers .= 'Content-type: text/plain; charset=utf-8' . "\n";

						/* Additional headers */
						$headers .= 'From: ' . $from_field_name . ' <' . $from_email . '>' . "\n";
					}
					if ( isset( $_POST['cntctfrm_contact_send_copy'] ) && 1 == $_POST['cntctfrm_contact_send_copy'] )
						@mail( $email, $subject, $message_text_for_user, $headers );

					$mail_result = @mail( $to, $subject, $message_text, $headers );
					/* Delete attachment */
					if ( 1 == $cntctfrm_options['attachment'] && isset( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && "" != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] && '1' == $cntctfrm_options['delete_attached_file'] ) {
						@unlink( $cntctfrm_path_of_uploaded_file );
					}
					return $mail_result;
				}
			}
		}
		return false;
	}
}

/**
 * Function that is used by email-queue to check for compatibility
 * @return void
 */
if ( ! function_exists( 'cntctfrm_check_for_compatibility_with_mlq' ) ) {
	function cntctfrm_check_for_compatibility_with_mlq() {
		return false;
	}
}

if ( ! function_exists ( 'cntctfrm_plugin_action_links' ) ) {
	function cntctfrm_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=contact_form.php">' . __( 'Settings', 'contact-form-plugin' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}
/* End function cntctfrm_plugin_action_links */

if ( ! function_exists ( 'cntctfrm_register_plugin_links' ) ) {
	function cntctfrm_register_plugin_links( $links, $file ) {
		$base = plugin_basename(__FILE__);
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[] = '<a href="admin.php?page=contact_form.php">' . __( 'Settings','contact-form-plugin' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/plugins/contact-form-plugin/faq/" target="_blank">' . __( 'FAQ','contact-form-plugin' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . __( 'Support','contact-form-plugin' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'cntctfrm_clean_input' ) ) {
	function cntctfrm_clean_input( $string, $preserve_space = 0 ) {
		if ( is_string( $string ) ) {
			if ( $preserve_space ) {
				return cntctfrm_sanitize_string( strip_tags( stripslashes( $string ) ), $preserve_space );
			}
			return trim( cntctfrm_sanitize_string( strip_tags( stripslashes( $string ) ) ) );
		} else if ( is_array( $string ) ) {
			reset( $string );
			while ( list($key, $value ) = each( $string ) ) {
				$string[ $key ] = cntctfrm_clean_input( $value,$preserve_space );
			}
			return $string;
		} else {
			return $string;
		}
	}
}
/* End function ctf_clean_input */

/* Functions for protecting and validating form vars */
if ( ! function_exists ( 'cntctfrm_sanitize_string' ) ) {
	function cntctfrm_sanitize_string( $string, $preserve_space = 0 ) {
		if ( ! $preserve_space )
			$string = preg_replace("/ +/", ' ', trim( $string ) );

		return preg_replace( "/[<>]/", '_', $string );
	}
}

if ( ! function_exists ( 'cntctfrm_admin_head' ) ) {
	function cntctfrm_admin_head() {
		if ( isset( $_REQUEST['page'] ) && ( 'contact_form.php' == $_REQUEST['page'] ) ) {
			global $wp_version, $cntctfrm_plugin_info;

			wp_enqueue_style( 'cntctfrm_stylesheet', plugins_url( 'css/style.css', __FILE__ ), false, $cntctfrm_plugin_info["Version"] );

			if ( isset( $_GET['action'] ) && 'appearance' == $_GET['action'] ) {
				wp_enqueue_style( 'cntctfrm_form_style', plugins_url( 'css/form_style.css', __FILE__ ), false, $cntctfrm_plugin_info["Version"] );
			}

			$script_vars = array(
				'cntctfrm_nonce' 		=> wp_create_nonce( plugin_basename( __FILE__ ), 'cntctfrm_ajax_nonce_field' ),
				'cntctfrm_confirm_text'  => __( 'Are you sure that you want to delete this language data?', 'contact-form-plugin' )
			);

			if ( wp_is_mobile() )
				wp_enqueue_script( 'jquery-touch-punch' );

			wp_enqueue_script( 'cntctfrm_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ) );
			wp_localize_script( 'cntctfrm_script', 'cntctfrm_ajax', $script_vars );

			if ( isset( $_GET['action'] ) && 'custom_code' == $_GET['action'] )
				bws_plugins_include_codemirror();

			require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );

			$tooltip_args = array(
				'tooltip_id'	=> 'cntctfrm_install_multi_tooltip',
				'css_selector' 	=> '#cntctfrm_show_multi_notice',
				'actions' 		=> array(
					'click' 	=> true,
					'onload' 	=> true,
				),
				'content' 			=> '<h3>' . __( 'Add multiple forms', 'contact-form-plugin' ) . '</h3>' .'<p>' . __( 'Install Contact Form Multi plugin to create unlimited number of contact forms.', 'contact-form-plugin' ) . '</p>',
				'buttons'			=> array(
					array(
						'type' => 'link',
						'link' => 'https://bestwebsoft.com/products/wordpress/plugins/contact-form-multi/?k=747ca825fb44711e2d24e40697747bc6&pn=77&v=' . $cntctfrm_plugin_info["Version"] . '&wp_v=' . $wp_version,
						'text' => __( 'Learn more', 'contact-form-plugin' ),
					),
					'close' => array(
						'type' => 'dismiss',
						'text' => __( 'Close', 'contact-form-plugin' ),
					),
				),
				'position' => array(
					'edge' 		=> 'top',
					'align' 	=> is_rtl() ? 'right' : 'left',
				),
			);
			bws_add_tooltip_in_admin( $tooltip_args );
		}
	}
}

if ( ! function_exists( 'cntctfrm_wp_enqueue_style' ) ) {
	function cntctfrm_wp_enqueue_style() {
		global $cntctfrm_plugin_info;
		wp_enqueue_style( 'cntctfrm_form_style', plugins_url( 'css/form_style.css', __FILE__ ), false, $cntctfrm_plugin_info["Version"] );
	}
}

if ( ! function_exists ( 'cntctfrm_wp_footer' ) ) {
	function cntctfrm_wp_footer() {
		if ( wp_script_is( 'cntctfrm_frontend_script', 'registered' ) ) {
			wp_enqueue_script( 'cntctfrm_frontend_script', plugins_url( 'js/cntctfrm.js', __FILE__ ), array( 'jquery' ) );
		}
	}
}

if ( ! function_exists ( 'cntctfrm_add_language' ) ) {
	function cntctfrm_add_language() {
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( $is_ajax )
			check_ajax_referer( plugin_basename( __FILE__ ), 'cntctfrm_ajax_nonce_field' );
		else
			$_POST['cntctfrm_change_tab'] = $_REQUEST['cntctfrm_languages'];

		$lang_slug = $is_ajax ? $_REQUEST['lang'] : $_REQUEST['cntctfrm_languages'];
		$lang = strip_tags( preg_replace( '/<[^>]*>/', '', preg_replace( '/<script.*<\/[^>]*>/', '', htmlspecialchars( $lang_slug ) ) ) );

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( $contact_form_multi_active ) {
			if ( isset( $_SESSION['cntctfrmmlt_id_form'] ) && $cntctfrm_options =  get_option( 'cntctfrmmlt_options_' . $_SESSION['cntctfrmmlt_id_form'] ) ) {
				/**/
			} else {
				$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
			}
		} else {
			$cntctfrm_options = get_option( 'cntctfrm_options' );
		}

		if ( ! in_array( $lang, $cntctfrm_options['language'] ) ) {
			$cntctfrm_options['language'][] = $lang;

			if ( 'pro' == $contact_form_multi_active && $options_main = get_option( 'cntctfrmmltpr_options_main' ) ) {
				update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
			} elseif ( $contact_form_multi_active ) {
				$options_main = get_option( 'cntctfrmmlt_options_main' );
				update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
			} else {
				update_option( 'cntctfrm_options', $cntctfrm_options );
			}

		}

		if ( ! $contact_form_multi_active ) {
			$result = __( "Use shortcode", 'contact-form-plugin' ) . ' <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=' . $lang . ']</span> ' . __( "for this language", 'contact-form-plugin' );
		} else {
			$result = __( "Use shortcode", 'contact-form-plugin' ) . ' <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=' . $lang . ' id=' . $_SESSION['cntctfrmmlt_id_form'] . ']</span> ' . __( "for this language", 'contact-form-plugin' );
		}

		if ( $is_ajax ) {
			echo json_encode( $result );
			die();
		}
	}
}

if ( ! function_exists ( 'cntctfrm_remove_language' ) ) {
	function cntctfrm_remove_language() {
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
		if ( $is_alax )
			check_ajax_referer( plugin_basename( __FILE__ ), 'cntctfrm_ajax_nonce_field' );
		else
			$_POST['cntctfrm_change_tab'] = 'default';

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( $contact_form_multi_active ) {
			if ( isset( $_SESSION['cntctfrmmlt_id_form'] ) && $cntctfrm_options = get_option( 'cntctfrmmlt_options_' . $_SESSION['cntctfrmmlt_id_form'] ) ) {
				/**/
			} else {
				$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
			}
		} else {
			$cntctfrm_options = get_option( 'cntctfrm_options' );
		}

		$lang = $is_ajax ? $_REQUEST['lang'] : $_REQUEST['cntctfrm_delete_button'];

		if ( $key = array_search( $lang, $cntctfrm_options['language'] ) !== false )
			$cntctfrm_options['language'] = array_diff( $cntctfrm_options['language'], array( $lang ) );
		if ( isset( $cntctfrm_options['name_label'][ $lang ] ) )
			unset( $cntctfrm_options['name_label'][ $lang ] );
		if ( isset( $cntctfrm_options['address_label'][ $lang ] ) )
			unset( $cntctfrm_options['address_label'][ $lang ] );
		if ( isset( $cntctfrm_options['email_label'][ $lang ] ) )
			unset( $cntctfrm_options['email_label'][ $lang ] );
		if ( isset( $cntctfrm_options['phone_label'][ $lang ] ) )
			unset( $cntctfrm_options['phone_label'][ $lang ] );
		if ( isset( $cntctfrm_options['subject_label'][ $lang ] ) )
			unset( $cntctfrm_options['subject_label'][ $lang ] );
		if ( isset( $cntctfrm_options['message_label'][ $lang ] ) )
			unset( $cntctfrm_options['message_label'][ $lang ] );
		if ( isset( $cntctfrm_options['attachment_label'][ $lang ] ) )
			unset( $cntctfrm_options['attachment_label'][ $lang ] );
		if ( isset( $cntctfrm_options['attachment_tooltip'][ $lang ] ) )
			unset( $cntctfrm_options['attachment_tooltip'][ $lang ] );
		if ( isset( $cntctfrm_options['send_copy_label'][ $lang ] ) )
			unset( $cntctfrm_options['send_copy_label'][ $lang ] );
		if ( isset( $cntctfrm_options['thank_text'][ $lang ] ) )
			unset( $cntctfrm_options['thank_text'][ $lang ] );
		if ( isset( $cntctfrm_options['submit_label'][ $lang ] ) )
			unset( $cntctfrm_options['submit_label'][ $lang ] );
		if ( isset( $cntctfrm_options['name_error'][ $lang ] ) )
			unset( $cntctfrm_options['name_error'][ $lang ] );
		if ( isset( $cntctfrm_options['address_error'][ $lang ] ) )
			unset( $cntctfrm_options['address_error'][ $lang ] );
		if ( isset( $cntctfrm_options['email_error'][ $lang ] ) )
			unset( $cntctfrm_options['email_error'][ $lang ] );
		if ( isset( $cntctfrm_options['phone_error'][ $lang ] ) )
			unset( $cntctfrm_options['phone_error'][ $lang ] );
		if ( isset( $cntctfrm_options['subject_error'][ $lang ] ) )
			unset( $cntctfrm_options['subject_error'][ $lang ] );
		if ( isset( $cntctfrm_options['message_error'][ $lang ] ) )
			unset( $cntctfrm_options['message_error'][ $lang ] );
		if ( isset( $cntctfrm_options['attachment_error'][ $lang ] ) )
			unset( $cntctfrm_options['attachment_error'][ $lang ] );
		if ( isset( $cntctfrm_options['attachment_upload_error'][ $lang ] ) )
			unset( $cntctfrm_options['attachment_upload_error'][ $lang ] );
		if ( isset( $cntctfrm_options['attachment_move_error'][ $lang ] ) )
			unset( $cntctfrm_options['attachment_move_error'][ $lang ] );
		if ( isset( $cntctfrm_options['attachment_size_error'][ $lang ] ) )
			unset( $cntctfrm_options['attachment_size_error'][ $lang ] );
		if ( isset( $cntctfrm_options['captcha_error'][ $lang ] ) )
			unset( $cntctfrm_options['captcha_error'][ $lang ] );
		if ( isset( $cntctfrm_options['form_error'][ $lang ] ) )
			unset( $cntctfrm_options['form_error'][ $lang ] );

		if ( 'pro' == $contact_form_multi_active && $options_main = get_option( 'cntctfrmmltpr_options_main' ) ) {
			update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
		} elseif ( $contact_form_multi_active ) {
			$options_main = get_option( 'cntctfrmmlt_options_main' );
			update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $cntctfrm_options );
		} else {
			update_option( 'cntctfrm_options', $cntctfrm_options );
		}

		if ( $is_alax )
			die();
	}
}

if ( ! function_exists ( 'cntctfrm_plugin_banner' ) ) {
	function cntctfrm_plugin_banner() {
		global $hook_suffix;
		if ( 'plugins.php' == $hook_suffix || ( isset( $_REQUEST['page'] ) && 'contact_form.php' == $_REQUEST['page'] ) ) {
			global $cntctfrm_plugin_info, $wp_version, $bstwbsftwppdtplgns_cookie_add, $bstwbsftwppdtplgns_banner_array;

			if ( 'plugins.php' == $hook_suffix ) {
				$cntctfrm_options = get_option( 'cntctfrm_options' );
				if ( isset( $cntctfrm_options['first_install'] ) && strtotime( '-1 week' ) > $cntctfrm_options['first_install'] ) {
					bws_plugin_banner( $cntctfrm_plugin_info, 'cntctfrm', 'contact-form', 'f575dc39cba54a9de88df346eed52101', '77', '//ps.w.org/contact-form-plugin/assets/icon-128x128.png' );
				}
				bws_plugin_banner_to_settings( $cntctfrm_plugin_info, 'cntctfrm_options', 'contact-form-plugin', 'admin.php?page=contact_form.php' );
			}

			if ( isset( $_REQUEST['page'] ) && 'contact_form.php' == $_REQUEST['page'] ) {
				bws_plugin_suggest_feature_banner( $cntctfrm_plugin_info, 'cntctfrm_options', 'contact-form-plugin' );
			}

			/**
			 * @since 4.0.3
			 * @todo delete after 01.04.2017
			 */
			echo cntctfrm_display_deprecated_shortcode_message();

			if ( empty( $bstwbsftwppdtplgns_banner_array ) )
				bws_get_banner_array();

			if ( ! function_exists( 'is_plugin_active' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$all_plugins = get_plugins();
			$this_banner_prefix = 'cntctfrm_for_ctfrmtdb';
			$this_banner = 'cntctfrm_for_ctfrmtdb_hide_banner_on_plugin_page';
			foreach ( $bstwbsftwppdtplgns_banner_array as $key => $value ) {
				if ( $this_banner == $value[0] ) {
					if ( ! isset( $bstwbsftwppdtplgns_cookie_add ) ) {
						echo '<script type="text/javascript" src="' . plugins_url( '/bws_menu/js/c_o_o_k_i_e.js', __FILE__ ) . '"></script>';
						$bstwbsftwppdtplgns_cookie_add = true;
					} ?>
					<script type="text/javascript">
						(function($) {
							$(document).ready( function() {
								var hide_message = $.cookie( '<?php echo $this_banner_prefix; ?>_hide_banner_on_plugin_page' );
								if ( hide_message == "true" ) {
									$( ".<?php echo $this_banner_prefix; ?>_message" ).css( "display", "none" );
								} else {
									$( ".<?php echo $this_banner_prefix; ?>_message" ).css( "display", "block" );
								};
								$( ".<?php echo $this_banner_prefix; ?>_close_icon" ).click( function() {
									$( ".<?php echo $this_banner_prefix; ?>_message" ).css( "display", "none" );
									$.cookie( "<?php echo $this_banner_prefix; ?>_hide_banner_on_plugin_page", "true", { expires: 32 } );
								});
							});
						})(jQuery);
					</script>
					<?php if ( ! array_key_exists( 'contact-form-to-db/contact_form_to_db.php', $all_plugins ) && ! array_key_exists( 'contact-form-to-db-pro/contact_form_to_db_pro.php', $all_plugins ) ) { ?>
						<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
							<div class="cntctfrm_for_ctfrmtdb_message bws_banner_on_plugin_page" style="display: none;">
								<button class="<?php echo $this_banner_prefix; ?>_close_icon close_icon notice-dismiss bws_hide_settings_notice" title="<?php _e( 'Close notice', 'contact-form-plugin' ); ?>"></button>
								<div class="icon">
									<img title="" src="//ps.w.org/contact-form-to-db/assets/icon-128x128.png" alt="" />
								</div>
								<div class="text">
									<strong>Contact Form to DB</strong> <?php _e( "allows to store your messages to the database.", 'contact-form-plugin' ); ?><br />
									<span><?php _e( "Manage messages that have been sent from your website.", 'contact-form-plugin' ); ?></span>
								</div>
								<div class="button_div">
									<a class="button" target="_blank" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form-to-db/?k=6ebf0743736411607343ad391dc3b436&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>"><?php _e( 'Learn More', 'contact-form-plugin' ); ?></a>
								</div>
							</div>
						</div>
					<?php }
					break;
				}
				if ( isset( $all_plugins[ $value[1] ] ) && $all_plugins[ $value[1] ]["Version"] >= $value[2] && is_plugin_active( $value[1] ) && ! isset( $_COOKIE[ $value[0] ] ) ) {
					break;
				}
			}
		}
	}
}

if ( ! function_exists( 'cntctfrm_shortcode_button_content' ) ) {
	function cntctfrm_shortcode_button_content( $content ) {
		global $wp_version, $cntctfrm_lang_codes;
		$lang_default = '...';

		/* Check contact-form-multi plugin */
		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( $contact_form_multi_active ) {
			if ( 'pro' == $contact_form_multi_active && $multi_options_main = get_option( 'cntctfrmmltpr_options_main' ) ) {
				/**/
			} else {
				$multi_options_main = get_option( 'cntctfrmmlt_options_main' );
			}

			if ( ! $multi_options_main ) {
				$contact_form_multi_active = false;
			} else {
				if ( $multi_options_main['name_id_form'] ) {
					$multi_forms = $multi_ids = $multi_forms_languages = array();
					foreach ( $multi_options_main['name_id_form'] as $id => $title ) {
						$multi_forms[ $id ] = $title;
						array_push( $multi_ids, $id );
						$multi_options = get_option( 'cntctfrmmlt_options_' . $id );
						$language = isset( $multi_options['language'] ) ? $multi_options['language'] : array();
						array_unshift( $language, 'default' );
						$multi_forms_languages[ $id ] = $language;
					}
				}
			}
		}

		if ( ! $contact_form_multi_active ) {
			$options = get_option( 'cntctfrm_options' );
			array_unshift( $options['language'], 'default' );
		} ?>
		<div id="cntctfrm" style="display:none;">
			<fieldset>
				<?php if ( $contact_form_multi_active ) { ?>
					<label>
						<select name="cntctfrm_forms_list" id="cntctfrm_forms_list">
							<?php foreach ( $multi_forms as $id => $title ) {
									printf( '<option value="%1$s">%2$s</option>', $id, $title );
							} ?>
						</select>
						<span class="title"><?php _e( 'Contact form', 'contact-form-plugin' ); ?></span>
					</label>
					<br>
					<label>
						<select name="cntctfrm_multi_languages_list" id="cntctfrm_multi_languages_list">
							<?php $i = 1;
							foreach ( $multi_forms_languages as $id => $languages ) {
								foreach ( $languages as $language ) {
									printf( '<option value="%1$s" data-form-id="%2$s" %3$s>%4$s</option>', strtolower( $language ), $id, ( $i > 1 ) ? 'style="display: none;"' : '', ( $language == 'default' ) ? $lang_default : $cntctfrm_lang_codes[ $language ] );
								}
								$i++;
							} ?>
						</select>
						<span class="title"><?php _e( 'Language', 'contact-form-plugin' ); ?></span>
					</label>
					<input class="bws_default_shortcode" type="hidden" name="default" value="[bestwebsoft_contact_form id=<?php echo array_shift( $multi_ids ); ?>]" />
				<?php } else { ?>
					<label>
						<select name="cntctfrm_languages_list" id="cntctfrm_languages_list">
							<?php foreach ( $options['language'] as $language ) {
									printf( '<option value="%1$s">%2$s</option>', strtolower( $language ), ( $language == 'default' ) ? $lang_default : $cntctfrm_lang_codes[ $language ] );
							} ?>
						</select>
						<span class="title"><?php _e( 'Language', 'contact-form-plugin' ); ?></span>
					</label>
					<input class="bws_default_shortcode" type="hidden" name="default" value="[bestwebsoft_contact_form]" />
				<?php } ?>
			</fieldset>
			<script type="text/javascript">
				function cntctfrm_shortcode_init() {
					(function($) {
						var current_object = '<?php echo ( $wp_version < 3.9 ) ? "#TB_ajaxContent" : ".mce-reset" ?>';
						<?php if ( $contact_form_multi_active ) { ?>
							$( current_object + ' #bws_shortcode_display' ).bind( 'display_shortcode', function() {
								var cntctfrm_form_id = $( current_object + ' #cntctfrm_forms_list option:selected' ).val(),
									cntctfrm_get_form_language = $( current_object + ' #cntctfrm_multi_languages_list option:selected' ).val(),
									cntctfrm_form_language = ( cntctfrm_get_form_language == 'default' ) ? '' : ' lang=' + cntctfrm_get_form_language,
									shortcode = '[bestwebsoft_contact_form' + cntctfrm_form_language + ' id=' + cntctfrm_form_id + ']';
								$( this ).text( shortcode );
							});
							$( current_object + ' #cntctfrm_forms_list' ).on( 'change', function() {
								var cntctfrm_form = $( this ).find( 'option:selected' ).val(),
									cntctfrm_languages = $( current_object + ' #cntctfrm_multi_languages_list' ),
									cntctfrm_languages_options = cntctfrm_languages.find( 'option' );
								cntctfrm_languages_options.hide();
								cntctfrm_languages_options.filter( '[data-form-id="' + cntctfrm_form + '"]' ).show();
								cntctfrm_languages_options.filter( '[value="default"]' ).attr( 'selected', true );
								$( current_object + ' #bws_shortcode_display' ).trigger( 'display_shortcode' );
							});
							$( current_object + ' #cntctfrm_multi_languages_list' ).on( 'change', function() {
								$( current_object + ' #bws_shortcode_display' ).trigger( 'display_shortcode' );
							});
						<?php } else { ?>
							$( current_object + ' #cntctfrm_languages_list' ).on( 'change', function() {
								var cntctfrm_get_language = $( current_object + ' #cntctfrm_languages_list option:selected' ).val(),
									cntctfrm_language = ( cntctfrm_get_language == 'default' ) ? '' : ' lang=' + cntctfrm_get_language,
									shortcode = '[bestwebsoft_contact_form' + cntctfrm_language + ']';
								$( current_object + ' #bws_shortcode_display' ).text( shortcode );
							});
						<?php } ?>
					})(jQuery);
				}
			</script>
			<div class="clear"></div>
		</div>
		<?php return $content;
	}
}

/* add help tab  */
if ( ! function_exists( 'cntctfrm_add_tabs' ) ) {
	function cntctfrm_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id' 			=> 'cntctfrm',
			'section' 		=> '200538909'
		);
		bws_help_tab( $screen, $args );
	}
}

/* Function for delete options */
if ( ! function_exists ( 'cntctfrm_delete_options' ) ) {
	function cntctfrm_delete_options() {
		global $wpdb;
		$all_plugins = get_plugins();

		if ( ! array_key_exists( 'contact-form-pro/contact_form_pro.php', $all_plugins ) ) {
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				$old_blog = $wpdb->blogid;
				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					delete_option( 'cntctfrm_options' );
					$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->prefix . "cntctfrm_field`;" );
				}
				switch_to_blog( $old_blog );
			} else {
				delete_option( 'cntctfrm_options' );
				$wpdb->query( "DROP TABLE IF EXISTS `" . $wpdb->prefix . "cntctfrm_field`;" );
			}
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

register_activation_hook( __FILE__, 'cntctfrm_activation' );

add_action( 'admin_menu', 'cntctfrm_admin_menu' );

add_action( 'init', 'cntctfrm_init', 50 );
add_action( 'admin_init', 'cntctfrm_admin_init' );
add_action( 'plugins_loaded', 'cntctfrm_plugins_loaded' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'cntctfrm_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'cntctfrm_register_plugin_links', 10, 2 );

add_action( 'admin_enqueue_scripts', 'cntctfrm_admin_head' );
add_action( 'wp_enqueue_scripts', 'cntctfrm_wp_enqueue_style' );
add_action( 'wp_footer', 'cntctfrm_wp_footer' );

add_shortcode( 'bws_contact_form', 'cntctfrm_display_form' );
add_shortcode( 'bestwebsoft_contact_form', 'cntctfrm_display_form' );
add_filter( 'widget_text', 'do_shortcode' );

/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'cntctfrm_shortcode_button_content' );
add_action( 'wp_ajax_cntctfrm_add_language', 'cntctfrm_add_language' );
add_action( 'wp_ajax_cntctfrm_remove_language', 'cntctfrm_remove_language' );

add_action( 'admin_notices', 'cntctfrm_plugin_banner');

register_uninstall_hook( __FILE__, 'cntctfrm_delete_options' );