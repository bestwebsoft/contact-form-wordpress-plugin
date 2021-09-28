<?php
/*
Plugin Name: Contact Form by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/contact-form/
Description: Simple contact form plugin any WordPress website must have.
Author: BestWebSoft
Text Domain: contact-form-plugin
Domain Path: /languages
Version: 4.2.4
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
*/

/*  @ Copyright 2021 BestWebSoft  ( https://support.bestwebsoft.com )

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
* Add Wordpress page 'bws_panel' and sub-page of this plugin to admin-panel.
* @return void
*/
if ( ! function_exists( 'cntctfrm_admin_menu' ) ) {
	function cntctfrm_admin_menu() {
		global $submenu, $cntctfrm_plugin_info, $wp_version;
		if ( ! is_plugin_active( 'contact-form-pro/contact_form_pro.php')/*pls */ && ! is_plugin_active( 'contact-form-plus/contact-form-plus.php' )/* pls*/ ) {
			$cntctfrm_settings = add_menu_page(
				esc_html__( 'Contact Form Settings', 'contact-form-plugin' ), /* $page_title */
				'Contact Form', /* $menu_title */
				'manage_options', /* $capability */
				'contact_form.php', /* $menu_slug */
				'cntctfrm_settings_page', /* $callable_function */
                'none'
			);
			add_submenu_page(
				'contact_form.php',
                esc_html__( 'Contact Form Settings', 'contact-form-plugin' ),
                esc_html__( 'Settings', 'contact-form-plugin' ),
				'manage_options',
				'contact_form.php',
				'cntctfrm_settings_page'
			);
			if ( isset( $submenu['contact_form.php'] ) && ( is_plugin_active( 'contact-form-to-db/contact_form_to_db.php' ) || is_plugin_active( 'contact-form-to-db-pro/contact_form_to_db_pro.php' ) ) ) {
				$submenu['contact_form.php'][] = array(
					'CF to DB',
					'manage_options',
					admin_url( 'admin.php?page=cntctfrmtdb_manager' )
				);
			}
			add_submenu_page(
				'contact_form.php',
				'BWS Panel',
				'BWS Panel',
				'manage_options',
				'cntctfrm-bws-panel',
				'bws_add_menu_render'
			);
            /*pls   */
			if ( isset( $submenu['contact_form.php'] ) )
				$submenu['contact_form.php'][] = array(
					'<span style="color:#d86463"> ' . esc_html__( 'Upgrade to Pro', 'contact-form-plugin' ) . '</span>',
					'manage_options',
					'https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=697c5e74f39779ce77850e11dbe21962&pn=77&v=' . $cntctfrm_plugin_info["Version"] . '&wp_v=' . $wp_version );
            /*  pls*/

			add_action( "load-{$cntctfrm_settings}", 'cntctfrm_add_tabs' );
        }
	}
}

if ( ! function_exists ( 'cntctfrm_init' ) ) {
	function cntctfrm_init() {
		global $cntctfrm_plugin_info, $cntctfrm_result, $cntctfrm_form_count;

        $form_submited = isset( $_POST['cntctfrm_form_submited'] ) ? sanitize_key( $_POST['cntctfrm_form_submited'] ) : 0;
        if ( true === $cntctfrm_result && $cntctfrm_form_count == $form_submited )
            setcookie( 'cntctfrm_send_mail', true );
        if ( true !== $cntctfrm_result || $cntctfrm_form_count != $form_submited )
            setcookie( 'cntctfrm_send_mail', false);

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $cntctfrm_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$cntctfrm_plugin_info = get_plugin_data( __FILE__ );
		}
		/* Function check if plugin is compatible with current WP version  */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $cntctfrm_plugin_info, '4.5' );

		cntctfrm_check_and_send();
	}
}

if ( ! function_exists ( 'cntctfrm_admin_init' ) ) {
	function cntctfrm_admin_init() {
		global $bws_plugin_info, $cntctfrm_plugin_info, $bws_shortcode_list, $cntctfrm_lang_codes, $pagenow, $cntctfrm_options;
		/* Add variable for bws_menu */

		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '77', 'version' => $cntctfrm_plugin_info["Version"] );

        do_action( 'cntctfrm_new_object_custom_field');

		/* Display form on the setting page */
		$cntctfrm_lang_codes = array(
			'ab' => 'Abkhazian',
            'aa' => 'Afar',
            'af' => 'Afrikaans',
            'ak' => 'Akan',
            'sq' => 'Albanian',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'an' => 'Aragonese',
            'hy' => 'Armenian',
            'as' => 'Assamese',
            'av' => 'Avaric',
            'ae' => 'Avestan',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
			'bm' => 'Bambara',
            'ba' => 'Bashkir',
            'eu' => 'Basque',
            'be' => 'Belarusian',
            'bn' => 'Bengali',
            'bh' => 'Bihari',
            'bi' => 'Bislama',
            'bs' => 'Bosnian',
            'br' => 'Breton',
            'bg' => 'Bulgarian',
            'my' => 'Burmese',
			'ca' => 'Catalan; Valencian',
            'ch' => 'Chamorro',
            'ce' => 'Chechen',
            'ny' => 'Chichewa; Chewa; Nyanja',
            'zh' => 'Chinese',
            'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic',
            'cv' => 'Chuvash',
            'km' => 'Central Khmer',
            'kw' => 'Cornish',
            'co' => 'Corsican',
            'cr' => 'Cree',
            'hr' => 'Croatian',
            'cs' => 'Czech',
			'da' => 'Danish',
            'dv' => 'Divehi; Dhivehi; Maldivian',
            'nl' => 'Dutch; Flemish',
            'dz' => 'Dzongkha',
			'en' => 'English',
            'eo' => 'Esperanto',
            'et' => 'Estonian',
            'ee' => 'Ewe',
			'fo' => 'Faroese',
            'fj' => 'Fijjian',
            'fi' => 'Finnish',
            'fr' => 'French',
            'ff' => 'Fulah',
			'gd' => 'Gaelic; Scottish Gaelic',
            'gl' => 'Galician',
            'lg' => 'Ganda',
            'ka' => 'Georgian',
            'de' => 'German',
            'el' => 'Greek, Modern',
            'gn' => 'Guarani',
            'gu' => 'Gujarati',
			'ht' => 'Haitian; Haitian Creole',
            'ha' => 'Hausa',
            'he' => 'Hebrew',
            'hz' => 'Herero',
            'hi' => 'Hindi',
            'ho' => 'Hiri Motu',
            'hu' => 'Hungarian',
			'is' => 'Icelandic',
            'io' => 'Ido',
            'ig' => 'Igbo',
            'id' => 'Indonesian',
            'ie' => 'Interlingue',
            'ia' => 'Interlingua (International Auxiliary Language Association)',
            'iu' => 'Inuktitut',
            'ik' => 'Inupiaq',
            'ga' => 'Irish',
            'it' => 'Italian',
			'ja' => 'Japanese',
            'jv' => 'Javanese',
			'kl' => 'Kalaallisut; Greenlandic',
            'kn' => 'Kannada',
            'kr' => 'Kanuri',
            'ks' => 'Kashmiri',
            'kk' => 'Kazakh',
            'ki' => 'Kikuyu; Gikuyu',
            'rw' => 'Kinyarwanda',
            'ky' => 'Kirghiz; Kyrgyz',
            'kv' => 'Komi',
            'kg' => 'Kongo',
            'ko' => 'Korean',
            'kj' => 'Kuanyama; Kwanyama',
            'ku' => 'Kurdish',
			'lo' => 'Lao',
            'la' => 'Latin',
            'lv' => 'Latvian',
            'li' => 'Limburgan; Limburger; Limburgish',
            'ln' => 'Lingala',
            'lt' => 'Lithuanian',
            'lu' => 'Luba-Katanga',
            'lb' => 'Luxembourgish; Letzeburgesch',
			'mk' => 'Macedonian',
            'mg' => 'Malagasy',
            'ms' => 'Malay',
            'ml' => 'Malayalam',
            'mt' => 'Maltese',
            'gv' => 'Manx',
            'mi' => 'Maori',
            'mr' => 'Marathi',
            'mh' => 'Marshallese',
            'mo' => 'Moldavian',
            'mn' => 'Mongolian',
			'na' => 'Nauru',
            'nv' => 'Navajo; Navaho',
            'nr' => 'Ndebele, South; South Ndebele',
            'nd' => 'Ndebele, North; North Ndebele',
            'ng' => 'Ndonga',
            'ne' => 'Nepali',
            'se' => 'Northern Sami',
            'no' => 'Norwegian',
            'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian',
            'nb' => 'Norwegian Bokmål; Bokmål, Norwegian',
			'oc' => 'Occitan, Provençal',
            'oj' => 'Ojibwa',
            'or' => 'Oriya',
            'om' => 'Oromo',
            'os' => 'Ossetian; Ossetic',
			'pi' => 'Pali',
            'pa' => 'Panjabi; Punjabi',
            'fa' => 'Persian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'ps' => 'Pushto',
			'qu' => 'Quechua',
			'ro' => 'Romanian',
            'rm' => 'Romansh',
            'rn' => 'Rundi',
            'ru' => 'Russian',
			'sm' => 'Samoan',
            'sg' => 'Sango',
            'sa' => 'Sanskrit',
            'sc' => 'Sardinian',
            'sr' => 'Serbian',
            'sn' => 'Shona',
            'ii' => 'Sichuan Yi',
            'sd' => 'Sindhi',
            'si' => 'Sinhala; Sinhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'so' => 'Somali',
            'st' => 'Sotho, Southern',
            'es' => 'Spanish; Castilian',
            'su' => 'Sundanese',
            'sw' => 'Swahili',
            'ss' => 'Swati',
            'sv' => 'Swedish',
			'tl' => 'Tagalog',
            'ty' => 'Tahitian',
            'tg' => 'Tajik',
            'ta' => 'Tamil',
            'tt' => 'Tatar',
            'te' => 'Telugu',
            'th' => 'Thai',
            'bo' => 'Tibetan',
            'ti' => 'Tigrinya',
            'to' => 'Tonga (Tonga Islands)',
            'ts' => 'Tsonga',
            'tn' => 'Tswana',
            'tr' => 'Turkish',
            'tk' => 'Turkmen',
            'tw' => 'Twi',
			'ug' => 'Uighur; Uyghur',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
			've' => 'Venda',
            'vi' => 'Vietnamese',
            'vo' => 'Volapük',
			'wa' => 'Walloon',
            'cy' => 'Welsh',
            'fy' => 'Western Frisian',
            'wo' => 'Wolof',
			'xh' => 'Xhosa',
			'yi' => 'Yiddish',
            'yo' => 'Yoruba',
			'za' => 'Zhuang; Chuang',
            'zu' => 'Zulu',
		);

		/* Call register settings function */
		if ( isset( $_REQUEST['page'] ) && 'contact_form.php' == $_REQUEST['page'] )
			cntctfrm_settings();
        /*pls   */
        if ( 'plugins.php' == $pagenow ) {
            /* Install the option defaults */
            if ( function_exists( 'bws_plugin_banner_go_pro' ) ) {
                cntctfrm_settings();
                bws_plugin_banner_go_pro( $cntctfrm_options, $cntctfrm_plugin_info, 'cntctfrm', 'contact-form-plugin', 'f575dc39cba54a9de88df346eed52101', '77', 'contact-form-plugin' );
            }
        }
        /*  pls*/
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
		$db_version = '4.1.1';

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

				/* array_merge */
				$cntctfrmmlt_options = array_merge( $option_defaults, $cntctfrmmlt_options );
				foreach ( $option_defaults as $key => $value ) {
					if ( is_array( $value ) ) {
						foreach ( $value as $key2 => $value2 ) {
							if ( ! isset( $cntctfrmmlt_options[ $key ][ $key2 ] ) )
								$cntctfrmmlt_options[ $key ][ $key2 ] = $option_defaults[ $key ][ $key2 ];
						}
					}
				}

				$cntctfrmmlt_options['plugin_option_version'] = $cntctfrm_plugin_info["Version"];
				update_option( 'cntctfrmmlt_options', $cntctfrmmlt_options );
			}

			/* Get options from the database */
			if ( isset( $GLOBALS['cntctfrmmlt_id_form'] ) || $form_id ) {
				$id = ( $form_id ) ? $form_id : absint( $GLOBALS['cntctfrmmlt_id_form'] );
				if ( $cntctfrm_options = get_option( 'cntctfrmmlt_options_' . $id ) ) {
					$cntctfrm_options = get_option( 'cntctfrmmlt_options_' . $id );
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

			/* array_merge */
			$cntctfrm_options = array_merge( $option_defaults, $cntctfrm_options );
			foreach ( $option_defaults as $key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $key2 => $value2 ) {
						if ( ! isset( $cntctfrm_options[ $key ][ $key2 ] ) )
							$cntctfrm_options[ $key ][ $key2 ] = $option_defaults[ $key ][ $key2 ];
					}
				}
			}
			$cntctfrm_options['plugin_option_version'] = $cntctfrm_plugin_info["Version"];
			/* show pro features */
			$cntctfrm_options['hide_premium_options'] = array();
			if ( $contact_form_multi_active ) {
				if ( isset( $GLOBALS['cntctfrmmlt_id_form'] ) || $form_id ) {
					$id = ( $form_id ) ? $form_id : absint( $GLOBALS['cntctfrmmlt_id_form'] );
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

			if ( is_multisite() ) {
				switch_to_blog( 1 );
				register_uninstall_hook( __FILE__, 'cntctfrm_delete_options' );
				restore_current_blog();
			} else {
				register_uninstall_hook( __FILE__, 'cntctfrm_delete_options' );
			}
		}

		/* Create db table of fields list */
		if ( ! isset( $cntctfrm_options['plugin_db_version'] ) || $cntctfrm_options['plugin_db_version'] != $db_version ) {
			cntctfrm_db_create();
			$cntctfrm_options['plugin_db_version'] = $db_version;
			if ( $contact_form_multi_active ) {
				if ( isset( $GLOBALS['cntctfrmmlt_id_form'] ) || $form_id ) {
					$id = ( $form_id ) ? $form_id : absint( $GLOBALS['cntctfrmmlt_id_form'] );
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

if ( ! function_exists( 'cntctfrm_get_first_form_id' ) ) {
	function cntctfrm_get_first_form_id() {
		$multi_options_main = get_option( 'cntctfrmmlt_options_main' );
		$first_form_id = key( $multi_options_main['name_id_form'] );
		if ( empty( $multi_options_main ) || empty( $multi_options_main['name_id_form'] ) ) {
			return false;
		} else {
			return $first_form_id;
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
			'plugin_option_version'     => $cntctfrm_plugin_info["Version"],
			'display_settings_notice'   =>  1,
			'first_install'             =>  strtotime( "now" ),
			'suggest_feature_banner'    =>  1,
			'user_email'                => 'admin',
			'custom_email'              => get_option("admin_email"),
			'select_email'              => 'custom',
			'from_email'                => 'custom',
			'custom_from_email'         => $from_email,
			'attachment'                => 0,
			'attachment_explanations'   => 1,
			'send_copy'                 => 0,
			'gdpr'                      => 0,
			'from_field'                => get_bloginfo( 'name' ),
			'select_from_field'         => 'custom',
			'display_name_field'        => 1,
			'display_address_field'     => 0,
			'display_phone_field'       => 0,
			'required_name_field'       => 1,
			'required_address_field'    => 0,
			'required_email_field'      => 1,
			'required_phone_field'      => 0,
			'required_subject_field'    => 1,
			'required_message_field'    => 1,
			'required_symbol'           => '*',
			'display_add_info'          => 1,
			'display_sent_from'         => 1,
			'display_date_time'         => 1,
			'mail_method'               => 'wp-mail',
			'display_coming_from'       => 1,
			'display_user_agent'        => 1,
			'language'                  => array(),
			'change_label'              => 0,
			'gdpr_link'                 => '',
			'name_label'                => array( 'default' => esc_html__( "Name", 'contact-form-plugin' ) . ':' ),
			'address_label'             => array( 'default' => esc_html__( "Address", 'contact-form-plugin' ) . ':' ),
			'email_label'               => array( 'default' => esc_html__( "Email Address", 'contact-form-plugin' ) . ':' ),
			'phone_label'               => array( 'default' => esc_html__( "Phone number", 'contact-form-plugin' ) . ':' ),
			'subject_label'             => array( 'default' => esc_html__( "Subject", 'contact-form-plugin' ) . ':' ),
			'message_label'             => array( 'default' => esc_html__( "Message", 'contact-form-plugin' ) . ':' ),
			'attachment_label'          => array( 'default' => esc_html__( "Attachment", 'contact-form-plugin' ) . ':' ),
			'attachment_tooltip'        => array( 'default' => esc_html__( "Supported file types: HTML, TXT, CSS, GIF, PNG, JPEG, JPG, TIFF, BMP, AI, EPS, PS, CSV, RTF, PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR, WAV, MP3, PPT.", 'contact-form-plugin' ) ),
			'send_copy_label'           => array( 'default' => esc_html__( "Send me a copy", 'contact-form-plugin' ) ),
			'gdpr_label'                => array( 'default' => esc_html__( "I consent to having this site collect my personal data.", 'contact-form-plugin' ) ),
			'gdpr_text_button'			=> array( 'default' => esc_html__( "Learn more", 'contact-form-plugin' ) ),
			'submit_label'              => array( 'default' => esc_html__( "Submit", 'contact-form-plugin' ) ),
			'name_error'                => array( 'default' => esc_html__( "Your name is required.", 'contact-form-plugin' ) ),
			'address_error'             => array( 'default' => esc_html__( "Address is required.", 'contact-form-plugin' ) ),
			'email_error'               => array( 'default' => esc_html__( "A valid email address is required.", 'contact-form-plugin' ) ),
			'phone_error'               => array( 'default' => esc_html__( "Phone number is required.", 'contact-form-plugin' ) ),
			'subject_error'             => array( 'default' => esc_html__( "Subject is required.", 'contact-form-plugin' ) ),
			'message_error'             => array( 'default' => esc_html__( "Message text is required.", 'contact-form-plugin' ) ),
			'attachment_error'          => array( 'default' => esc_html__( "File format is not valid.", 'contact-form-plugin' ) ),
			'attachment_upload_error'   => array( 'default' => esc_html__( "File upload error.", 'contact-form-plugin' ) ),
			'attachment_move_error'     => array( 'default' => esc_html__( "The file could not be uploaded.", 'contact-form-plugin' ) ),
			'attachment_size_error'     => array( 'default' => esc_html__( "This file is too large.", 'contact-form-plugin' ) ),
			'captcha_error'             => array( 'default' => esc_html__( "Please fill out the CAPTCHA.", 'contact-form-plugin' ) ),
			'form_error'                => array( 'default' => esc_html__( "Please make corrections below and try again.", 'contact-form-plugin' ) ),
			'action_after_send'         => 1,
			'thank_text'                => array( 'default' => esc_html__( "Thank you for contacting us.", 'contact-form-plugin' ) ),
			'redirect_url'              => '',
			'delete_attached_file'      => '0',
			'html_email'                => 1,
			'change_label_in_email'     => 0,
			'layout'                    => 1,
			'submit_position'           => 'left',
			'order_fields'              => array(
					'first_column'  => array(
						'cntctfrm_contact_name',
						'cntctfrm_contact_address',
						'cntctfrm_contact_email',
						'cntctfrm_contact_phone',
						'cntctfrm_contact_subject',
						'cntctfrm_contact_message',
						'cntctfrm_contact_gdpr',
						'cntctfrm_contact_attachment',
						'cntctfrm_contact_send_copy',
						'cntctfrm_subscribe',
						'cntctfrm_captcha'
					),
					'second_column' => array()
				),
			'width'                 => array(
				'type'          => 'default',
				'input_value'   => '100',
				'input_unit'    => '%'
			),
            'active_multi_attachment' => 0
		);
        $option_defaults = apply_filters( 'cntctfrm_get_additional_options_default', $option_defaults );

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
        $wpdb->query( $sql );
		$fields = array(
			'name',
			'email',
			'subject',
			'message',
			'gdpr',
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
			$db_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "cntctfrm_field WHERE `name` = %s'", $value ), ARRAY_A );
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
		if ( is_multisite() ) {
			switch_to_blog( 1 );
			register_uninstall_hook( __FILE__, 'cntctfrm_delete_options' );
			restore_current_blog();
		} else {
			register_uninstall_hook( __FILE__, 'cntctfrm_delete_options' );
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
		$free_active = is_plugin_active( 'captcha-bws/captcha-bws.php' );
		$plus_active = is_plugin_active( 'captcha-plus/captcha-plus.php' );
		$pro_active = is_plugin_active( 'captcha-pro/captcha_pro.php' );
		if ( $free_active || $plus_active || $pro_active ) {
			$cptch_options = get_option( 'cptch_options' );

			if ( $free_active )
				$settings_page = 'captcha.php';
			elseif ( $pro_active )
				$settings_page = 'captcha_pro.php';
			else
				$settings_page = 'captcha-plus.php';

			$cntctfrm_related_plugins['captcha'] = array(
				'options'           => $cptch_options,
				'settings_page'     => $settings_page
			);
		}

		/* Get Google Captcha options */
		if ( is_plugin_active( 'google-captcha/google-captcha.php' ) || is_plugin_active( 'google-captcha-pro/google-captcha-pro.php' ) || is_plugin_active( 'google-captcha-plus/google-captcha-plus.php' ) ) {
			$gglcptch_options = get_option( 'gglcptch_options' );
			if ( is_plugin_active( 'google-captcha/google-captcha.php' ) ) {
				$settings_page = 'google-captcha.php';
            } elseif ( is_plugin_active( 'google-captcha-pro/google-captcha-pro.php' ) ) {
				$settings_page = 'google-captcha-pro.php';
            } else {
				$settings_page = 'google-captcha-plus.php';
            }

			$cntctfrm_related_plugins['google-captcha'] = array(
				'options'           => $gglcptch_options,
				'settings_page'     => $settings_page
			);
		}

		/* Get Subscriber options */
		if ( is_multisite() ) {
			if ( is_plugin_active_for_network( 'subscriber/subscriber.php' ) || is_plugin_active_for_network( 'subscriber-pro/subscriber-pro.php' ) ) {
				$sbscrbr_options = get_site_option( 'sbscrbr_options' );
				$settings_page = is_plugin_active_for_network( 'subscriber/subscriber.php' ) ? 'sbscrbr_settings_page' : 'sbscrbrpr_settings_page';

				$cntctfrm_related_plugins['subscriber'] = array(
					'options'           => $sbscrbr_options,
					'settings_page'     => $settings_page
				);
			}
		} else {
			if ( is_plugin_active( 'subscriber/subscriber.php' ) || is_plugin_active( 'subscriber-pro/subscriber-pro.php' ) ) {
				$sbscrbr_options = get_option( 'sbscrbr_options' );
				$settings_page = is_plugin_active( 'subscriber/subscriber.php' ) ? 'sbscrbr_settings_page' : 'sbscrbrpr_settings_page';

				$cntctfrm_related_plugins['subscriber'] = array(
					'options'           => $sbscrbr_options,
					'settings_page'     => $settings_page
				);
			}
		}

		/* Get Contact Form to DB options */
		if ( is_plugin_active( 'contact-form-to-db/contact_form_to_db.php' ) || is_plugin_active( 'contact-form-to-db-pro/contact_form_to_db_pro.php' ) ) {
			$cntctfrmtdb_options = get_option( 'cntctfrmtdb_options' );

			$settings_page = is_plugin_active( 'contact-form-to-db/contact_form_to_db.php' ) ? 'contact_form_to_db.php' : 'contact_form_to_db_pro.php';
			$cntctfrm_related_plugins['contact-form-to-db'] = array(
				'options'           => $cntctfrmtdb_options,
				'settings_page'     => $settings_page
			);
		}
	}
}

if ( ! function_exists( 'cntctfrm_check_cf_multi_active' ) ) {
	function cntctfrm_check_cf_multi_active() {
		/* Check contact-form-multi (free) plugin */
		if ( is_plugin_active( 'contact-form-multi/contact-form-multi.php' ) )
			return "free";

		/* Check contact-form-multi-pro plugin */
		if ( is_plugin_active( 'contact-form-multi-pro/contact-form-multi-pro.php' ) )
			return "pro";

		return false;
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'cntctfrm_get_ordered_fields' ) ) {
	function cntctfrm_get_ordered_fields( $id = false ) {
		global $cntctfrm_options, $cntctfrm_related_plugins;
		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( ! isset( $cntctfrm_options['order_fields'] ) ) {
			cntctfrm_settings();
		}

		if ( empty( $cntctfrm_related_plugins ) ) {
			cntctfrm_related_plugins();
		}

		if ( ! $contact_form_multi_active ) {
			$cntctfrm_options = get_option( 'cntctfrm_options' );
			$display_captcha = $display_google_captcha = $display_subscriber = false;

			if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) )
				$display_captcha = ! empty( $cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] );

			if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) )
				$display_google_captcha = ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] );

			if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) )
				$display_subscriber = ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] );
		} else {
		    if ( empty( $id ) && isset( $GLOBALS['cntctfrmmlt_id_form'] ) ) {
			    $id = $GLOBALS['cntctfrmmlt_id_form'];
            }
			if ( ! empty( $id ) ) {
				if ( get_option( 'cntctfrmmlt_options_' . $id ) ) {
					$cntctfrm_options = get_option( 'cntctfrmmlt_options_' . $id );
				} else {
					$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
				}
			} else {
				$cntctfrm_options = get_option( 'cntctfrmmlt_options' );
			}
			$display_captcha        = ( isset( $cntctfrm_options['display_captcha'] ) && 1 == $cntctfrm_options['display_captcha'] ) ? true : false;
			$display_google_captcha = ( isset( $cntctfrm_options['display_google_captcha'] ) && 1 == $cntctfrm_options['display_google_captcha'] ) ? true : false;
			$display_subscriber     = ( isset( $cntctfrm_options['display_subscribe'] ) && 1 == $cntctfrm_options['display_subscribe'] ) ? true : false;
		}

		$default_order_fields = array(
			'cntctfrm_contact_name'         => ( 1 == $cntctfrm_options['display_name_field'] ) ? true : false,
			'cntctfrm_contact_address'      => ( 1 == $cntctfrm_options['display_address_field'] ) ? true : false,
			'cntctfrm_contact_email'        => true,
			'cntctfrm_contact_phone'        => ( 1 == $cntctfrm_options['display_phone_field'] ) ? true : false,
			'cntctfrm_contact_subject'      => true,
			'cntctfrm_contact_message'      => true,
			'cntctfrm_contact_attachment'   => ( 1 == $cntctfrm_options['attachment'] ) ? true : false,
			'cntctfrm_contact_send_copy'    => ( 1 == $cntctfrm_options['send_copy'] ) ? true : false,
			'cntctfrm_contact_gdpr'         => ( 1 == $cntctfrm_options['gdpr'] ) ? true : false,
			'cntctfrm_subscribe'            => $display_subscriber,
			'cntctfrm_captcha'              => $display_captcha || $display_google_captcha ? true : false,
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

if( ! function_exists( 'cntctfrm_settings_page' ) ) {
	function cntctfrm_settings_page() {
		global $cntctfrm_plugin_info, $wp_version, $cntctfrmmlt_plugin_info, $cntctfrm_related_plugins, $cntctfrm_options;
        if ( ! class_exists( 'Bws_Settings_Tabs' ) )
            require_once( dirname( __FILE__ ) . '/bws_menu/class-bws-settings.php' );
		require_once( dirname( __FILE__ ) . '/includes/class-cntctfrm-settings.php' );
		$page = new Cntctfrm_Settings_Tabs( plugin_basename( __FILE__ ) );
        if ( method_exists( $page, 'add_request_feature' ) )
            $page->add_request_feature(); ?>
		<div class="wrap">
			<h1 class="cntctfrm-title"><?php esc_html_e( 'Contact Form Settings', 'contact-form-plugin' ); ?></h1>
			<?php

			if ( isset( $_POST["cntctfrm_hide_note"] ) && ! isset( $cntctfrm_options['hide_captcha_note'] ) ) {
				$cntctfrm_options['hide_captcha_note'] = 1;
				update_option( 'cntctfrm_options', $cntctfrm_options );
			}
			if ( empty( $cntctfrm_related_plugins ) ) {
				cntctfrm_related_plugins();
			}
			if ( ! array_key_exists( 'captcha', $cntctfrm_related_plugins ) && ! array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) && ! isset( $cntctfrm_options['hide_captcha_note'] ) ) { ?>
                <div class="notice notice-info inline is-dismissible">
                    <form method="post" action="">
                        <button type="submit" name="cntctfrm_hide_note" class="notice-dismiss bws_hide_note" title="Close"></button>
                    </form>
                    <p><?php echo sprintf( __( '<strong>Note:</strong> The contact form is not protected from spam and bots. Use %1$s Captcha by BestWebSoft plugin %3$s or %2$s reCaptcha by BestWebSoft plugin %3$s to protect your contact form.', 'contact-form-plugin' ), '<a href="https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=8820a3dc45ab5d7d493999becb13a0b8" target="_blank">', '<a href="https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=119dd1c656f672aab67aaf405dd6f08e" target="_blank">', '</a>' ); ?></p>
                </div>
				<?php
			}

            $contact_form_multi_active = cntctfrm_check_cf_multi_active();
			if ( ! $contact_form_multi_active ) { ?>
				<h3 class="nav-tab-wrapper cntctfrm_noborder">
					<span class="nav-tab nav-tab-active"><?php esc_html_e( 'NEW_FORM', 'contact-form-plugin' ); ?></span>
					<a id="cntctfrm_show_multi_notice" class="nav-tab" target="_new" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form-multi/?k=57d8351b1c6b67d3e0600bd9a680c283&amp;pn=3&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>" title="<?php esc_html_e( "If you want to create multiple contact forms, please install the Contact Form Multi plugin.", 'contact-form-plugin' ); ?>">+</a>
				</h3>
			<?php } else { ?>
				<h3 class="nav-tab-wrapper cntctfrm_noborder"></h3>
                <?php if ( version_compare( $cntctfrmmlt_plugin_info['Version'] , '1.2.6', '<') ) { ?>
                    <div class="error below-h2"><p><strong><?php  esc_html_e( "Contact Form plugin doesn't support your current version of Contact Form Multi plugin. Please update Contact Form Multi plugin to version 1.2.6 or higher.", 'contact-form-plugin' ); ?></strong></p></div>
                <?php } ?>
			<?php }
			$page->display_content(); ?>
		</div>
	<?php }
}

/* Display contact form in front end - page or post */
if ( ! function_exists( 'cntctfrm_display_form' ) ) {
	function cntctfrm_display_form( $atts = array( 'lang' => 'default' ) ) {
		global $cntctfrm_error_message, $cntctfrm_options, $cntctfrm_plugin_info, $cntctfrm_result, $cntctfrmmlt_ide, $cntctfrmmlt_active_plugin, $cntctfrm_form_count, $cntctfrm_related_plugins, $cntctfrm_stile_options;

		if ( empty( $cntctfrm_related_plugins ) )
			cntctfrm_related_plugins();

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( ! wp_script_is( 'cntctfrm_frontend_script', 'registered' ) )
			wp_register_script( 'cntctfrm_frontend_script', plugins_url( 'js/cntctfrm.js', __FILE__ ), array( 'jquery' ), $cntctfrm_plugin_info["Version"], true );

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

		$cntctfrm_stile_options[ $cntctfrm_form_count ] = $options_name;
		/* check lang and replace with en default if need */
		foreach ( $cntctfrm_options as $key => $value ) {
			if ( is_array( $value ) && array_key_exists( 'default', $value ) && ( ! array_key_exists( $lang, $value ) || ( isset( $cntctfrm_options[ $key ][ $lang ] ) && $cntctfrm_options[ $key ][ $lang ] == '' ) ) ) {
				$cntctfrm_options[ $key ][ $lang ] = $cntctfrm_options[ $key ]['default'];
			}
		}

		$page_url = esc_url( add_query_arg( array() ) . '#cntctfrm_contact_form' );

		/* If contact form submited */
        $default_value = '';
		$form_submited = isset( $_POST['cntctfrm_form_submited'] ) ? sanitize_key( $_POST['cntctfrm_form_submited'] ) : 0;
		$name = ( isset( $_POST['cntctfrm_contact_name'] ) && $cntctfrm_form_count == $form_submited ) ? stripcslashes( sanitize_text_field( $_POST['cntctfrm_contact_name'] ) ) : apply_filters( 'cntctfrm_default_name_check', $default_value );
		$address = ( isset( $_POST['cntctfrm_contact_address'] ) && $cntctfrm_form_count == $form_submited ) ? stripcslashes( sanitize_text_field( $_POST['cntctfrm_contact_address'] ) ) : "";
		$email = ( isset( $_POST['cntctfrm_contact_email'] ) && $cntctfrm_form_count == $form_submited ) ? stripcslashes( sanitize_email( $_POST['cntctfrm_contact_email'] ) ) : apply_filters( 'cntctfrm_default_email_check', $default_value );
		$subject = ( isset( $_POST['cntctfrm_contact_subject'] ) && $cntctfrm_form_count == $form_submited ) ? stripcslashes( sanitize_text_field( $_POST['cntctfrm_contact_subject'] ) ) : apply_filters( 'cntctfrm_default_subject_check', $default_value );
		$message = ( isset( $_POST['cntctfrm_contact_message'] ) && $cntctfrm_form_count == $form_submited ) ? stripcslashes( sanitize_textarea_field( $_POST['cntctfrm_contact_message'] ) ) : apply_filters( 'cntctfrm_default_message_check', $default_value );
		$phone = ( isset( $_POST['cntctfrm_contact_phone'] ) && $cntctfrm_form_count == $form_submited ) ? stripcslashes( sanitize_text_field( $_POST['cntctfrm_contact_phone'] ) ) : "";

		$send_copy = ( isset( $_POST['cntctfrm_contact_send_copy'] ) && $cntctfrm_form_count == $form_submited ) ? $_POST['cntctfrm_contact_send_copy'] : "";
		$gdpr = ( isset( $_POST['cntctfrm_contact_gdpr'] ) && $cntctfrm_form_count == $form_submited ) ? $_POST['cntctfrm_contact_gdpr'] : "";
		/* If it is good */

		if ( true === $cntctfrm_result && $cntctfrm_form_count == $form_submited ) {

			if ( 1 == $cntctfrm_options['action_after_send'] ) {
                $content .= '<div id="cntctfrm_contact_form' . $form_countid . '"><div id="cntctfrm_thanks">' . $cntctfrm_options['thank_text'][$lang] . '</div></div>';
            } else {
                $content .= "<script type='text/javascript'>window.location.href = '" . $cntctfrm_options['redirect_url'] . "';</script>";

            }
		} elseif ( false === $cntctfrm_result && $cntctfrm_form_count == $form_submited ) {
			/* If email not be delivered */
			$cntctfrm_error_message['error_form'] = esc_html__( "Sorry, email message could not be delivered.", 'contact-form-plugin' );
		}

		if ( true !== $cntctfrm_result || $cntctfrm_form_count != $form_submited ) {

			$classes = ( $cntctfrm_options['layout'] === 1 ) ? ' cntctfrm_one_column' : ' cntctfrm_two_columns';
			$classes .= is_rtl() ? ' cntctfrm_rtl' : ' cntctfrm_ltr';
			$classes .= ( 'custom' != $cntctfrm_options['width']['type'] ) ? ' cntctfrm_width_default' : ' cntctfrm_width_custom';

			/* Output form */
			$content .= '<form method="post" id="cntctfrm_contact_form' . $form_countid . '" class="cntctfrm_contact_form' . $classes . '"';
			$content .= ' action="' . $page_url .  $form_countid . '" enctype="multipart/form-data">';
			if ( isset( $cntctfrm_error_message['error_form'] ) && $cntctfrm_form_count == $form_submited ) {
				$content .= '<div class="cntctfrm_error_text">' . ( isset( $cntctfrm_result['error_lmtttmpts'] ) ? $cntctfrm_result['error_lmtttmpts'] : $cntctfrm_error_message['error_form'] ) . '</div>';
			}
            if ( ! isset( $id ) ) {
	            $cntctfrm_ordered_fields = cntctfrm_get_ordered_fields();
            } else {
	            $cntctfrm_ordered_fields = cntctfrm_get_ordered_fields( $id );
            }

			for ( $i = 1; $i <= $cntctfrm_options['layout']; $i++ ) {

				$column = ( $i == 1 ) ? 'first_column' : 'second_column';
				$content .= '<div id="cntctfrm_' . $column . '" class="cntctfrm_column">';
                $custom_fields = apply_filters( 'cntctfrm_custom_fields', $atts );
				foreach ( $cntctfrm_ordered_fields[ $column ] as $cntctfrm_field ) {
					switch( $cntctfrm_field ) {
						case 'cntctfrm_contact_name':
							if ( 1 == $cntctfrm_options['display_name_field'] ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_name_wrap"' . apply_filters( 'cntctfrm_visibility', 'namecontact' ) . '>';
									$content .= '<div class="cntctfrm_label cntctfrm_label_name">
										<label for="cntctfrm_contact_name' . $form_countid . '">' . $cntctfrm_options['name_label'][ $lang ] . ( $cntctfrm_options['required_name_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' );
									$content .= '</div>';
									if ( isset( $cntctfrm_error_message['error_name'] ) && $cntctfrm_form_count == $form_submited ) {
										$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_name'] . '</div>';
									}
									$content .= '<div class="cntctfrm_input cntctfrm_input_name">
										<input ' . apply_filters( 'cntctfrm_readonly', 'namecontact' ) . ' class="text" type="text" size="40" value="' . $name . '" name="cntctfrm_contact_name" id="cntctfrm_contact_name' . $form_countid . '" />';
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
							$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_email_wrap"' . apply_filters( 'cntctfrm_visibility', 'email' ) . '>';
								$content .= '<div class="cntctfrm_label cntctfrm_label_email">
										<label for="cntctfrm_contact_email' . $form_countid . '">' . $cntctfrm_options['email_label'][ $lang ] . ( $cntctfrm_options['required_email_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '
									</div>';
								if ( isset( $cntctfrm_error_message['error_email'] ) && $cntctfrm_form_count == $form_submited ) {
									$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_email'] . '</div>';
								}
								$content .= '<div class="cntctfrm_input cntctfrm_input_email">
										<input ' . apply_filters( 'cntctfrm_readonly', 'email' ) . ' class="text" type="text" size="40" value="' . $email . '" name="cntctfrm_contact_email" id="cntctfrm_contact_email' . $form_countid . '" />';
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
							$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_subject_wrap"' . apply_filters( 'cntctfrm_visibility', 'subject' ) . '>';
								$content .= '<div class="cntctfrm_label cntctfrm_label_subject">
										<label for="cntctfrm_contact_subject' . $form_countid . '">' . $cntctfrm_options['subject_label'][ $lang ] . ( $cntctfrm_options['required_subject_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '
									</div>';
								if ( isset( $cntctfrm_error_message['error_subject'] ) && $cntctfrm_form_count == $form_submited ) {
									$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_subject'] . '</div>';
								}
								$content .= '<div class="cntctfrm_input cntctfrm_input_subject">
									<input ' . apply_filters( 'cntctfrm_readonly', 'subject' ) . 'class="text" type="text" size="40" value="' . $subject . '" name="cntctfrm_contact_subject" id="cntctfrm_contact_subject' . $form_countid . '" />';
								$content .= '</div>';
							$content .= '</div>';
							break;
						case 'cntctfrm_contact_message':
							$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_message_wrap"' . apply_filters( 'cntctfrm_visibility', 'message' ) . '>';
								$content .= '<div class="cntctfrm_label cntctfrm_label_message">
									<label for="cntctfrm_contact_message' . $form_countid . '">' . $cntctfrm_options['message_label'][ $lang ] . ( $cntctfrm_options['required_message_field'] == 1 ? ' <span class="required">' . $cntctfrm_options['required_symbol'] . '</span></label>' : '</label>' ) . '
								</div>';
								if ( isset( $cntctfrm_error_message['error_message'] ) && $cntctfrm_form_count == $form_submited ) {
									$content .= '<div class="cntctfrm_error_text">' . $cntctfrm_error_message['error_message'] . '</div>';
								}
								$content .= '<div class="cntctfrm_input cntctfrm_input_message">
									<textarea ' . apply_filters( 'cntctfrm_readonly', 'message' ) . ' rows="5" cols="30" name="cntctfrm_contact_message" id="cntctfrm_contact_message' . $form_countid . '">' . $message . '</textarea>';
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

								    if ( 1 == $cntctfrm_options['active_multi_attachment'] ) {
									    $content .= '<div class="cntctfrm_input cntctfrm_input_attachment"> 
											<input type="file" multiple name="cntctfrm_contact_attachment[]" id="cntctfrm_contact_attachment' . $form_countid . '"' . ( isset( $cntctfrm_error_message['error_attachment'] ) ? "class='error'": "" ) . ' />';
                                    } else {
									    $content .= '<div class="cntctfrm_input cntctfrm_input_attachment"> 
											<input type="file" name="cntctfrm_contact_attachment" id="cntctfrm_contact_attachment' . $form_countid . '"' . ( isset( $cntctfrm_error_message['error_attachment'] ) ? "class='error'": "" ) . ' />';
                                    }


									$content .= '</div>';
									if ( 1 == $cntctfrm_options['attachment_explanations'] ) {
											$content .= '<label class="cntctfrm_contact_attachment_extensions"><br />' . $cntctfrm_options['attachment_tooltip'][ $lang ] . '</label>';
									}

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
						case 'cntctfrm_contact_gdpr':
							if ( 1 == $cntctfrm_options['gdpr'] ) {
								$content .= '<div class="cntctfrm_field_wrap cntctfrm_field_attachment_wrap">';
									$content .= '<div class="cntctfrm_checkbox cntctfrm_checkbox_gdpr">
										<input type="checkbox" value="" required name="cntctfrm_contact_gdpr" id="cntctfrm_contact_gdpr"' . ( $gdpr == '1' ? ' checked="checked" ' : "" ) . ' />
										<label for="cntctfrm_contact_gdpr">' . $cntctfrm_options['gdpr_label'][ $lang ] . '</label>';
									if( ! empty( $cntctfrm_options['gdpr_link'] ) ) {
										$content .= ' ' . '<a target="_blank" href="' . $cntctfrm_options['gdpr_link'] . '">' . $cntctfrm_options['gdpr_text_button'][ $lang ] . '</a>';
									} else {
										$content .= '<span>' . ' ' . $cntctfrm_options['gdpr_text_button'][ $lang ] . '</span>';
									}
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
								if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_captcha'] ) ) ) ) {
									$display_captcha = true;
								}
								if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_google_captcha'] ) ) ) ) {
									$display_google_captcha = true;
								}

								if ( ! empty( $display_captcha ) ) {
									if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) ) {
										$captcha_label = isset( $cntctfrm_related_plugins['captcha']['options']['title'] ) ? $cntctfrm_related_plugins['captcha']['options']['title'] : '';
										if ( ! empty( $captcha_label ) ) {
											$captcha_required_symbol = sprintf( '<span class="required">%s</span>', ( ! empty( $cntctfrm_related_plugins['captcha']['options']['required_symbol'] ) ) ? $cntctfrm_related_plugins['captcha']['options']['required_symbol'] : '' );
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
										$content .= apply_filters( 'cntctfrm_display_captcha', '', 'bws_contact' );
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
				if ( has_filter( 'cntctfrm_foreach_custom_fields' ) )
                    $content = apply_filters('cntctfrm_foreach_custom_fields', $custom_fields, $i, $form_submited, $form_countid, $lang, $content );

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
						$content .= '<div class="cntctfrm_input cntctfrm_input_submit">';
						if ( isset( $atts['id'] ) )
							$content .= '<input type="hidden" value="' . esc_attr( $atts['id'] ) . '" name="cntctfrmmlt_shortcode_id">';
						$content .= '<input type="hidden" value="send" name="cntctfrm_contact_action">
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
* $action               string    can be 'remove_filters' or 'add_filters'
* $removed_filters      array of existed filters (returned from this function when 'remove_filters' action)
* @return               array of existed filters for 'remove_filters' or 'false' for 'add_filters'
*/
if ( ! function_exists( 'cntctfrm_handle_captcha_filters' ) ) {
	function cntctfrm_handle_captcha_filters( $action, $removed_filters = false ) {
		global $cntctfrm_options, $cntctfrm_related_plugins;

		if ( 'remove_filters' == $action ) {

			if ( empty( $cntctfrm_related_plugins ) )
				cntctfrm_related_plugins();

			$contact_form_multi_active = cntctfrm_check_cf_multi_active();

			$removed_filters = $remove_captcha = array();

			if ( ! ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_captcha'] ) ) ) ) )
				$remove_captcha[] = 'captcha';

			if ( ! ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) && ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) || ( $contact_form_multi_active && ! empty( $cntctfrm_options['display_google_captcha'] ) ) ) ) )
				$remove_captcha[] = 'google-captcha';

			$filters = array(
				'google-captcha'    => array(
					'gglcptch_cf_display'       => 'gglcptch_recaptcha_check',
					'gglcptchpr_cf_display'     => 'gglcptchpr_recaptcha_check',
					'gglcptch_display'          => 'gglcptch_contact_form_check',
				),
				'captcha'           => array(
					'cptch_custom_form'         => 'cptch_check_custom_form',
					'cptchpls_custom_form'      => 'cptchpls_check_custom_form',
					'cptchpr_custom_form'       => 'cptchpr_check_custom_form',
					'cptch_custom_form'         => 'cptch_check_bws_contact_form',
                    'cptch_cf_form'             => 'cptch_check_bws_contact_form',
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
					add_filter( $tag, $function, 10, 2 );
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
			$cntctfrm_options = get_option( sanitize_text_field( $_POST['cntctfrm_options_name'] ) );

			if ( isset( $_POST['cntctfrm_contact_action'] ) ) {
				/* Check all input data */
				$cntctfrm_result = cntctfrm_check_form();
			}
			/* If it is good */
			if ( true === $cntctfrm_result ) {
                setcookie( 'cntctfrm_send_mail', true );
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
		$name = isset( $_POST['cntctfrm_contact_name'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_name'] ) : "";
		$address = isset( $_POST['cntctfrm_contact_address'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_address'] ) : "";
		$email = isset( $_POST['cntctfrm_contact_email'] ) ? sanitize_email( $_POST['cntctfrm_contact_email'] ) : "";
		$subject = isset( $_POST['cntctfrm_contact_subject'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_subject'] ) : "";
		$message = isset( $_POST['cntctfrm_contact_message'] ) ? sanitize_textarea_field( $_POST['cntctfrm_contact_message'] ) : "";
		$phone = isset( $_POST['cntctfrm_contact_phone'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_phone'] ) : "";

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
			global $cntctfrm_path_of_uploaded_file, $cntctfrm_path_of_uploaded_files, $cntctfrm_mime_type;
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

		if (
			(
				array_key_exists( 'captcha', $cntctfrm_related_plugins ) &&
				(
					( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] ) ) ||
					( $contact_form_multi_active && ! empty( $cntctfrm_options['display_captcha'] ) )
				)
			) ||
			( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) )
		) {
			if ( false === $result ) { /* for CAPTCHA older than PRO - v1.0.7, PLUS - v1.1.0 v FREE - 1.2.5 */
				$cntctfrm_error_message['error_captcha'] = $cntctfrm_options['captcha_error'][ $language ];
			} else if ( ! empty( $result ) && ( is_string( $result ) || is_wp_error( $result ) ) ) {
				$cntctfrm_error_message['error_captcha'] = is_string( $result ) ? $result : implode( '', $result->get_error_messages() );
			}
		}

		if ( ! empty( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && array("") != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) {

			// Number of uploaded files
			$num_files = count( (array)$_FILES['cntctfrm_contact_attachment']['tmp_name']);

			for($i=0; $i < $num_files;$i++) {

				if ( $cntctfrm_options['active_multi_attachment'] ) {
					$new_filename = 'cntctfrm_' . md5( sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"][$i] ) . time() . $email . mt_rand() ) . '_' . sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"][$i] );
				} else {
					$new_filename = 'cntctfrm_' . md5( sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] ) . time() . $email . mt_rand() ) . '_' . sanitize_file_name( $_FILES["cntctfrm_contact_attachment"]["name"] );
				}
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

				if ( $cntctfrm_options['active_multi_attachment'] ) {
					$tmp_path = $_FILES["cntctfrm_contact_attachment"]["tmp_name"][$i];
				} else {
					$tmp_path = $_FILES["cntctfrm_contact_attachment"]["tmp_name"];
				}

				$path_info = pathinfo( $cntctfrm_path_of_uploaded_file );

				if ( isset( $path_info['extension'] ) && array_key_exists( strtolower( $path_info['extension'] ), $cntctfrm_mime_type ) ) {
					if ( is_uploaded_file( $tmp_path ) ) {
						if ( move_uploaded_file( $tmp_path, $cntctfrm_path_of_uploaded_file ) ) {
							do_action( 'cntctfrm_get_attachment_data', $cntctfrm_path_of_uploaded_file );
							//adding to array
							$cntctfrm_path_of_uploaded_files[] = $cntctfrm_path_of_uploaded_file;
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

							if ( $cntctfrm_options['active_multi_attachment'] ) {
								$cntctfrm_contact_attachment_size = $_FILES["cntctfrm_contact_attachment"]["size"][$i];
							} else {
								$cntctfrm_contact_attachment_size = $_FILES["cntctfrm_contact_attachment"]["size"];
							}

							if ( isset( $cntctfrm_contact_attachment_size ) && $cntctfrm_contact_attachment_size <= $upload_max_size ) {
								$cntctfrm_error_message['error_attachment'] = $cntctfrm_options['attachment_move_error'][ $language ];
							} else {
								$cntctfrm_error_message['error_attachment'] = $cntctfrm_options['attachment_size_error'][ $language ];
							}
						}
					} else {
						$cntctfrm_error_message['error_attachment'] = $cntctfrm_options['attachment_upload_error'][ $language ];
					}
				}
			}
		} else {
			unset( $cntctfrm_error_message['error_attachment'] );
		}
        $cntctfrm_error_message = apply_filters('cntctfrm_check_fields', $cntctfrm_error_message );
		if ( 1 == count( $cntctfrm_error_message ) ) {
			if ( has_filter( 'sbscrbr_cntctfrm_checkbox_check' ) ) {
				$cntctfrm_sbscrbr_check = apply_filters( 'sbscrbr_cntctfrm_checkbox_check', array(
					'form_id' => 'cntctfrm_' . sanitize_key( $_POST['cntctfrm_form_submited'] ),
					'email'   => $email,
					'name'    => $name
				) );
				if ( isset( $cntctfrm_sbscrbr_check['response'] ) && $cntctfrm_sbscrbr_check['response']['type'] == 'error' ) {
					$cntctfrm_error_message['error_sbscrbr'] = $cntctfrm_sbscrbr_check['response'];
					return $cntctfrm_result;
				}
			}

			/* Check for Limit Attempts */
			if ( has_filter( 'cntctfrm_check' ) ) {
				$cntctfrm_limit_check = apply_filters( 'cntctfrm_check', $cntctfrm_error_message );

				if ( $cntctfrm_limit_check )
					return $cntctfrm_limit_check;
			}

			unset( $cntctfrm_error_message['error_form'] );
			/* If all is good - send mail */
			$cntctfrm_result = cntctfrm_send_mail();

			$save_emails  = false;
			if ( ! $contact_form_multi_active && array_key_exists( 'contact-form-to-db' , $cntctfrm_related_plugins ) )
				$save_emails = ! empty( $cntctfrm_related_plugins['contact-form-to-db']['options']['save_messages_to_db'] );
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
		global $cntctfrm_options, $cntctfrm_path_of_uploaded_file, $cntctfrm_path_of_uploaded_files, $wp_version, $wpdb;
		$to = $headers  = "";

		$lang = isset( $_POST['cntctfrm_language'] ) ? sanitize_text_field( $_POST['cntctfrm_language'] ) : 'default';

		$name = isset( $_POST['cntctfrm_contact_name'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_name'] ) : "";
		$address = isset( $_POST['cntctfrm_contact_address'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_address'] ) : "";
		$email = isset( $_POST['cntctfrm_contact_email'] ) ? sanitize_email( $_POST['cntctfrm_contact_email'] ) : "";
		$subject = isset( $_POST['cntctfrm_contact_subject'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_subject'] ) : "";
		$message = isset( $_POST['cntctfrm_contact_message'] ) ? sanitize_textarea_field( $_POST['cntctfrm_contact_message'] ) : "";
		$phone = isset( $_POST['cntctfrm_contact_phone'] ) ? sanitize_text_field( $_POST['cntctfrm_contact_phone'] ) : "";
		$user_agent = cntctfrm_clean_input( $_SERVER['HTTP_USER_AGENT'] );
		if ( isset( $_COOKIE['cntctfrm_send_mail'] ) && true == $_COOKIE['cntctfrm_send_mail'] )
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
						$user_info_string .= '<tr><td>' . esc_html__( 'Sent from (ip address)', 'contact-form-plugin' ) . ':</td><td>' . $cntctfrm_remote_addr . " ( " . $userdomain . " )" . '</td></tr>';
					else
						$user_info_string .= esc_html__( 'Sent from (ip address)', 'contact-form-plugin' ) . ': ' . $cntctfrm_remote_addr . " ( " . $userdomain . " )" . "\n";
				}
				if ( 1 == $cntctfrm_options['display_date_time'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td>' . esc_html__('Date/Time', 'contact-form-plugin') . ':</td><td>' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( current_time( 'mysql' ) ) ) . '</td></tr>';
					else
						$user_info_string .= esc_html__( 'Date/Time', 'contact-form-plugin' ) . ': ' . date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( current_time( 'mysql' ) ) ) . "\n";
				}
				if ( 1 == $cntctfrm_options['display_coming_from'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td>' . esc_html__( 'Sent from (referer)', 'contact-form-plugin' ) . ':</td><td>' . $form_action_url . '</td></tr>';
					else
						$user_info_string .= esc_html__( 'Sent from (referer)', 'contact-form-plugin' ) . ': ' . $form_action_url . "\n";
				}
				if ( 1 == $cntctfrm_options['display_user_agent'] ) {
					if ( 1 == $cntctfrm_options['html_email'] )
						$user_info_string .= '<tr><td>' . esc_html__( 'Using (user agent)', 'contact-form-plugin' ) . ':</td><td>' . $user_agent . '</td></tr>';
					else
						$user_info_string .= esc_html__( 'Using (user agent)', 'contact-form-plugin' ) . ': ' . $user_agent . "\n";
				}
			}

			/* Message */
			$message_order_fields = array_merge( $cntctfrm_options['order_fields']['first_column'], $cntctfrm_options['order_fields']['second_column'] );

			if ( 1 == $cntctfrm_options['html_email'] ) {
				$message_text = '<html>
					<head>
						<title>'. esc_html__( "Contact from", 'contact-form-plugin' ) . ' ' . get_bloginfo( 'name' ) . '</title>
					</head>
					<body>
						<table>
							<tr>
								<td>' . esc_html__( "Site", 'contact-form-plugin' ) . '</td>
								<td>' . get_bloginfo( "url" ) . '</td>
							</tr>';
				foreach ( $message_order_fields as $field ){
					$field = str_replace( 'cntctfrm_contact_', '', $field );
					switch ( $field ) {
						case "name":
							if ( 1 == $cntctfrm_options['display_name_field'] ) {
								$message_text .= '<tr><td width="160">';
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['name_label'][ $lang ] : esc_html__( "Name", 'contact-form-plugin' );
								$message_text .= '</td><td>'. $name .'</td></tr>';
							}
							break;
						case "address":
							if ( 1 == $cntctfrm_options['display_address_field'] ) {
								$message_text .= '<tr><td>';
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['address_label'][ $lang ] : esc_html__( "Address", 'contact-form-plugin' );
								$message_text .= '</td><td>'. $address .'</td></tr>';
							}
							break;
						case "email":
							$message_text .= '<tr><td>';
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['email_label'][ $lang ] : esc_html__( "Email", 'contact-form-plugin' );
							$message_text .= '</td><td>'. $email .'</td></tr>';
							break;
						case "subject":
							$message_text .= '<tr><td>';
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['subject_label'][ $lang ] : esc_html__( "Subject", 'contact-form-plugin' );
							$message_text .= '</td><td>' . $subject .'</td></tr>';
							break;
						case "message":
							$message_text .= '<tr><td>';
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['message_label'][ $lang ] : esc_html__( "Message", 'contact-form-plugin' );
							$message_text .= '</td><td>' . $message .'</td></tr>';
							break;
						case "phone":
							if ( 1 == $cntctfrm_options['display_phone_field'] ) {
								$message_text .= '<tr><td>';
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['phone_label'][ $lang ] : esc_html__( "Phone Number", 'contact-form-plugin' );
								$message_text .= '</td><td>'. $phone .'</td></tr>';
							}
							break;
					}
				}
                $message_text = apply_filters( 'cntctfrm_cf_id_message_text', $message_text );
				$message_text .= '<tr><td><br /></td><td><br /></td></tr>';

				$message_text_for_user = $message_text . '</table></body></html>';
				$message_text .= $user_info_string . '</table></body></html>';
			} else {
				$message_text = esc_html__( "Site", 'contact-form-plugin' ) . ': ' . get_bloginfo("url") . "\n";;
				foreach ( $message_order_fields as $field ) {
					$field = str_replace( 'cntctfrm_contact_', '', $field );
					switch ( $field ) {
						case "name":
							if ( 1 == $cntctfrm_options['display_name_field'] ) {
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['name_label'][ $lang ] : esc_html__( "Name", 'contact-form-plugin' );
								$message_text .= ': '. $name . "\n";
							}
							break;
						case "address":
							if ( 1 == $cntctfrm_options['display_address_field'] ) {
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['address_label'][ $lang ] : esc_html__( "Address", 'contact-form-plugin' );
								$message_text .= ': '. $address . "\n";
							}
							break;
						case "email":
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['email_label'][ $lang ] : esc_html__( "Email", 'contact-form-plugin' );
							$message_text .= ': ' . $email . "\n";
							break;
						case "subject":
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['subject_label'][ $lang ] : esc_html__( "Subject", 'contact-form-plugin' );
							$message_text .= ': ' . $subject . "\n";
							break;
						case "message":
							$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['message_label'][ $lang ] : esc_html__( "Message", 'contact-form-plugin' );
							$message_text .= ': ' . $message ."\n";
							break;
						case "phone":
							if ( 1 == $cntctfrm_options['display_phone_field'] ) {
								$message_text .= ( 1 == $cntctfrm_options['change_label_in_email'] ) ? $cntctfrm_options['phone_label'][ $lang ] : esc_html__( "Phone Number", 'contact-form-plugin' );
								$message_text .= ': '. $phone . "\n";
							}
							break;
					}
				}
                $message_text = apply_filters( 'cntctfrm_cf_id_message_text_field', $message_text );
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

			if ( 'wp-mail' == $cntctfrm_options['mail_method'] ) {
				/* To send HTML mail, the Content-type header must be set */
				if ( 1 == $cntctfrm_options['html_email'] )
					$headers .= 'Content-type: text/html; charset=utf-8' . "\n";
				else
					$headers .= 'Content-type: text/plain; charset=utf-8' . "\n";

				/* Additional headers */
				$headers .= 'From: ' . $from_field_name . ' <' . $from_email . '>';

				if ( 1 == $cntctfrm_options['attachment'] && ! empty( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && array("") != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) {
					if( empty( $cntctfrm_path_of_uploaded_files ) || ! is_array( $cntctfrm_path_of_uploaded_files ) || sizeof( $cntctfrm_path_of_uploaded_files ) <= 1 ) {
						$path_parts = pathinfo( $cntctfrm_path_of_uploaded_file );
						$cntctfrm_path_of_uploaded_file_changed = $path_parts['dirname'] . '/' . preg_replace( '/^cntctfrm_[A-Z,a-z,0-9]{32}_/i', '', $path_parts['basename'] );

						if ( ! @copy( $cntctfrm_path_of_uploaded_file, $cntctfrm_path_of_uploaded_file_changed ) )
							$cntctfrm_path_of_uploaded_file_changed = $cntctfrm_path_of_uploaded_file;

						$attachments = array( $cntctfrm_path_of_uploaded_file_changed );
					} else {
						//multiple files
						$attachments = array();
						foreach( $cntctfrm_path_of_uploaded_files as $cntctfrm_path_of_uploaded_file ) {
							$path_parts = pathinfo( $cntctfrm_path_of_uploaded_file );
							$cntctfrm_path_of_uploaded_file_changed = $path_parts['dirname'] . '/' . preg_replace( '/^cntctfrm_[A-Z,a-z,0-9]{32}_/i', '', $path_parts['basename'] );

							if ( ! @copy( $cntctfrm_path_of_uploaded_file, $cntctfrm_path_of_uploaded_file_changed ) )
								$cntctfrm_path_of_uploaded_file_changed = $cntctfrm_path_of_uploaded_file;

							$attachments[] = $cntctfrm_path_of_uploaded_file_changed;
						}
					}

				}

				if ( isset( $_POST['cntctfrm_contact_send_copy'] ) && 1 == $_POST['cntctfrm_contact_send_copy'] )
					wp_mail( $email, $subject, $message_text_for_user, $headers, $attachments );

				/* Mail it */
                $mail_result = wp_mail( $to, $subject, $message_text, $headers, $attachments );
				/* Delete attachment */
				if ( 1 == $cntctfrm_options['attachment'] && ! empty( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && array("") != $_FILES["cntctfrm_contact_attachment"]["tmp_name"]
					&& $cntctfrm_path_of_uploaded_file_changed != $cntctfrm_path_of_uploaded_file ) {
					@unlink( $cntctfrm_path_of_uploaded_file_changed );
				}
				if ( 1 == $cntctfrm_options['attachment'] && ! empty( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && array("") != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] && '1' == $cntctfrm_options['delete_attached_file'] ) {
					@unlink( $cntctfrm_path_of_uploaded_file );
				}
				return $mail_result;
			} else {
				/* Set headers */
				$headers  .= 'MIME-Version: 1.0' . "\n";

				if ( 1 == $cntctfrm_options['attachment'] && ! empty( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && array("") != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) {
					$message_block = $message_text;
					$message_block_for_user = $message_text_for_user;

					/* Additional headers */
					$headers .= 'From: ' . $from_field_name . ' <' . $from_email . '>' . "\n";

					$bound_text = "jimmyP123";

					$bound = "--" . $bound_text . "";

					$bound_last = "--" . $bound_text . "--";

					$headers .= "Content-Type: multipart/mixed; boundary=\"$bound_text\"";

					$message_text = $message_text_for_user = esc_html__( "If you can see this MIME, it means that the MIME type is not supported by your email client!", 'contact-form-plugin' ) . "\n";

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
				if ( 1 == $cntctfrm_options['attachment'] && ! empty( $_FILES["cntctfrm_contact_attachment"]["tmp_name"] ) && array("") != $_FILES["cntctfrm_contact_attachment"]["tmp_name"] && '1' == $cntctfrm_options['delete_attached_file'] ) {
					@unlink( $cntctfrm_path_of_uploaded_file );
				}
				return $mail_result;
			}
		}
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
				$settings_link = '<a href="admin.php?page=contact_form.php">' . esc_html__( 'Settings', 'contact-form-plugin' ) . '</a>';
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
				$links[] = '<a href="admin.php?page=contact_form.php">' . esc_html__( 'Settings','contact-form-plugin' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/plugins/contact-form-plugin/faq/" target="_blank">' . esc_html__( 'FAQ','contact-form-plugin' ) . '</a>';
			$links[] = '<a href="https://support.bestwebsoft.com">' . esc_html__( 'Support','contact-form-plugin' ) . '</a>';
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
		global $cntctfrm_plugin_info;
		wp_enqueue_style( 'cntctfrm_icon_stylesheet', plugins_url( 'css/icon_style.css', __FILE__ ), false, $cntctfrm_plugin_info["Version"] );
		if ( isset( $_REQUEST['page'] ) && ( 'contact_form.php' == $_REQUEST['page'] ) ) {
			global $wp_version, $cntctfrm_plugin_info;

			wp_enqueue_style( 'cntctfrm_stylesheet', plugins_url( 'css/style.css', __FILE__ ), false, $cntctfrm_plugin_info["Version"] );
            do_action( 'cntctfrm_include_plus_style' );

			wp_enqueue_style( 'cntctfrm_form_style', plugins_url( 'css/form_style.css', __FILE__ ), false, $cntctfrm_plugin_info["Version"] );

			$script_vars = array(
				'cntctfrm_nonce'        => wp_create_nonce( plugin_basename( __FILE__ ), 'cntctfrm_ajax_nonce_field' ),
				'cntctfrm_confirm_text'  => esc_html__( 'Are you sure that you want to delete this language data?', 'contact-form-plugin' )
			);

			if ( wp_is_mobile() )
				wp_enqueue_script( 'jquery-touch-punch' );

			wp_enqueue_script( 'cntctfrm_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ), $cntctfrm_plugin_info["Version"] );
			wp_localize_script( 'cntctfrm_script', 'cntctfrm_ajax', $script_vars );
			do_action('cntctfrm_custom_enqueue_script');

			bws_enqueue_settings_scripts();

			bws_plugins_include_codemirror();

			require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );

			$tooltip_args = array(
				'tooltip_id'    => 'cntctfrm_install_multi_tooltip',
				'css_selector'  => '#cntctfrm_show_multi_notice',
				'actions'       => array(
					'click'     => true,
					'onload'    => true,
				),
				'content'           => '<h3>' . esc_html__( 'Add multiple forms', 'contact-form-plugin' ) . '</h3>' .'<p>' . esc_html__( 'Install Contact Form Multi plugin to create unlimited number of contact forms.', 'contact-form-plugin' ) . '</p>',
				'buttons'           => array(
					array(
						'type' => 'link',
						'link' => 'https://bestwebsoft.com/products/wordpress/plugins/contact-form-multi/?k=747ca825fb44711e2d24e40697747bc6&pn=77&v=' . $cntctfrm_plugin_info["Version"] . '&wp_v=' . $wp_version,
						'text' => esc_html__( 'Learn more', 'contact-form-plugin' ),
					),
					'close' => array(
						'type' => 'dismiss',
						'text' => esc_html__( 'Close', 'contact-form-plugin' ),
					),
				),
				'position' => array(
					'edge'      => 'top',
					'align'     => is_rtl() ? 'right' : 'left',
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
		global $cntctfrm_form_count, $cntctfrm_stile_options, $cntctfrm_plugin_info;

		if ( wp_script_is( 'cntctfrm_frontend_script', 'registered' ) )
			wp_enqueue_script( 'cntctfrm_frontend_script' );

		for ( $count = 1; $count <= $cntctfrm_form_count; $count++ ) {
			$form_countid = ( 1 == $count ? '#cntctfrm_contact_form' : '#cntctfrm_contact_form_' . $count );
			$options = get_option( $cntctfrm_stile_options[ $count ] );
			if ( 'custom' == $options['width']['type'] ) { ?>
				<style id="cntctfrm_custom_styles__<?php echo $count; ?>" type="text/css">
					<?php echo $form_countid; ?>.cntctfrm_width_custom {
						max-width: <?php printf( '%d%s', $options['width']['input_value'], $options['width']['input_unit'] ); ?>;
					}
				</style>
			<?php }
		}
	}
}

if ( ! function_exists ( 'cntctfrm_add_language' ) ) {
	function cntctfrm_add_language() {
		global $cntctfrm_lang_codes;
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( $is_ajax )
			check_ajax_referer( plugin_basename( __FILE__ ), 'cntctfrm_ajax_nonce_field' );
		else
			$_POST['cntctfrm_change_tab'] = sanitize_text_field( $_REQUEST['cntctfrm_languages'] );

		$lang = $is_ajax ? $_REQUEST['lang'] : sanitize_text_field( $_REQUEST['cntctfrm_languages'] );

		if ( ! array_key_exists( $lang, $cntctfrm_lang_codes ) ) {
			$message = sprintf( '<strong>%s</strong>:&nbsp%s.', esc_html__( 'Error', 'contact-form-plugin' ), esc_html__( 'Illegal language code', 'contact-form-plugin' ) );
			if ( $is_ajax ) {
				throw new Exception( esc_html__( 'Illegal language code', 'contact-form-plugin' ) );
				die();
			} else {
				wp_die( $message );
			}
		}

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( $contact_form_multi_active ) {
            $multi_options_main = get_option( 'cntctfrmmlt_options_main' );
            if ( isset( $multi_options_main['id_form'] ) && $cntctfrm_options =  get_option( 'cntctfrmmlt_options_' . absint( $multi_options_main['id_form'] ) ) ) {
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
			$result = esc_html__( "Use shortcode", 'contact-form-plugin' ) . ' <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=' . $lang . ']</span> ' . esc_html__( "for this language", 'contact-form-plugin' );
		} else {
			$result = esc_html__( "Use shortcode", 'contact-form-plugin' ) . ' <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=' . $lang . ' id=' . $multi_options_main['id_form'] . ']</span> ' . esc_html__( "for this language", 'contact-form-plugin' );
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
		if ( $is_ajax )
			check_ajax_referer( plugin_basename( __FILE__ ), 'cntctfrm_ajax_nonce_field' );
		else
			$_POST['cntctfrm_change_tab'] = 'default';

		$contact_form_multi_active = cntctfrm_check_cf_multi_active();

		if ( $contact_form_multi_active ) {
            $multi_options_main = get_option( 'cntctfrmmlt_options_main' );
			if ( isset( $multi_options_main['id_form'] ) && $cntctfrm_options = get_option( 'cntctfrmmlt_options_' . absint( $multi_options_main['id_form'] ) ) ) {
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
		if ( isset( $cntctfrm_options['gdpr_label'][ $lang ] ) )
			unset( $cntctfrm_options['gdpr_label'][ $lang ] );
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

		if ( $is_ajax )
			die();
	}
}
if ( ! function_exists ( 'cntctfrm_plugin_banner' ) ) {
	function cntctfrm_plugin_banner() {
		global $hook_suffix;
		if ( 'plugins.php' == $hook_suffix || ( isset( $_REQUEST['page'] ) && 'contact_form.php' == $_REQUEST['page'] ) ) {
			global $cntctfrm_plugin_info, $wp_version, $bstwbsftwppdtplgns_cookie_add, $bstwbsftwppdtplgns_banner_array;

			if ( 'plugins.php' == $hook_suffix ) {
				bws_plugin_banner_to_settings( $cntctfrm_plugin_info, 'cntctfrm_options', 'contact-form-plugin', 'admin.php?page=contact_form.php' );
			}

			if ( isset( $_REQUEST['page'] ) && 'contact_form.php' == $_REQUEST['page'] ) {
				bws_plugin_suggest_feature_banner( $cntctfrm_plugin_info, 'cntctfrm_options', 'contact-form-plugin' );
			}

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
                        wp_enqueue_script( 'bstwbsftwppdtplgns_cookie_add', plugins_url( 'bws_menu/js/c_o_o_k_i_e.js', __FILE__ ) );
					}
                    $cntctfrm_script_banner = "
						(function($) {
						'use strict';
							$(document).ready( function() {
								var hide_message = $.cookie( '" . $this_banner_prefix . " _hide_banner_on_plugin_page' );
								if ( hide_message == \"true\" ) {
									$( '" . $this_banner_prefix . " _message' ).css( \"display\", \"none\" );
								} else {
									$( '" . $this_banner_prefix . " _message' ).css( \"display\", \"block\" );
								};
								$( '" . $this_banner_prefix . " _close_icon' ).on( 'click', function() {
									$( '" . $this_banner_prefix . " _message' ).css( \"display\", \"none\" );
									$.cookie( '" . $this_banner_prefix . " _hide_banner_on_plugin_page', \"true\", { expires: 32 } );
								});
                            } ) ( jQuery );
                        } )";

                    wp_register_script( 'cntctfrm_bws_script_banner', '//' );
                    wp_enqueue_script( 'cntctfrm_bws_script_banner' );
                    wp_add_inline_script( 'cntctfrm_bws_script_banner', sprintf( $cntctfrm_script_banner ) );

                    if ( ! array_key_exists( 'contact-form-to-db/contact_form_to_db.php', $all_plugins ) && ! array_key_exists( 'contact-form-to-db-pro/contact_form_to_db_pro.php', $all_plugins ) ) { ?>
						<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
							<div class="cntctfrm_for_ctfrmtdb_message bws_banner_on_plugin_page" style="display: none;">
								<button class="<?php echo $this_banner_prefix; ?>_close_icon close_icon notice-dismiss bws_hide_settings_notice" title="<?php esc_html_e( 'Close notice', 'contact-form-plugin' ); ?>"></button>
								<div class="icon">
									<img title="" src="//ps.w.org/contact-form-to-db/assets/icon-128x128.png" alt="" />
								</div>
								<div class="text">
									<strong>Contact Form to DB</strong> <?php esc_html_e( "allows to store your messages to the database.", 'contact-form-plugin' ); ?><br />
									<span><?php esc_html_e( "Manage messages that have been sent from your website.", 'contact-form-plugin' ); ?></span>
								</div>
								<div class="button_div">
									<a class="button" target="_blank" href="https://bestwebsoft.com/products/wordpress/plugins/contact-form-to-db/?k=6ebf0743736411607343ad391dc3b436&amp;pn=77&amp;v=<?php echo $cntctfrm_plugin_info["Version"]; ?>&amp;wp_v=<?php echo $wp_version; ?>"><?php esc_html_e( 'Learn More', 'contact-form-plugin' ); ?></a>
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
						<span class="title"><?php esc_html_e( 'Contact form', 'contact-form-plugin' ); ?></span>
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
						<span class="title"><?php esc_html_e( 'Language', 'contact-form-plugin' ); ?></span>
					</label>
					<input class="bws_default_shortcode" type="hidden" name="default" value="[bestwebsoft_contact_form id=<?php echo array_shift( $multi_ids ); ?>]" />
				<?php } else { ?>
					<label>
						<select name="cntctfrm_languages_list" id="cntctfrm_languages_list">
							<?php foreach ( $options['language'] as $language ) {
									printf( '<option value="%1$s">%2$s</option>', strtolower( $language ), ( $language == 'default' ) ? $lang_default : $cntctfrm_lang_codes[ $language ] );
							} ?>
						</select>
						<span class="title"><?php esc_html_e( 'Language', 'contact-form-plugin' ); ?></span>
					</label>
					<input class="bws_default_shortcode" type="hidden" name="default" value="[bestwebsoft_contact_form]" />
				<?php } ?>
			</fieldset>
            <?php $script = "function cntctfrm_shortcode_init() {
					( function( $ ) {
					'use strict'
						var current_object = '.mce-reset';";
                    if ( $contact_form_multi_active ) {
                        $script .= "$( current_object + ' #bws_shortcode_display' ).on( 'display_shortcode', function() {
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
                            cntctfrm_languages_options.filter( '[data-form-id=\"' + cntctfrm_form + '\"]' ).show();
                            cntctfrm_languages_options.filter( '[value=\"default\"]' ).attr( 'selected', true );
                            $( current_object + ' #bws_shortcode_display' ).trigger( 'display_shortcode' );
                        });
                        $( current_object + ' #cntctfrm_multi_languages_list' ).on( 'change', function() {
                            $( current_object + ' #bws_shortcode_display' ).trigger( 'display_shortcode' );
                        });";
                    } else {
                        $script .= "$( current_object + ' #cntctfrm_languages_list' ).on( 'change', function() {
                            var cntctfrm_get_language = $( current_object + ' #cntctfrm_languages_list option:selected' ).val(),
                                cntctfrm_language = ( cntctfrm_get_language == 'default' ) ? '' : ' lang=' + cntctfrm_get_language,
                                shortcode = '[bestwebsoft_contact_form' + cntctfrm_language + ']';
                            $( current_object + ' #bws_shortcode_display' ).text( shortcode );
                        });";
                    }
                    $script .= "} ) ( jQuery );
                }";
            wp_register_script( 'cntctfrm_bws_shortcode_button_script', '//' );
            wp_enqueue_script( 'cntctfrm_bws_shortcode_button_script' );
            wp_add_inline_script( 'cntctfrm_bws_shortcode_button_script', sprintf( $script ) ); ?>
            <div class="cntctfrm_clear"></div>
		</div>
		<?php return $content;
	}
}
/*pls   */
/* add help tab  */
if ( ! function_exists( 'cntctfrm_add_tabs' ) ) {
	function cntctfrm_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id'            => 'cntctfrm',
			'section'       => '200538909'
		);
		bws_help_tab( $screen, $args );
	}
}
/*  pls*/
/* Function for delete options */
if ( ! function_exists ( 'cntctfrm_delete_options' ) ) {
	function cntctfrm_delete_options() {
		global $wpdb;
		$all_plugins = get_plugins();

		if ( ! array_key_exists( 'contact-form-pro/contact_form_pro.php', $all_plugins ) /*pls */ && ! array_key_exists( 'contact-form-plus/contact-form-plus.php', $all_plugins )/* pls*/ ) {
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
