<?php
/**
 * plugin settings page
 */

if ( ! class_exists( 'Cntctfrm_Settings_Tabs' ) ) {
	class Cntctfrm_Settings_Tabs extends Bws_Settings_Tabs {
		private $cfmlt_is_active, $first_form_id, $multi_options_main, $cf_class;
		private $is_cfmltpr_pro = false;
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $cntctfrm_options, $cntctfrm_plugin_info, $contact_form_multi_active;

			$tabs = array(
				'settings'				=> array( 'label' => esc_html__( 'Settings', 'contact-form-plugin' ) ),
				'additional_settings'	=> array( 'label' => esc_html__( 'Additional Settings', 'contact-form-plugin' ) ),
				'appearance'			=> array( 'label' => esc_html__( 'Appearance', 'contact-form-plugin' ) ),
				'misc'					=> array( 'label' => esc_html__( 'Misc', 'contact-form-plugin' ) ),
				/*pls   */
				'custom_code'			=> array( 'label' => esc_html__( 'Custom Code', 'contact-form-plugin' ) ),
                'license'				=> array( 'label' => esc_html__( 'License Key', 'contact-form-plugin' ) ),
                /*  pls*/
			);

			$addons_check = array();

			$all_plugins = get_plugins();
			if ( 'pro' == cntctfrm_check_cf_multi_active() ) {
				$addons_check['contact-form-multi-pro'] =
					array(
						'name'          => 'Contact Form Multi Pro',
						'slug'          => 'contact-form-multi-pro',
						'basename'      => 'contact-form-multi-pro/contact-form-multi-pro.php'
					);
			} else if ( 'free' == cntctfrm_check_cf_multi_active() ) {
				$addons_check['contact-form-multi'] =
					array(
						'name'          => 'Contact Form Multi',
						'slug'          => 'contact-form-multi',
						'pro_slug'      => 'contact-form-multi-pro',
						'basename'      => 'contact-form-multi/contact-form-multi.php',
						'pro_basename'  => 'contact-form-multi-pro/contact-form-multi-pro.php'
					);
			}

			parent::__construct( array(
				'plugin_basename'		=> $plugin_basename,
				'plugins_info'			=> $cntctfrm_plugin_info,
				'prefix'				=> 'cntctfrm',
				'default_options'		=> cntctfrm_get_option_defaults(),
				'options'				=> $cntctfrm_options,
				'is_network_options'	=> is_network_admin(),
				'tabs'					=> $tabs,                
				'licenses'				=> $addons_check,                
                'doc_link'              => 'https://docs.google.com/document/d/1qZYPJhkSdVyyM6XO5WfiBcTS2Sa9_9UMn4vS2g48JRY/',
                /*pls */
                'wp_slug'				=> 'contact-form-plugin',
                /*  pls*/
			) );

            if ( file_exists( dirname( __FILE__ ) . '/class-custom-field.php' ) ) {
                require_once( dirname(__FILE__) . '/class-custom-field.php' );
                $this->cf_class = new CustomFieldPlus( $args = array( 'prefix' => 'cntctfrm' ) );
            }

			add_action( get_parent_class( $this ) . '_appearance_custom_messages', array( $this, 'appearance_custom_messages' ) );
            add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
			$this->cfmlt_is_active = cntctfrm_check_cf_multi_active();

			$this->multi_options_main = get_option( 'cntctfrmmlt_options_main' );
			if ( $this->multi_options_main ) {
				$this->first_form_id = key( $this->multi_options_main['name_id_form'] );
			}

			if ( $this->is_multisite ) {
				switch_to_blog( 1 );
				$this->upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$this->upload_dir = wp_upload_dir();
			}
			$contact_form_multi_active = cntctfrm_check_cf_multi_active();
		}

		/**
		 * Appearance custom error\message\notice
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function appearance_custom_messages( $save_results ) {
			global $cntctfrm_is_old_php;
			$message = $error = ""; ?>
			<noscript><div class="error below-h2"><p><strong><?php esc_html_e( "Please enable JavaScript in your browser.", 'contact-form-plugin' ); ?></strong></p></div></noscript>
			<?php if ( $cntctfrm_is_old_php ) { ?>
				<div class="error below-h2"><p><strong><?php printf( esc_html__( "Contact Form plugin requires PHP %s or higher. Please contact your hosting provider to upgrade PHP version.", 'contact-form-plugin' ), '5.4.0' ); ?></strong></p></div>
			<?php } ?>

			<div class="updated below-h2" <?php if ( empty( $message ) || "" != $error ) echo "style=\"appearance:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error below-h2" <?php if ( "" == $error ) echo "style=\"appearance:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
		<?php }

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			global $wpdb, $cntctfrm_countries, $cntctfrm_related_plugins, $contact_form_multi_active;

			if ( empty( $cntctfrm_related_plugins ) )
				cntctfrm_related_plugins();
			$error = $message = $notice = '';
			if ( ! function_exists( 'get_plugins' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			if ( isset( $_POST['bws_hide_premium_options'] ) ) {
				$hide_result = bws_hide_premium_options( $this->options );
				$this->options = $hide_result['options'];
			}
            $this->options = apply_filters ( 'cntctfrm_save_additional_options', $this->options );
			if ( isset( $_POST['cntctfrm_width_type'] ) && in_array( $_POST['cntctfrm_width_type'], array( 'default', 'custom' ) ) ) {
				$this->options['width']['type'] = $_POST['cntctfrm_width_type'];
				if ( 'custom' == $_POST['cntctfrm_width_type'] ) {
					$this->options['width']['input_value'] = ( isset( $_POST['cntctfrm_input_width_value'] ) ) ? intval( $_POST[ 'cntctfrm_input_width_value' ] ) : 100;
					$this->options['width']['input_unit'] = ( isset( $_POST['cntctfrm_input_width_unit'] ) && in_array( $_POST['cntctfrm_input_width_unit'], array( '%', 'px' ) ) ) ? $_POST[ 'cntctfrm_input_width_unit' ] : '%';
				}
			}

			$this->options['layout'] = ( isset( $_POST['cntctfrm_layout'] ) ) ? (int) $_POST['cntctfrm_layout'] : 1;

			$layout_first_column_string = stripslashes( sanitize_text_field( $_POST['cntctfrm_layout_first_column'] ) );
			$layout_first_column = explode( ',', $layout_first_column_string );
			$layout_first_column = array_diff( $layout_first_column, array('') );

            $layout_second_column_string = stripslashes( sanitize_text_field( $_POST['cntctfrm_layout_second_column'] ) );
            $layout_second_column = explode( ',', $layout_second_column_string );
			$layout_second_column = array_diff( $layout_second_column, array('') );

			if ( 1 === $this->options['layout'] && ! empty( $layout_second_column ) ) {
				$layout_first_column = array_merge( $layout_first_column, $layout_second_column );
				$layout_second_column = array();
			}

            $this->options['submit_position']          = isset( $_POST['cntctfrm_submit_position'] ) && in_array( $_POST['cntctfrm_submit_position'], array( 'right', 'left' ) ) ? $_POST['cntctfrm_submit_position'] : 'left';
            $this->options['order_fields']['first_column'] = $layout_first_column;
            $this->options['order_fields']['second_column'] = $layout_second_column;

            $this->options['user_email']               = isset( $_POST['cntctfrm_user_email'] ) ? sanitize_text_field( $_POST['cntctfrm_user_email'] ) : $this->options['user_email'];
			if ( isset( $_POST['cntctfrm_custom_email'] ) && preg_match( '|,|', $_POST['cntctfrm_custom_email'] ) ) {
				$cntctfrm_multi_emails = explode( ',', $_POST['cntctfrm_custom_email'] );
				foreach ( $cntctfrm_multi_emails as $cntctfrm_multi_email ) {
					$to[] = sanitize_email( $cntctfrm_multi_email );
				}
				$this->options['custom_email']         = implode( ', ', $to );
			} else {
				$this->options['custom_email']         = isset( $_POST['cntctfrm_custom_email'] ) ? sanitize_email( $_POST['cntctfrm_custom_email'] ) : $this->options['custom_email'];
			}
			$this->options['select_email']             = isset( $_POST['cntctfrm_select_email'] ) && in_array( $_POST['cntctfrm_select_email'], array( 'user', 'custom', 'departments' ) ) ? ( $_POST['cntctfrm_select_email'] ) : 'user';
            $this->options['from_email']               = isset( $_POST['cntctfrm_from_email'] ) && in_array( $_POST['cntctfrm_from_email'], array( 'user', 'custom' ) ) ? ( $_POST['cntctfrm_from_email'] ) : 'user';
            $this->options['custom_from_email']        = isset( $_POST['cntctfrm_custom_from_email'] ) ? sanitize_email( $_POST['cntctfrm_custom_from_email'] ) : $this->options['custom_from_email'];

            $this->options['mail_method']              = isset( $_POST['cntctfrm_mail_method'] ) && in_array( $_POST['cntctfrm_mail_method'], array( 'wp-mail', 'mail' ) ) ? ( $_POST['cntctfrm_mail_method'] ) : 'wp-mail';
            $this->options['from_field']               = isset( $_POST['cntctfrm_from_field'] ) ? sanitize_text_field( $_POST['cntctfrm_from_field'] ) : $this->options['from_field'];
            $this->options['select_from_field']        = isset( $_POST['cntctfrm_select_from_field'] ) && in_array( $_POST['cntctfrm_select_from_field'], array( 'custom', 'user_name' ) ) ? ( $_POST['cntctfrm_select_from_field'] ) : 'custom';
            $this->options['display_name_field']       = isset( $_POST['cntctfrm_display_name_field'] ) ? 1 : 0;
            $this->options['display_address_field']    = isset( $_POST['cntctfrm_display_address_field'] ) ? 1 : 0;
            $this->options['display_phone_field']      = isset( $_POST['cntctfrm_display_phone_field'] ) ? 1 : 0;
            $this->options['attachment']               = isset( $_POST['cntctfrm_attachment'] ) ? 1 : 0;
            $this->options['attachment_explanations']  = isset( $_POST['cntctfrm_attachment_explanations'] ) ? 1 : 0;
            $this->options['send_copy']                = isset( $_POST['cntctfrm_send_copy'] ) ? 1 : 0;
            $this->options['gdpr']                     = isset( $_POST['cntctfrm_gdpr'] ) ? 1 : 0;
            $this->options['gdpr_link']                = isset( $_POST['cntctfrm_gdpr_link'] ) ? esc_url( $_POST['cntctfrm_gdpr_link'] ) : $this->options['gdpr_link'];

			$this->options['delete_attached_file']     = isset( $_POST['cntctfrm_delete_attached_file'] ) ? 1 : 0;

			if ( isset( $_POST['cntctfrm_add_language_button'] ) )
				cntctfrm_add_language();

			if ( isset( $_POST['cntctfrm_delete_button'] ) )
				cntctfrm_remove_language();
			if ( $contact_form_multi_active ) {
				$this->options['display_captcha']          = isset( $_POST['cntctfrm_display_captcha'] ) ? 1 : 0;
				$this->options['display_google_captcha']   = isset( $_POST['cntctfrm_display_google_captcha'] ) ? 1 : 0;
				$this->options['display_subscribe']        = isset( $_POST['cntctfrm_display_subscriber'] ) ? 1 : 0;
				$this->options['save_email_to_db']         = isset( $_POST['cntctfrm_save_email_to_db'] ) ? 1 : 0;
			} else {
				/* Update related plugins options if Contact Form Multi is not active */
				if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) ) {
					$cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] = isset( $_POST['cntctfrm_display_captcha'] ) ? 1 : 0;
					update_option( 'cptch_options', $cntctfrm_related_plugins['captcha']['options'] );
				}

				if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) ) {
					$cntctfrm_related_plugins['google-captcha']['options']['contact_form'] = isset( $_POST['cntctfrm_display_google_captcha'] ) ? 1 : 0;
					update_option( 'gglcptch_options', $cntctfrm_related_plugins['google-captcha']['options'] );
				}

				if ( is_multisite() ) {
					if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
						$cntctfrm_related_plugins['subscriber']['options']['contact_form'] = isset( $_POST['cntctfrm_display_subscriber'] ) ? 1 : 0;
						update_site_option( 'sbscrbr_options', $cntctfrm_related_plugins['subscriber']['options'] );
					}
				} else {
					if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
						$cntctfrm_related_plugins['subscriber']['options']['contact_form'] = isset( $_POST['cntctfrm_display_subscriber'] ) ? 1 : 0;
						update_option( 'sbscrbr_options', $cntctfrm_related_plugins['subscriber']['options'] );
					}
				}

				if ( array_key_exists( 'contact-form-to-db', $cntctfrm_related_plugins ) ) {
					$cntctfrm_related_plugins['contact-form-to-db']['options']['save_messages_to_db'] = isset( $_POST['cntctfrm_save_email_to_db'] ) ? 1 : 0;
					update_option( 'cntctfrmtdb_options', $cntctfrm_related_plugins['contact-form-to-db']['options'] );
				}
			}

			if ( 0 == $this->options['display_name_field'] ) {
				$this->options['required_name_field'] = 0;
			} else {
				$this->options['required_name_field'] = isset( $_POST['cntctfrm_required_name_field'] ) ? 1 : 0;
			}
			if ( 0 == $this->options['display_address_field'] ) {
				$this->options['required_address_field']   = 0;
			} else {
				$this->options['required_address_field']   = isset( $_POST['cntctfrm_required_address_field'] ) ? 1 : 0;
			}
			$this->options['required_email_field'] = 1;
			if ( 0 == $this->options['display_phone_field'] ) {
				$this->options['required_phone_field'] = 0;
			} else {
				$this->options['required_phone_field'] = isset( $_POST['cntctfrm_required_phone_field'] ) ? 1 : 0;
			}
			$this->options['required_subject_field']   = isset( $_POST['cntctfrm_required_subject_field'] ) ? 1 : 0;
			$this->options['required_message_field']   = isset( $_POST['cntctfrm_required_message_field'] ) ? 1 : 0;

			$this->options['required_symbol']          = isset( $_POST['cntctfrm_required_symbol'] ) ? sanitize_text_field( $_POST['cntctfrm_required_symbol'] ) : '*';
			$this->options['html_email']               = isset( $_POST['cntctfrm_html_email'] ) ? 1 : 0;
			$this->options['display_add_info']         = isset( $_POST['cntctfrm_display_add_info'] ) ? 1 : 0;

			$this->options['display_sent_from']        = isset( $_POST['cntctfrm_display_sent_from'] ) ? 1 : 0;
			$this->options['display_date_time']        = isset( $_POST['cntctfrm_display_date_time'] ) ? 1 : 0;
			$this->options['display_coming_from']      = isset( $_POST['cntctfrm_display_coming_from'] ) ? 1 : 0;
			$this->options['display_user_agent']       = isset( $_POST['cntctfrm_display_user_agent'] ) ? 1 : 0;

			if ( 0 == $this->options['display_sent_from'] && 0 == $this->options['display_date_time'] && 0 == $this->options['display_coming_from'] && 0 == $this->options['display_user_agent'] )
				$this->options['display_add_info'] = 0;

			if ( 0 == $this->options['display_add_info'] ) {
				$this->options['display_sent_from']    = 1;
				$this->options['display_date_time']    = 1;
				$this->options['display_coming_from']  = 1;
				$this->options['display_user_agent']   = 1;
			}

			$this->options['change_label']              = isset( $_POST['cntctfrm_change_label'] ) ? 1 : 0;
			$this->options['change_label_in_email']     = isset( $_POST['cntctfrm_change_label_in_email'] ) ? 1 : 0;
			$this->options['active_multi_attachment']   = isset( $_POST['cntctfrm_active_multi_attachment'] ) ? 1 : 0;

			if ( 1 == $this->options['change_label'] ) {
				foreach ( $_POST['cntctfrm_name_label'] as $key => $val ) {
					$this->options['name_label'][ $key ]               = stripcslashes( sanitize_text_field( $_POST['cntctfrm_name_label'][ $key ] ) );
                    $this->options['address_label'][ $key ]            = stripcslashes( sanitize_text_field( $_POST['cntctfrm_address_label'][ $key ] ) );
                    $this->options['email_label'][ $key ]              = stripcslashes( sanitize_email( $_POST['cntctfrm_email_label'][ $key ] ) );
                    $this->options['phone_label'][ $key ]              = stripcslashes( sanitize_text_field( $_POST['cntctfrm_phone_label'][ $key ] ) );
                    $this->options['subject_label'][ $key ]            = stripcslashes( sanitize_text_field( $_POST['cntctfrm_subject_label'][ $key ] ) );
                    $this->options['message_label'][ $key ]            = stripcslashes( sanitize_textarea_field( $_POST['cntctfrm_message_label'][ $key ] ) );
                    $this->options['attachment_label'][ $key ]         = stripcslashes( sanitize_text_field( $_POST['cntctfrm_attachment_label'][ $key ] ) );
                    $this->options['attachment_tooltip'][ $key ]       = stripcslashes( sanitize_text_field( $_POST['cntctfrm_attachment_tooltip'][ $key ] ) );
                    $this->options['send_copy_label'][ $key ]          = stripcslashes( sanitize_text_field( $_POST['cntctfrm_send_copy_label'][ $key ] ) );
                    $this->options['gdpr_label'][ $key ]               = stripcslashes( sanitize_text_field( $_POST['cntctfrm_gdpr_label'][ $key ] ) );
                    $this->options['gdpr_text_button'][ $key ]         = stripcslashes( sanitize_text_field( $_POST['cntctfrm_gdpr_text_button'][ $key ] ) );
                    $this->options['thank_text'][ $key ]               = stripcslashes( sanitize_text_field( $_POST['cntctfrm_thank_text'][ $key ] ) );
                    $this->options['submit_label'][ $key ]             = stripcslashes( sanitize_text_field( $_POST['cntctfrm_submit_label'][ $key ] ) );
                    $this->options['name_error'][ $key ]               = stripcslashes( sanitize_text_field( $_POST['cntctfrm_name_error'][ $key ] ) );
                    $this->options['address_error'][ $key ]            = stripcslashes( sanitize_text_field( $_POST['cntctfrm_address_error'][ $key ] ) );
                    $this->options['email_error'][ $key ]              = stripcslashes( sanitize_text_field( $_POST['cntctfrm_email_error'][ $key ] ) );
                    $this->options['phone_error'][ $key ]              = stripcslashes( sanitize_text_field( $_POST['cntctfrm_phone_error'][ $key ] ) );
                    $this->options['subject_error'][ $key ]            = stripcslashes( sanitize_text_field( $_POST['cntctfrm_subject_error'][ $key ] ) );
                    $this->options['message_error'][ $key ]            = stripcslashes( sanitize_text_field( $_POST['cntctfrm_message_error'][ $key ] ) );
                    $this->options['attachment_error'][ $key ]         = stripcslashes( sanitize_text_field( $_POST['cntctfrm_attachment_error'][ $key ] ) );
                    $this->options['attachment_upload_error'][ $key ]  = stripcslashes( sanitize_text_field( $_POST['cntctfrm_attachment_upload_error'][ $key ] ) );
                    $this->options['attachment_move_error'][ $key ]    = stripcslashes( sanitize_text_field( $_POST['cntctfrm_attachment_move_error'][ $key ] ) );
                    $this->options['attachment_size_error'][ $key ]    = stripcslashes( sanitize_text_field( $_POST['cntctfrm_attachment_size_error'][ $key ] ) );
                    $this->options['captcha_error'][ $key ]            = stripcslashes( sanitize_text_field( $_POST['cntctfrm_captcha_error'][ $key ] ) );
                    $this->options['form_error'][ $key ]               = stripcslashes( sanitize_text_field( $_POST['cntctfrm_form_error'][ $key ] ) );
				}
			} else {
				$option_defaults = cntctfrm_get_option_defaults();

				if ( empty( $this->options['language'] ) ) {
					$this->options['name_label']               = $option_defaults['name_label'];
					$this->options['address_label']            = $option_defaults['address_label'];
					$this->options['email_label']              = $option_defaults['email_label'];
					$this->options['phone_label']              = $option_defaults['phone_label'];
					$this->options['subject_label']            = $option_defaults['subject_label'];
					$this->options['message_label']            = $option_defaults['message_label'];
					$this->options['attachment_label']         = $option_defaults['attachment_label'];
					$this->options['attachment_tooltip']       = $option_defaults['attachment_tooltip'];
					$this->options['send_copy_label']          = $option_defaults['send_copy_label'];
					$this->options['gdpr_label']               = $option_defaults['gdpr_label'];
					$this->options['gdpr_text_button']         = $option_defaults['gdpr_text_button'];
					$this->options['thank_text']               = $_POST['cntctfrm_thank_text'];
					$this->options['submit_label']             = $option_defaults['submit_label'];
					$this->options['name_error']               = $option_defaults['name_error'];
					$this->options['address_error']            = $option_defaults['address_error'];
					$this->options['email_error']              = $option_defaults['email_error'];
					$this->options['phone_error']              = $option_defaults['phone_error'];
					$this->options['subject_error']            = $option_defaults['subject_error'];
					$this->options['message_error']            = $option_defaults['message_error'];
					$this->options['attachment_error']         = $option_defaults['attachment_error'];
					$this->options['attachment_upload_error']  = $option_defaults['attachment_upload_error'];
					$this->options['attachment_move_error']    = $option_defaults['attachment_move_error'];
					$this->options['attachment_size_error']    = $option_defaults['attachment_size_error'];
					$this->options['captcha_error']            = $option_defaults['captcha_error'];
					$this->options['form_error']               = $option_defaults['form_error'];
					foreach ( $this->options['thank_text'] as $key => $val ) {
						$this->options['thank_text'][ $key ] = sanitize_text_field( $val ) ;
					}
				} else {
					$this->options['name_label']['default']                = $option_defaults['name_label']['default'];
					$this->options['address_label']['default']             = $option_defaults['address_label']['default'];
					$this->options['email_label']['default']               = $option_defaults['email_label']['default'];
					$this->options['phone_label']['default']               = $option_defaults['phone_label']['default'];
					$this->options['subject_label']['default']             = $option_defaults['subject_label']['default'];
					$this->options['message_label']['default']             = $option_defaults['message_label']['default'];
					$this->options['attachment_label']['default']          = $option_defaults['attachment_label']['default'];
					$this->options['attachment_tooltip']['default']        = $option_defaults['attachment_tooltip']['default'];
					$this->options['send_copy_label']['default']           = $option_defaults['send_copy_label']['default'];
					$this->options['gdpr_label']['default']                = $option_defaults['gdpr_label']['default'];
					$this->options['gdpr_text_button']['default']          = $option_defaults['gdpr_text_button']['default'];
					$this->options['submit_label']['default']              = $option_defaults['submit_label']['default'];
					$this->options['name_error']['default']                = $option_defaults['name_error']['default'];
					$this->options['address_error']['default']             = $option_defaults['address_error']['default'];
					$this->options['email_error']['default']               = $option_defaults['email_error']['default'];
					$this->options['phone_error']['default']               = $option_defaults['phone_error']['default'];
					$this->options['subject_error']['default']             = $option_defaults['subject_error']['default'];
					$this->options['message_error']['default']             = $option_defaults['message_error']['default'];
					$this->options['attachment_error']['default']          = $option_defaults['attachment_error']['default'];
					$this->options['attachment_upload_error']['default']   = $option_defaults['attachment_upload_error']['default'];
					$this->options['attachment_move_error']['default']     = $option_defaults['attachment_move_error']['default'];
					$this->options['attachment_size_error']['default']     = $option_defaults['attachment_size_error']['default'];
					$this->options['captcha_error']['default']             = $option_defaults['captcha_error']['default'];
					$this->options['form_error']['default']                = $option_defaults['form_error']['default'];

					foreach ( $_POST['cntctfrm_thank_text'] as $key => $val ) {
						$this->options['thank_text'][ $key ] = sanitize_text_field( $_POST['cntctfrm_thank_text'][ $key ] ) ;
					}
				}
			}
			/* if 'FROM' field was changed */
			if ( ( 'custom' == $this->options['from_email'] && 'custom' != $this->options['from_email'] ) ||
				( 'custom' == $this->options['from_email'] && $this->options['custom_from_email'] != $this->options['custom_from_email'] ) ) {
				$notice = esc_html__( "Email 'FROM' field option was changed, which may cause email messages being moved to the spam folder or email delivery failures.", 'contact-form-plugin' );
			}

			$this->options['action_after_send'] = ( isset( $_POST['cntctfrm_action_after_send'] ) && in_array( $_POST['cntctfrm_action_after_send'], array( '1', '0') ) ?( $_POST['cntctfrm_action_after_send'] ) : '1' );
			$this->options['redirect_url'] = isset( $_POST['cntctfrm_redirect_url'] ) ? esc_url( $_POST['cntctfrm_redirect_url'] ) : $this->options['redirect_url'];
			$this->options = array_merge( $this->options, $this->options );

			if ( 0 == $this->options['action_after_send']
				&& ( "" == trim( $this->options['redirect_url'] )
				|| ! filter_var( $this->options['redirect_url'], FILTER_VALIDATE_URL) ) ) {
					$error .= esc_html__(  "If the 'Redirect to page' option is selected then the URL field should be in the following format", 'contact-form-plugin' )." <code>http://your_site/your_page</code>";
					$this->options['action_after_send'] = 1;
			}
			if ( 'user' == $this->options['select_email'] ) {
				if ( false !== get_user_by( 'login', $this->options['user_email'] ) ) {
					/**/
				} else {
					$error .= esc_html__(  "Such user does not exist.", 'contact-form-plugin' );
				}
			} else {
				if ( preg_match( '|,|', $this->options['custom_email'] ) ) {
					$cntctfrm_custom_emails = explode( ',', $this->options['custom_email'] );
				} else {
					$cntctfrm_custom_emails[0] = $this->options['custom_email'];
				}
				foreach ( $cntctfrm_custom_emails as $cntctfrm_custom_email ) {
					if ( $cntctfrm_custom_email == "" || ! is_email( trim( $cntctfrm_custom_email ) ) ) {
						$error .= esc_html__( "Please enter a valid email address in the 'Use this email address' field.", 'contact-form-plugin' );
						break;
					}
				}
			}
			if ( 'custom' == $this->options['from_email'] ) {
				if ( "" == $this->options['custom_from_email']
					|| ! is_email( trim( $this->options['custom_from_email'] ) ) ) {
					$error .= esc_html__( "Please enter a valid email address in the 'FROM' field.", 'contact-form-plugin' );
				}
			}

			if ( '' == $error ) {
				if ( 'pro' == $contact_form_multi_active && $options_main = get_option( 'cntctfrmmltpr_options_main' ) ) {
					if ( $options_main['id_form'] !== $GLOBALS['cntctfrmmlt_id_form'] )
						add_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $this->options );
					else if ( $options_main['id_form'] == $GLOBALS['cntctfrmmlt_id_form'] )
						update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $this->options );
				} elseif ( $contact_form_multi_active ) {
					$options_main = get_option( 'cntctfrmmlt_options_main' );

					if ( $options_main['id_form'] !== $GLOBALS['cntctfrmmlt_id_form'] )
						add_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $this->options );
					else if ( $options_main['id_form'] == $GLOBALS['cntctfrmmlt_id_form'] )
						update_option( 'cntctfrmmlt_options_' . $options_main['id_form'], $this->options );
				} else {
					update_option( 'cntctfrm_options', $this->options );
				}
				$message = esc_html__( "Settings saved.", 'contact-form-plugin' );
			} else {
				$error .=  ' ' . esc_html__( "Settings are not saved.", 'contact-form-plugin' );
			}

			return compact( 'message', 'notice', 'error' );
		}

		public function tab_settings() {
			global $wpdb, $wp_version, $cntctfrm_plugin_info, $bstwbsftwppdtplgns_options, $cntctfrm_countries, $cntctfrm_lang_codes, $cntctfrm_related_plugins, $contact_form_multi_active;

			if ( empty( $cntctfrm_related_plugins ) ) {
				cntctfrm_related_plugins();
			}
			$userslogin = get_users( 'blog_id=' . absint( $GLOBALS['blog_id'] ) . '&role=administrator' );
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$all_plugins = get_plugins();?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Contact Form Settings', 'contact-form-plugin' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div>
				<p><?php esc_html_e( "If you leave the fields empty, the messages will be sent to the email address specified during registration.", 'contact-form-plugin' ); ?></p>
				<table class="form-table" style="width:auto;">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( "The user's email address", 'contact-form-plugin' ); ?> </th>
						<td colspan="2">
							<label>
								<input type="radio" id="cntctfrm_select_email_user" name="cntctfrm_select_email" value="user" <?php checked( 'user', $this->options['select_email'] ); ?> />
								<select name="cntctfrm_user_email">
									<option disabled><?php esc_html_e( "Select a username", 'contact-form-plugin' ); ?></option>
									<?php foreach ( $userslogin as $key => $value ) {
										if ( isset( $value->data ) ) {
											if ( $value->data->user_email != '' ) { ?>
												<option value="<?php echo $value->data->user_login; ?>" <?php selected( $value->data->user_login, $this->options['user_email'] ); ?>><?php echo $value->data->user_login; ?></option>
											<?php }
										} else {
											if ( $value->user_email != '' ) { ?>
												<option value="<?php echo $value->user_login; ?>" <?php selected( $value->user_login, $this->options['user_email'] ); ?>><?php echo $value->user_login; ?></option>
											<?php }
										}
									} ?>
								</select>
								<div class="bws_info cntctfrm_info"><?php esc_html_e( 'Select a username of the person who should get the messages from the contact form.', 'contact-form-plugin' ); ?></div>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" style="width:200px;"><?php esc_html_e( "Use this email address", 'contact-form-plugin' ); ?></th>
						<td colspan="2">
							<label>
								<input type="radio" id="cntctfrm_select_email_custom" name="cntctfrm_select_email" value="custom" <?php checked( 'custom', $this->options['select_email'] ); ?>/>
								<input type="text" maxlength="500" name="cntctfrm_custom_email" value="<?php echo $this->options['custom_email']; ?>" />
								<div class="bws_info cntctfrm_info"><?php esc_html_e( 'Enter multiple email addresses separated by comma for receiving messages.', 'contact-form-plugin' ); ?>.</div>
							</label>
						</td>
					</tr>
				</table>
                <!-- pls -->
				<?php if ( ! $this->hide_pro_tabs ) { ?>
					<div class="bws_pro_version_bloc">
						<div class="bws_pro_version_table_bloc">
							<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'contact-form-plugin' ); ?>"></button>
							<div class="bws_table_bg"></div>
							<table class="form-table bws_pro_version">
								<tr valign="top">
									<th scope="row"><?php esc_html_e( "Add department selectbox to the contact form", 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<input type="radio" name="cntctfrm_select_email" value="departments" disabled="disabled" />
										<div><img src="<?php echo plugins_url( '../images/pro_screen_1.png', __FILE__ ); ?>" alt="" /></div>
									</td>
								</tr>
							</table>
						</div>
						<?php $this->bws_pro_block_links(); ?>
					</div>
				<?php } ?>
                <!-- end pls -->
				<table class="form-table" style="width:auto;">
					<tr valign="top">
						<th scope="row" style="width:200px;"><?php esc_html_e( "Save emails to the database", 'contact-form-plugin' ); ?> </th>
						<td colspan="2">
							<?php if ( array_key_exists( 'contact-form-to-db/contact_form_to_db.php', $all_plugins ) || array_key_exists( 'contact-form-to-db-pro/contact_form_to_db_pro.php', $all_plugins ) ) {
								if ( array_key_exists( 'contact-form-to-db', $cntctfrm_related_plugins ) ) {
									if ( false == $this->cfmlt_is_active ) {
										$save_emails = ! empty( $cntctfrm_related_plugins['contact-form-to-db']['options']['save_messages_to_db'] ) ? true : false;
									} else {
										$save_emails = ! empty( $this->options['save_email_to_db'] ) ? true : false;
									}
									if (  false == $this->cfmlt_is_active || ! empty( $cntctfrm_related_plugins['contact-form-to-db']['options']['save_messages_to_db'] ) ) { ?>
										<label><input type="checkbox" name="cntctfrm_save_email_to_db" value="1" <?php checked( $save_emails ); ?> />
											<span class="bws_info"> <?php esc_html_e( 'Using', 'contact-form-plugin' ); ?>
												<a href="<?php echo self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['contact-form-to-db']['settings_page'] ); ?>" target="_blank">Contact Form to DB by BestWebSoft</a>
											</span>
										</label>
									<?php } else { ?>
										<label><input type="checkbox" name="cntctfrm_save_email_to_db" value="1" disabled="disabled" /></label>
										<span class="bws_info">&nbsp;<?php esc_html_e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
											<?php printf( '<a href="%s" target="_blank"> Contact Form to DB %s</a>&nbsp;',
												self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['contact-form-to-db']['settings_page'] ),
                                                esc_html__( 'settings page', 'contact-form-plugin' ) ); ?>
										</span>
									<?php }
								} else { ?>
									<label><input disabled="disabled" type="checkbox" name="cntctfrm_save_email_to_db" value="1" />
										<span class="bws_info"><?php esc_html_e( 'Using', 'contact-form-plugin' ); ?> Contact Form to DB by BestWebSoft
										<?php printf( '<a href="%s" target="_blank">%s Contact Form to DB</a>', self_admin_url( 'plugins.php' ), esc_html__( 'Activate', 'contact-form-plugin' ) ); ?>
										</span>
									</label>
								<?php }
							} else { ?>
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_save_email_to_db" value="1" />
									<span class="bws_info"><?php esc_html_e( 'Using', 'contact-form-plugin' ); ?> Contact Form to DB by BestWebSoft
										<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/contact-form-to-db/?k=b3bfaac63a55128a35e3f6d0a20dd43d&amp;pn=3&amp;v=%s&amp;wp_v=%s"> %s Contact Form to DB</a>', $cntctfrm_plugin_info["Version"], $wp_version, esc_html__( 'Download', 'contact-form-plugin' ) ); ?>
									</span>
								</label>
							<?php } ?>
						</td>
					</tr>
				</table>
			</div>
		<?php }

		public function tab_additional_settings() {
			global $cntctfrm_related_plugins, $cntctfrm_lang_codes, $cntctfrm_plugin_info, $wp_version, $contact_form_multi_active;
			if ( empty( $cntctfrm_related_plugins ) ) {
				cntctfrm_related_plugins();
			}
			$all_plugins = get_plugins();?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Additional Settings', 'contact-form-plugin' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table" style="width:auto;">
				<tr>
					<th scope="row"><?php esc_html_e( 'Sending method', 'contact-form-plugin' ); ?></th>
					<td colspan="2">
						<fieldset>
							<label>
								<input type='radio' name='cntctfrm_mail_method' value='wp-mail' <?php checked( 'wp-mail', $this->options['mail_method'] ); ?> />
								<?php esc_html_e( 'Wp-mail', 'contact-form-plugin' ); ?>
                                <div class="bws_info" style="padding-left: 20px;"><?php esc_html_e( 'You can use the Wordpress wp_mail function for mailing', 'contact-form-plugin' ); ?></div>
							</label>
							<br />
							<label>
								<input type='radio' name='cntctfrm_mail_method' value='mail' <?php checked( 'mail', $this->options['mail_method'] ); ?> />
								<?php esc_html_e( 'Mail', 'contact-form-plugin' ); ?>
                                <div class="bws_info" style="padding-left: 20px;"><?php esc_html_e( 'You can use the PHP mail function for mailing', 'contact-form-plugin' ); ?></div>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "'FROM' field", 'contact-form-plugin' ); ?></th>
					<td class="cntctfrm_td_name" style="vertical-align: top;">
						<table id="cntctfrm_table_from_name">
							<tbody>
								<tr>
									<td colspan="2"><?php esc_html_e( "Name", 'contact-form-plugin' ); ?></td>
								</tr>
								<tr>
									<td class="cntctfrm_radio_from_name"><input type="radio" id="cntctfrm_select_from_custom_field" name="cntctfrm_select_from_field" value="custom" <?php checked( 'custom', $this->options['select_from_field'] ); ?> /></td>
									<td><input type="text" name="cntctfrm_from_field" value="<?php echo stripslashes( $this->options['from_field'] ); ?>" size="18" maxlength="100" /></td>
								</tr>
								<tr>
									<td class="cntctfrm_radio_from_name">
										<input type="radio" id="cntctfrm_select_from_field" name="cntctfrm_select_from_field" value="user_name" <?php checked( 'user_name', $this->options['select_from_field'] ); ?>/>
									</td>
									<td>
										<label for="cntctfrm_select_from_field"><?php esc_html_e( "User name", 'contact-form-plugin' ); ?></label>
										<div class="bws_help_box dashicons dashicons-editor-help">
											<div class="bws_hidden_help_text" style="min-width: 200px;"><?php echo esc_html__( "The name of the user who fills the form will be used in the field 'From'.", 'contact-form-plugin' ); ?></div>
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
									<td colspan="2"><?php esc_html_e( "Email", 'contact-form-plugin' ); ?></td>
								</tr>
								<tr>
									<td class="cntctfrm_radio_from_email"><input type="radio" id="cntctfrm_from_custom_email" name="cntctfrm_from_email" value="custom" <?php checked( 'custom', $this->options['from_email'] ); ?> /></td>
									<td><input type="text" name="cntctfrm_custom_from_email" value="<?php echo $this->options['custom_from_email']; ?>" maxlength="100" /></td>
								</tr>
								<tr>
									<td class="cntctfrm_radio_from_email">
										<input type="radio" id="cntctfrm_from_email" name="cntctfrm_from_email" value="user" <?php checked( 'user', $this->options['from_email'] ); ?> />
									</td>
									<td>
										<label for="cntctfrm_from_email"><?php esc_html_e( "User email", 'contact-form-plugin' ); ?></label>
									<div class="bws_help_box dashicons dashicons-editor-help">
										<div class="bws_hidden_help_text" style="min-width: 200px;"><?php echo esc_html__( "The email address of the user who fills the form will be used in the field 'From'.", 'contact-form-plugin' ); ?></div>
									</div>
									</td>
								</tr>
								<tr>
									<td>
									</td>
									<td>
										<div>
											<span class="bws_info"><?php esc_html_e( "If this option is changed, email messages may be moved to the spam folder or email delivery failures may occur.", 'contact-form-plugin' ); ?></span>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "Required symbol", 'contact-form-plugin' ); ?></th>
					<td colspan="2">
						<input type="text" id="cntctfrm_required_symbol" name="cntctfrm_required_symbol" value="<?php echo $this->options['required_symbol']; ?>" maxlength="100" />
					</td>
				</tr>
			</table>
			<br />
			<table class="cntctfrm_settings_table" style="width: auto;">
				<thead>
					<tr valign="top" <?php do_action( 'cntctfrm_disabled_row', $this->options ) ?>>
						<th scope="row"><?php esc_html_e( "Fields", 'contact-form-plugin' ); ?></th>
						<th><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></th>
						<th><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></th>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
							<th><?php esc_html_e( "Visible", 'contact-form-plugin' ); ?></th>
							<th><?php esc_html_e( "Disabled for editing", 'contact-form-plugin' ); ?></th>
							<th scope="row" ><?php esc_html_e( "Field's default value", 'contact-form-plugin' ); ?></th>
						<?php } ?>
                        <!-- end pls -->
                        <?php do_action( 'cntctfrm_display_settings_table_header' ) ?>
					</tr>
				</thead>
				<tbody>
                    <!-- pls -->
					<?php if ( ! $this->hide_pro_tabs ) { ?>
						<tr valign="top" >
							<td><?php esc_html_e( "Department selectbox", 'contact-form-plugin' ); ?></td>
                            <td class="bws_pro_version">
                                <label><input disabled="disabled" type="checkbox" name="cntctfrm_display_selectbox" value="1" />
                                    <span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
                            </td>
							<td class="bws_pro_version">
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_required_department_field" value="1" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
						</tr>
					<?php } ?>
                    <!-- end pls -->
					<tr valign="top">
						<td><?php esc_html_e( "Name", 'contact-form-plugin' ); ?></td>
						<td>
							<label><input <?php do_action( 'cntctfrm_add_class_checkbox_disabled_row') ?> type="checkbox" name="cntctfrm_display_name_field" value="1" <?php checked( '1', $this->options['display_name_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
						</td>
						<td>
							<label><input type="checkbox" id="cntctfrm_required_name_field" name="cntctfrm_required_name_field" value="1" <?php checked( '1', $this->options['required_name_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
						</td>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
							<td class="bws_pro_version">
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_visible_name" value="1" checked="checked" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Visible", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_disabled_name" value="1" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<input disabled="disabled" type="checkbox" name="cntctfrm_default_name" value="1" />
								<?php esc_html_e( "Use User's name as a default value if the user is logged in.", 'contact-form-plugin' ); ?><br />
								<span class="bws_info" style="padding-left: 20px;"><?php esc_html_e( "'Visible' and 'Disabled for editing' options will be applied only to logged-in users.", 'contact-form-plugin' ); ?></span>
							</td>
						<?php } ?>
                        <!-- end pls -->
                        <?php do_action( 'cntctfrm_display_settings_table_name', $this->options ) ?>
					</tr>
                    <!-- pls -->
					<?php if ( ! $this->hide_pro_tabs ) { ?>
						<tr valign="top">
							<td><?php esc_html_e( "Location selectbox", 'contact-form-plugin' ); ?></td>
							<td class="bws_pro_version">
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_selectbox" value="1" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_required_selectbox" value="1" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version">
								<label>
									<span><?php esc_html_e( "Field's default value", 'contact-form-plugin' ); ?></span>
									<input disabled="disabled" type="file" name="cntctfrm_default_location" />
								</label>
							</td>
						</tr>
					<?php } ?>
                    <!-- end pls -->
					<tr valign="top" <?php do_action( 'cntctfrm_disabled_row_address', $this->options ) ?>>
						<td><?php esc_html_e( "Address", 'contact-form-plugin' ); ?></td>
						<td>
							<label><input <?php do_action( 'cntctfrm_add_class_checkbox_disabled_row') ?> type="checkbox" id="cntctfrm_display_address_field" name="cntctfrm_display_address_field" value="1" <?php checked( '1', $this->options['display_address_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plus' ); ?></span></label>
						</td>
						<td>
							<label><input type="checkbox" id="cntctfrm_required_address_field" name="cntctfrm_required_address_field" value="1" <?php checked( '1', $this->options['required_address_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
						</td>
                        <?php do_action( 'cntctfrm_display_settings_table_emptiness' ) ?>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
						<?php } ?>
                        <!-- end pls -->
					</tr>
                    <?php do_action( 'cntctfrm_display_settings_table_email', $this->options ) ?>
                    <!-- pls -->
			        <?php if ( ! $this->hide_pro_tabs ) { ?>
					    <tr valign="top">
						    <td><?php esc_html_e( "Email Address", 'contact-form-plugin' ); ?></td>
                            <td class="bws_pro_version">
                                <label><input disabled="disabled" checked="checked" type="checkbox" name="cntctfrm_display_email" value="1" />
                                    <span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
                            </td>
                            <td class="bws_pro_version">
                                <label><input disabled="disabled" checked="checked" type="checkbox" name="cntctfrm_required_email" value="1" />
                                    <span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
                            </td>
							<td class="bws_pro_version">
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_visible_email" value="1" checked="checked" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Visible", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_disabled_email" value="1" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<input disabled="disabled" type="checkbox" name="cntctfrm_default_email" value="1" />
									<?php esc_html_e( "Use User's email as a default value if the user is logged in.", 'contact-form-plugin' ); ?><br />
								<span class="bws_info" style="padding-left: 20px;"><?php esc_html_e( "'Visible' and 'Disabled for editing' options will be applied only to logged-in users.", 'contact-form-plugin' ); ?></span>
							</td>
					    </tr>
				    <?php } ?>
                    <!-- end pls -->
					<tr valign="top" <?php do_action( 'cntctfrm_disabled_row_phone', $this->options ) ?>>
						<td><?php esc_html_e( "Phone number", 'contact-form-plugin' ); ?></td>
						<td>
							<label><input <?php do_action( 'cntctfrm_add_class_checkbox_disabled_row') ?> type="checkbox" id="cntctfrm_display_phone_field" name="cntctfrm_display_phone_field" value="1" <?php checked( '1', $this->options['display_phone_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
						</td>
						<td>
							<label><input type="checkbox" id="cntctfrm_required_phone_field" name="cntctfrm_required_phone_field" value="1" <?php checked( '1', $this->options['required_phone_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
						</td>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
                            <td class="bws_pro_version">
                                <input disabled="disabled" type="text" name="cntctfrm_phone_mask" value="" />
                                <div class="bws_info" style="word-break: break-word;"><?php printf( esc_html__( 'Specify a mask which will be used for the phone validation, where * is a number. Use only the following symbols: %s', 'contact-form-plugin' ), '* - ( ) +' ); ?></div>
                            </td>
						<?php } ?>
                        <!-- end pls -->
                        <?php do_action( 'cntctfrm_display_settings_table_emptiness' ) ?>
					</tr>
					<tr valign="top" <?php do_action( 'cntctfrm_disabled_row_subject', $this->options ) ?>>
						<td><?php esc_html_e( "Subject", 'contact-form-plugin' ); ?></td>
                        <?php do_action( 'cntctfrm_display_settings_table_subject_used', $this->options ) ?>
                        <!-- pls -->
			            <?php if ( ! $this->hide_pro_tabs ) { ?>
                            <td class="bws_pro_version">
                                <label><input disabled="disabled" checked="checked" type="checkbox" name="cntctfrm_display_subject" value="1" />
                                    <span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
                            </td>
                        <?php } ?>
                        <!-- end pls -->
						<td>
							<label><input type="checkbox" id="cntctfrm_required_subject_field" name="cntctfrm_required_subject_field" value="1" <?php checked( '1', $this->options['required_subject_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
						</td>
                        <?php do_action( 'cntctfrm_display_settings_table_subject', $this->options ) ?>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
							<td class="bws_pro_version">
								<label><input class="subject" disabled="disabled" type="checkbox" name="cntctfrm_visible_subject" value="1" checked="checked" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Visible", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<label><input class="subject" disabled="disabled" type="checkbox" name="cntctfrm_disabled_subject" value="1" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<label>
									<span><?php esc_html_e( "Field's default value", 'contact-form-plugin' ); ?></span>
									<input class="subject" disabled="disabled" type="text" name="cntctfrm_default_subject" value="" />
								</label>
							</td>
						<?php } ?>
                        <!-- end pls -->
					</tr>
					<tr valign="top" <?php do_action( 'cntctfrm_disabled_row_message', $this->options ) ?>>
						<td><?php esc_html_e( "Message", 'contact-form-plugin' ); ?></td>
                        <?php do_action( 'cntctfrm_display_settings_table_message_used', $this->options ) ?>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
                            <td class="bws_pro_version">
                                <label><input disabled="disabled" checked="checked" type="checkbox" name="cntctfrm_display_message" value="1" />
                                    <span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
                            </td>
						<?php } ?>
                        <!-- end pls -->
						<td>
							<label><input type="checkbox" id="cntctfrm_required_message_field" name="cntctfrm_required_message_field" value="1" <?php checked( '1', $this->options['required_message_field'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Required", 'contact-form-plugin' ); ?></span></label>
						</td>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
							<td class="bws_pro_version">
								<label><input class="message" disabled="disabled" type="checkbox" name="cntctfrm_visible_message" value="1" checked="checked" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Visible", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<label><input class="message" disabled="disabled" type="checkbox" name="cntctfrm_disabled_message" value="1" />
								<span class="cntctfrm_mobile_title"><?php esc_html_e( "Disabled for editing", 'contact-form-plugin' ); ?></span></label>
							</td>
							<td class="bws_pro_version">
								<label>
									<span><?php esc_html_e( "Field's default value", 'contact-form-plugin' ); ?></span>
									<input class="message" disabled="disabled" type="text" name="cntctfrm_default_message" value="" />
								</label>
							</td>
						<?php } ?>
                        <!-- end pls -->
                        <?php do_action( 'cntctfrm_display_settings_table_message', $this->options ) ?>
					</tr>
					<tr valign="top" <?php do_action( 'cntctfrm_disabled_row_attachment', $this->options ) ?>>
						<td>
							<?php esc_html_e( "Attachment block", 'contact-form-plugin' ); ?>
							<div class="bws_help_box dashicons dashicons-editor-help">
								<div class="bws_hidden_help_text" style="min-width: 200px;"><?php echo esc_html__( "Users can attach the following file formats", 'contact-form-plugin' ) . ": html, txt, css, gif, png, jpeg, jpg, tiff, bmp, ai, eps, ps, csv, rtf, pdf, doc, docx, xls, xlsx, zip, rar, wav, mp3, ppt, aar, sce"; ?></div>
							</div>
						</td>
						<td>
							<label><input <?php do_action( 'cntctfrm_add_class_checkbox_disabled_row') ?> type="checkbox" id="cntctfrm_attachment" name="cntctfrm_attachment" value="1" <?php checked( '1', $this->options['attachment'] ); ?> />
							<span class="cntctfrm_mobile_title"><?php esc_html_e( "Used", 'contact-form-plugin' ); ?></span></label>
						</td>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
                            <td class="bws_pro_version"></td>
                            <td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
							<td class="bws_pro_version"></td>
						<?php } else { ?>
                        <!-- end pls -->
                            <td></td>
                        <!-- pls -->
                        <?php } ?>
                        <!-- end pls -->
                        <?php do_action( 'cntctfrm_display_settings_table_emptiness' ) ?>
					</tr>
                    <tr valign="top" class="cntctfrm-multi-attachment" <?php if ( ! $this->options['attachment'] ) echo 'style="display:none";' ?> >
                        <td>
                            <?php esc_html_e( 'Multi-attachment', 'contact-form-plugin' ); ?>
                            <div class="bws_help_box dashicons dashicons-editor-help">
                                <div class="bws_hidden_help_text" style="min-width: 200px;"><?php esc_html_e( "Enable to multiple file selection", 'contact-form-plugin' ); ?></div>
                            </div>
                        </td>
                        <td>
                            <label class="bws_info"><input type="checkbox" name="cntctfrm_active_multi_attachment" value="1" <?php checked( '1', $this->options['active_multi_attachment'] ); ?> /></label>
                        </td>
                        <!-- pls -->
	                    <?php if ( ! $this->hide_pro_tabs ) { ?>
                            <td class="bws_pro_version"></td>
                            <td class="bws_pro_version"></td>
                            <td class="bws_pro_version"></td>
                            <td class="bws_pro_version"></td>
                        <?php } else { ?>
                        <!-- end pls -->
                            <td></td>
                        <!-- pls -->
                        <?php } ?>
                        <!-- end pls -->
                        <?php do_action( 'cntctfrm_display_settings_table_emptiness' ) ?>
                    </tr>
                    <?php do_action( 'cntctfrm_get_these_custom_fields', $this->cf_class ); ?>
				</tbody>
			</table>
            <br />
            <?php do_action( 'cntctfrm_get_create_new_button', $this->cf_class ); ?>
            <!-- pls -->
            <?php if( ! $this->hide_pro_tabs ) { ?>
                <div class="bws_pro_version_bloc">
                    <div class="bws_pro_version_table_bloc">
                        <button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'contact-form-plugin' ); ?>"></button>
                        <div class="bws_table_bg"></div>
                        <div class="bws_pro_version">
                            <table id="cstmfld_hidden_field_table" class="cntctfrm_settings_table" style="width: 100%;">
                                <thead>
                                <tr valign="top">
                                    <th scope="row"><?php esc_html_e( 'Title', 'contact-form-plugin' ); ?></th>
                                    <th><?php esc_html_e( 'Field Type', 'contact-form-plugin' ); ?></th>
                                    <th><?php esc_html_e( 'Used', 'contact-form-plugin' ); ?></th>
                                    <th><?php esc_html_e( 'Required', 'contact-form-plugin' ); ?></th>
                                    <th><?php esc_html_e( 'Visible', 'contact-form-plugin' ); ?></th>
                                    <th><?php esc_html_e( 'Disabled for editing', 'contact-form-plugin' ); ?></th>
                                    <th scope="row"><?php esc_html_e( 'Field\'s default/available value', 'contact-form-plugin' ); ?></th>
                                    <!--<th scope="row"></th>-->
                                </tr>
                                </thead>
                                <tbody>
                                <tr valign="top">
                                    <td><label><input disabled="disabled" class="subject cstmfld_add_new_title" type="text" maxlength="250" name="cntctfrm_title" value=""></label></td>
                                    <td>
                                        <select disabled="disabled" name="cntctfrm_add_new_field_type" class="cstmfld_add_new_field_type" style="width: 100%;">
                                            <option value="1">text</option>
                                            <option value="2">textarea</option>
                                            <option value="3">checkbox</option>
                                            <option value="4">radio</option>
                                            <option value="6">date</option>
                                            <option value="7">time</option>
                                            <option value="9">number</option>
                                        </select>
                                    </td>
                                    <td><label><input disabled="disabled" class="cntctfrm_type cstmfld_add_new_is_used" type="checkbox" name="cntctfrm_display" value="1" checked="checked"></label></td>
                                    <td><label><input disabled="disabled" type="checkbox" class="cstmfld_add_new_is_required" name="cntctfrm_required" value="1"></label></td>
                                    <td><label><input disabled="disabled" class="name cstmfld_add_new_is_visible" type="checkbox" name="cntctfrm_visible" value="1"></label></td>
                                    <td><label><input disabled="disabled" class="name cstmfld_add_new_is_disabled_editing" type="checkbox" name="cntctfrm_disabled" value="1"></label></td>
                                    <td class="cstmfld-default-value"><label><input disabled="disabled" class="subject cstmfld_add_new_default_value cstmfld_add_new_default_value" type="text" maxlength="250" name="cntctfrm_default" value=""></label></td>
                                </tr>
                                </tbody>
                            </table>
                            <button disabled="disabled" class="button button-primary cstmfld_create_new_field" style="width: 100%;" name="cstmfld_create_new_field"><?php esc_html_e( 'Create Field', 'contact-form-plugin' ); ?></button>
                            <table class="form-table" style="width:auto;">
                                <tbody>
                                <tr>
                                    <th scope="row">
	                                    <?php esc_html_e( 'Google Analytics Measurement ID', 'contact-form-plugin' ); ?>
                                    </th>
                                    <td>
                                        <input disabled="disabled" type="text" name="cntctfrm_tracking_id" value="" />
                                        <div class="bws_info">
	                                        <?php  printf( esc_html__( 'Enter your %sMeasurement ID%s to track contact form submissions in Google Analytics.', 'contact-form-plugin' ), '<a href="https://support.google.com/analytics/answer/9304153?hl=en&ref_topic=9303319">', '</a>' ); ?><br />
	                                        <?php  esc_html_e( 'Google Analytics version 4 is required.', 'contact-form-plugin' ) ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->bws_pro_block_links(); ?>
                </div>
            <?php } ?>
            <!-- end pls -->
			<table class="form-table" style="width:auto;">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "Add to the form", 'contact-form-plugin' ); ?></th>
					<td colspan="3"><fieldset>
                        <div id="cntctfrm-attachment-explanations" style="clear: both;<?php if ( ! $this->options['attachment'] ) echo ' display:none;' ?>">
							<label>
								<input type="checkbox" id="cntctfrm_attachment_explanations" name="cntctfrm_attachment_explanations" value="1" <?php checked( '1' == $this->options['attachment_explanations'] && '1' == $this->options['attachment'] ); ?> />
								<?php esc_html_e( "Tips below the Attachment", 'contact-form-plugin' ); ?>
							</label>
							<?php echo bws_add_help_box( '<img src="' . plugins_url( '../images/tooltip_attachment_tips.png', __FILE__ ) . '" />', 'bws-hide-for-mobile bws-auto-width' ); ?>
						</div>
						<div>
							<label>
								<input type="checkbox" id="cntctfrm_send_copy" name="cntctfrm_send_copy" value="1" <?php checked( '1', $this->options['send_copy'] ); ?> />
								<?php esc_html_e( "'Send me a copy' block", 'contact-form-plugin' ); ?>
							</label>
							<?php echo bws_add_help_box( '<img src="' . plugins_url( '../images/tooltip_sendme_block.png', __FILE__ ) . '" />', 'bws-hide-for-mobile bws-auto-width' ); ?>
						</div>
						<div>
							<label>
								<input type="checkbox" id="cntctfrm_gdpr" name="cntctfrm_gdpr" value="1" <?php checked( '1', $this->options['gdpr'] ); ?> />
								<?php esc_html_e( "GDPR Compliance", 'contact-form-plugin' ); ?>
							</label>
						</div>
						<div id="cntctfrm_gdpr_link_options" >
							<label class="cntctfrm_privacy_policy_text" >
								<?php esc_html_e( "Link to Privacy Policy Page", 'contact-form-plugin' ); ?>
								<input type="url" id="cntctfrm_gdpr_link" name="cntctfrm_gdpr_link" value="<?php echo $this->options['gdpr_link']; ?>" />
							</label>
						</div>
						<div style="clear: both; white-space: nowrap;">
							<?php if ( array_key_exists( 'subscriber/subscriber.php', $all_plugins ) || array_key_exists( 'subscriber-pro/subscriber-pro.php', $all_plugins ) ) {
								if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
									if ( ! $contact_form_multi_active ) {
										$display_subscriber = ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] ) ? true : false;
									} else {
										$display_subscriber = ! empty( $this->options['display_subscribe'] ) ? true : false;
									}
									if ( ! $contact_form_multi_active || ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] ) ) { ?>
										<label><input type="checkbox" name="cntctfrm_display_subscriber" value="1" <?php checked( $display_subscriber ); ?> /> Subscriber by BestWebSoft</label>
									<?php } else { ?>
										<label><input type="checkbox" name="cntctfrm_display_subscriber" value="1" disabled="disabled" <?php checked( $display_subscriber ); ?> /> Subscriber by BestWebSoft</label>
										<span class="bws_info">&nbsp;(<?php esc_html_e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
											<?php printf( '<a href="%s" target="_blank"> Subscriber %s</a>&nbsp;)',
												network_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['subscriber']['settings_page'] ),
                                                esc_html__( 'settings page', 'contact-form-plugin' ) ); ?>
										</span>
									<?php }
								} else { ?>
									<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_subscriber" value="1" <?php checked( isset( $this->options['display_subscribe'] ) && 1 == $this->options['display_subscribe'] ); ?> /> Subscriber by BestWebSoft</label>
									<span class="bws_info">
										<?php if ( ! is_multisite() ) {
											printf( '<a href="%s" target="_blank"> %s Subscriber</a>', admin_url( 'plugins.php' ), esc_html__( 'Activate', 'contact-form-plugin' ) );
										} else {
											printf( '<a href="%s" target="_blank"> %s Subscriber</a>', network_admin_url( 'plugins.php' ), esc_html__( 'Activate for network', 'contact-form-plugin' ) );
										} ?>
									</span>
								<?php }
							} else { ?>
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_subscriber" value="1" />   Subscriber by BestWebSoft</label>
								<span class="bws_info">
									<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/subscriber/?k=a9dfd3fa8513784c36622993b350b19e&amp;pn=77&amp;v=%s&amp;wp_v=%s" target="_blank">%s Subscriber</a>', $cntctfrm_plugin_info["Version"], $wp_version, esc_html__( 'Download', 'contact-form-plugin' ) ); ?>
								</span>
							<?php } ?>
						</div>
						<div style="clear: both; white-space: nowrap;">
							<?php if ( array_key_exists( 'captcha-bws/captcha-bws.php', $all_plugins ) || array_key_exists( 'captcha-plus/captcha-plus.php', $all_plugins ) || array_key_exists( 'captcha-pro/captcha_pro.php', $all_plugins ) ) {
								if ( array_key_exists( 'captcha', $cntctfrm_related_plugins ) ) {
									$captcha_enabled = ! empty( $cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] ) ? true : false;
									if ( ! $contact_form_multi_active ) {
										$display_captcha = $captcha_enabled;
									} else {
										$display_captcha = ! empty( $this->options['display_captcha'] ) ? true : false;
									}

									if ( ! $contact_form_multi_active ) { ?>
										<label><input type="checkbox" name="cntctfrm_display_captcha" value="1" <?php checked( $display_captcha ); ?> /> Captcha by BestWebSoft </label>
									<?php } else {
										if ( $captcha_enabled ) { ?>
											<label><input type="checkbox" name="cntctfrm_display_captcha" value="1" <?php checked( $display_captcha ); ?> /> Captcha by BestWebSoft </label>
										<?php } else { ?>
											<label>
												<input type="checkbox" name="cntctfrm_display_captcha" value="1" disabled="disabled" <?php checked( $display_captcha ); ?> /> Captcha by BestWebSoft
												<span class="bws_info">&nbsp;(<?php esc_html_e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
													<?php printf( '<a href="%s" target="_blank"> Captcha %s</a>&nbsp;)',
													self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['captcha']['settings_page'] ),
                                                        esc_html__( 'settings page', 'contact-form-plugin' ) ); ?>
												</span>
											</label>
										<?php }
									}
								} else { ?>
									<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_captcha" value="1" /> Captcha by BestWebSoft</label>
									<span class="bws_info">
										<?php printf( '<a href="%s" target="_blank">%s Captcha</a>', self_admin_url( 'plugins.php' ), esc_html__( 'Activate', 'contact-form-plugin' ) ); ?>
									</span>
								<?php }
							} else { ?>
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_captcha" value="1" /> Captcha by BestWebSoft</label>
								<span class="bws_info">
									<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=19ac1e9b23bea947cfc4a9b8e3326c03&amp;pn=77&amp;v=%s&amp;wp_v=%s" target="_blank">%s Captcha</a>', $cntctfrm_plugin_info["Version"], $wp_version, esc_html__( 'Download', 'contact-form-plugin' ) ) ?>
								</span>
							<?php } ?>
						</div>
						<div style="clear: both; white-space: nowrap;">
							<?php if ( array_key_exists( 'google-captcha/google-captcha.php', $all_plugins ) || array_key_exists( 'google-captcha-pro/google-captcha-pro.php', $all_plugins ) || array_key_exists( 'google-captcha-plus/google-captcha-plus.php', $all_plugins ) ) {
								if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) ) {
									if ( ! $contact_form_multi_active ) {
										$display_google_captcha = ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ? true : false;
									} else {
										$display_google_captcha = ! empty( $this->options['display_google_captcha'] ) ? true : false;
									}

									if ( ! $contact_form_multi_active || ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) { ?>
										<label><input type="checkbox" name="cntctfrm_display_google_captcha" value="1" <?php checked( $display_google_captcha ); ?> /> reCaptcha by BestWebSoft</label>
									<?php } else { ?>
										<label>
											<input type="checkbox" name="cntctfrm_display_google_captcha" value="1" disabled="disabled" <?php checked( $display_google_captcha ); ?> /> reCaptcha by BestWebSoft
											<span class="bws_info">&nbsp;(<?php esc_html_e( 'Please activate the appropriate option on', 'contact-form-plugin' ) ?>&nbsp;
												<?php printf( '<a href="%s" target="_blank"> reCaptcha by BestWebSoft %s</a>&nbsp;)',
												self_admin_url( '/admin.php?page=' . $cntctfrm_related_plugins['google-captcha']['settings_page'] ),
                                                    esc_html__( 'settings page', 'contact-form-plugin' ) ); ?>
											</span>
										</label>
									<?php }
								} else { ?>
									<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_google_captcha" value="1" /> reCaptcha by BestWebSoft</label>
									<span class="bws_info">
										<?php printf( '<a href="%s" target="_blank">%s reCaptcha</a>', self_admin_url( 'plugins.php' ), esc_html__( 'Activate', 'contact-form-plugin' ) ); ?>
									</span>
								<?php }
							} else { ?>
								<label><input disabled="disabled" type="checkbox" name="cntctfrm_display_google_captcha" value="1" /> reCaptcha by BestWebSoft</label> <span class="bws_info">
									<?php printf( '<a href="https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=7d74e61dd1cea23d0e9bf2fa88b5b117&amp;pn=77&amp;v=%s&amp;wp_v=%s" target="_blank">%s reCaptcha</a>', $cntctfrm_plugin_info["Version"], $wp_version, esc_html__( 'Download', 'contact-form-plugin' ) ) ?>
									</span>
							<?php } ?>
						</div>
                        <!-- pls -->
						<?php if ( ! $this->hide_pro_tabs ) { ?>
							<div class="bws_pro_version_bloc">
								<div class="bws_pro_version_table_bloc">
									<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'contact-form-plugin' ); ?>"></button>
									<div class="bws_table_bg"></div>
									<div class="bws_pro_version">
										<fieldset>
											<label><input disabled="disabled" type="checkbox" value="1" name="cntctfrm_display_privacy_check"> <?php esc_html_e( 'Agreement checkbox', 'contact-form-plugin' ); ?> <span class="bws_info"><?php esc_html_e( 'Required checkbox for submitting the form', 'contact-form-plugin' ); ?></span></label><br />
											<label><input disabled="disabled" type="checkbox" value="1" name="cntctfrm_display_optional_check"> <?php esc_html_e( 'Optional checkbox', 'contact-form-plugin' ); ?> <span class="bws_info"><?php esc_html_e( 'Optional checkbox, the results of which will be displayed in email', 'contact-form-plugin' ); ?></span></label>
										</fieldset>
									</div>
								</div>
								<?php $this->bws_pro_block_links(); ?>
							</div>
						<?php } ?>
                        <!-- end pls -->
					</fieldset></td>
				</tr>
				<tr valign="top">
					<th scope="row" style="width:200px;"><?php esc_html_e( "Delete an attachment file from the server after the email is sent", 'contact-form-plugin' ); ?> </th>
					<td colspan="3">
						<input type="checkbox" id="cntctfrm_delete_attached_file" name="cntctfrm_delete_attached_file" value="1" <?php checked( '1', $this->options['delete_attached_file'] ); ?> />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "Email in HTML format sending", 'contact-form-plugin' ); ?></th>
					<td colspan="2"><input type="checkbox" name="cntctfrm_html_email" value="1" <?php checked( '1', $this->options['html_email'] ); ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "Display additional info in the email", 'contact-form-plugin' ); ?></th>
					<td colspan="2">
						<input type="checkbox" id="cntctfrm_display_add_info" name="cntctfrm_display_add_info" value="1" <?php checked( '1', $this->options['display_add_info'] ); ?> />
                    <br />
                    <br />
					<div class="cntctfrm_display_add_info_block" <?php if ( '0' == $this->options['display_add_info'] ) echo 'style="display:none"'; ?>>
						<fieldset>
							<label><input type="checkbox" id="cntctfrm_display_sent_from" name="cntctfrm_display_sent_from" value="1" <?php checked( '1', $this->options['display_sent_from'] ); ?> /> <?php esc_html_e( "Sent from (IP address)", 'contact-form-plugin' ); ?></label><br /> <span class="bws_info"><?php esc_html_e( "Example: Sent from (IP address): 127.0.0.1", 'contact-form-plugin' ); ?></span><br />
							<label><input type="checkbox" id="cntctfrm_display_date_time" name="cntctfrm_display_date_time" value="1" <?php checked( '1', $this->options['display_date_time'] ); ?> /> <?php esc_html_e( "Date/Time", 'contact-form-plugin' ); ?></label><br /> <span class="bws_info"><?php esc_html_e( "Example: Date/Time: August 19, 2013 8:50 pm", 'contact-form-plugin' ); ?></span><br />
							<label><input type="checkbox" id="cntctfrm_display_coming_from" name="cntctfrm_display_coming_from" value="1" <?php checked( '1', $this->options['display_coming_from'] ); ?> /> <?php esc_html_e( "Sent from (referer)", 'contact-form-plugin' ); ?></label><br /> <span class="bws_info"><?php esc_html_e( "Example: Sent from (referer):   https://bestwebsoft.com/contacts/contact-us/", 'contact-form-plugin' ); ?></span><br />
							<label><input type="checkbox" id="cntctfrm_display_user_agent" name="cntctfrm_display_user_agent" value="1" <?php checked( '1', $this->options['display_user_agent'] ); ?> /> <?php esc_html_e( "Using (user agent)", 'contact-form-plugin' ); ?></label> <br /><span class="bws_info"><?php esc_html_e( "Example: Using (user agent): Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.95 Safari/537.36", 'contact-form-plugin' ); ?></span>
						</fieldset>
					</div>
                    </td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "Language Settings", 'contact-form-plugin' ); ?></th>
					<td colspan="2">
						<select name="cntctfrm_languages" id="cntctfrm_languages" style="max-width: 300px;">
                            <option value="0"></option>
							<?php foreach ( $cntctfrm_lang_codes as $key => $val ) {
								if ( ! empty( $this->options['language'] ) && in_array( $key, $this->options['language'] ) )
									continue;
								echo '<option value="' . esc_attr( $key ) . '"> ' . esc_html( $val ) . '</option>';
							} ?>
						</select>
						<input type="submit" class="button-secondary" name="cntctfrm_add_language_button" id="cntctfrm_add_language_button" value="<?php esc_html_e( 'Add a language', 'contact-form-plugin' ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "Change the names of the contact form fields and error messages", 'contact-form-plugin' ); ?></th>
					<td>
						<input type="checkbox" id="cntctfrm_change_label" name="cntctfrm_change_label" class="bws_option_affect" data-affect-show=".cntctfrm_change_label_block" value="1" <?php checked( '1', $this->options['change_label'] ); ?> />
					<br />
                    <br />
					<div class="cntctfrm_change_label_block" <?php if ( '0' == $this->options['change_label'] ) echo 'style="display:none"'; ?>>
						<div class="cntctfrm_label_language_tab <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? 'cntctfrm_active' : ''; ?>" id="cntctfrm_label_default"><?php esc_html_e( 'Default', 'contact-form-plugin' ); ?>
							<noscript>
								<input type="submit" class="cntctfrm_change_tab" value="default" name="cntctfrm_change_tab">
							</noscript>
						</div>
						<?php if ( ! empty( $this->options['language'] ) ) {
							foreach ( $this->options['language'] as $val ) {
								$active_tab_class = isset( $_POST["cntctfrm_change_tab"] ) && $val == $_POST["cntctfrm_change_tab"] ? "cntctfrm_active" : "";
								echo '<div class="cntctfrm_label_language_tab ' . $active_tab_class . '" id="cntctfrm_label_' . $val . '">' . $cntctfrm_lang_codes[ $val ] . ' <span class="cntctfrm_delete" rel="' . $val . '">X</span><noscript><input type="submit" class="cntctfrm_change_tab" value="' . $val . '" name="cntctfrm_change_tab"><span class="cntctfrm_del_button_wrap"><input type="submit" class="cntctfrm_delete_button" value="' . $val . '" name="cntctfrm_delete_button"></span></noscript></div>';
							}
						} ?>
						<div class="clear"></div>
						<div class="cntctfrm_language_tab cntctfrm_tab_default <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? '' : 'hidden' ?>" style="padding: 1px 3px;">
							<div class="cntctfrm_language_tab_block_mini" style="display:none;"><?php esc_html_e( "click to expand/hide the list", 'contact-form-plugin' ); ?></div>
							<div class="cntctfrm_language_tab_block">
								<input type="text" maxlength="250" name="cntctfrm_name_label[default]" value="<?php echo $this->options['name_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Name", 'contact-form-plugin' ); ?>:</span><br />
								<input type="text" maxlength="250" name="cntctfrm_address_label[default]" value="<?php echo $this->options['address_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Address", 'contact-form-plugin' ); ?>:</span><br />
								<input type="text" maxlength="250" name="cntctfrm_email_label[default]" value="<?php echo $this->options['email_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Email Address", 'contact-form-plugin' ); ?>:</span><br />
								<input type="text" maxlength="250" name="cntctfrm_phone_label[default]" value="<?php echo $this->options['phone_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Phone number", 'contact-form-plugin' ); ?>:</span><br />
								<input type="text" maxlength="250" name="cntctfrm_subject_label[default]" value="<?php echo $this->options['subject_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Subject", 'contact-form-plugin' ); ?>:</span><br />
								<input type="text" maxlength="250" name="cntctfrm_message_label[default]" value="<?php echo $this->options['message_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Message", 'contact-form-plugin' ); ?>:</span><br />
								<input type="text" maxlength="250" name="cntctfrm_attachment_label[default]" value="<?php echo $this->options['attachment_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Attachment", 'contact-form-plugin' ); ?>:</span><br />
								<input type="text" maxlength="250" name="cntctfrm_attachment_tooltip[default]" value="<?php echo $this->options['attachment_tooltip']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Tips below the Attachment block", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_send_copy_label[default]" value="<?php echo $this->options['send_copy_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Send me a copy", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_gdpr_label[default]" value="<?php echo $this->options['gdpr_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "I consent to having this site collect my personal data.", 'contact-form-plugin' ); ?></span><br />
                                <input type="text" maxlength="250" name="cntctfrm_gdpr_text_button[default]" value="<?php echo $this->options['gdpr_text_button']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Learn more", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_submit_label[default]" value="<?php echo $this->options['submit_label']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Submit", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_name_error[default]" value="<?php echo $this->options['name_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Name field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_address_error[default]" value="<?php echo $this->options['address_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Address field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_email_error[default]" value="<?php echo $this->options['email_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Email field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_phone_error[default]" value="<?php echo $this->options['phone_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Phone field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_subject_error[default]" value="<?php echo $this->options['subject_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Subject field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_message_error[default]" value="<?php echo $this->options['message_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Message field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_attachment_error[default]" value="<?php echo $this->options['attachment_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message about the file type for the Attachment field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_attachment_upload_error[default]" value="<?php echo $this->options['attachment_upload_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message while uploading a file for the Attachment field to the server", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_attachment_move_error[default]" value="<?php echo $this->options['attachment_move_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message while moving the file for the Attachment field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_attachment_size_error[default]" value="<?php echo $this->options['attachment_size_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message when file size limit for the Attachment field is exceeded", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_captcha_error[default]" value="<?php echo $this->options['captcha_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Captcha field", 'contact-form-plugin' ); ?></span><br />
								<input type="text" maxlength="250" name="cntctfrm_form_error[default]" value="<?php echo $this->options['form_error']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the whole form", 'contact-form-plugin' ); ?></span><br />
							</div>
							<?php if ( ! $contact_form_multi_active ) { ?>
								<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
							<?php } else { ?>
								<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form id=<?php echo $GLOBALS['cntctfrmmlt_id_form']; ?>]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
							<?php } ?>
						</div>
						<?php if ( ! empty( $this->options['language'] ) ) {
							foreach ( $this->options['language'] as $val ) {
								if ( ( isset( $_POST['cntctfrm_change_tab'] ) && $val != $_POST['cntctfrm_change_tab'] ) || ! isset($_POST['cntctfrm_change_tab'] ) )
									$labels_table_class = 'hidden';
								else
									$labels_table_class = ''; ?>
								<div class="cntctfrm_language_tab <?php echo $labels_table_class; ?> cntctfrm_tab_<?php echo $val; ?>">
									<div class="cntctfrm_language_tab_block_mini" style="display:none;"><?php esc_html_e( "click to expand/hide the list", 'contact-form-plugin' ); ?></div>
									<div class="cntctfrm_language_tab_block">
										<input type="text" maxlength="250" name="cntctfrm_name_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['name_label'][ $val ] ) ) echo $this->options['name_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Name", 'contact-form-plugin' ); ?>:</span><br />
										<input type="text" maxlength="250" name="cntctfrm_address_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['address_label'][ $val ] ) ) echo $this->options['address_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Address", 'contact-form-plugin' ); ?>:</span><br />
										<input type="text" maxlength="250" name="cntctfrm_email_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['email_label'][ $val ] ) ) echo $this->options['email_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Email Address", 'contact-form-plugin' ); ?>:</span><br />
										<input type="text" maxlength="250" name="cntctfrm_phone_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['phone_label'][ $val ] ) ) echo $this->options['phone_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Phone number", 'contact-form-plugin' ); ?>:</span><br />
										<input type="text" maxlength="250" name="cntctfrm_subject_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['subject_label'][ $val ] ) ) echo $this->options['subject_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Subject", 'contact-form-plugin' ); ?>:</span><br />
										<input type="text" maxlength="250" name="cntctfrm_message_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['message_label'][ $val ] ) ) echo $this->options['message_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Message", 'contact-form-plugin' ); ?>:</span><br />
										<input type="text" maxlength="250" name="cntctfrm_attachment_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['attachment_label'][ $val ] ) ) echo $this->options['attachment_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Attachment", 'contact-form-plugin' ); ?>:</span><br />
										<input type="text" maxlength="250" name="cntctfrm_attachment_tooltip[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['attachment_tooltip'][ $val ] ) ) echo $this->options['attachment_tooltip'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Tips below the Attachment block", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_send_copy_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['send_copy_label'][ $val ] ) ) echo $this->options['send_copy_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Send me a copy", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_gdpr_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['gdpr_label'][ $val ] ) ) echo $this->options['gdpr_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "I consent to having this site collect my personal data.", 'contact-form-plugin' ); ?></span><br />
                                        <input type="text" maxlength="250" name="cntctfrm_gdpr_text_button[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['gdpr_text_button'][ $val ] ) ) echo $this->options['gdpr_text_button'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Learn more", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_submit_label[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['submit_label'][ $val ] ) ) echo $this->options['submit_label'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Submit", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_name_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['name_error'][ $val ] ) ) echo $this->options['name_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Name field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_address_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['address_error'][ $val ] ) ) echo $this->options['address_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Address field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_email_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['email_error'][ $val ] ) ) echo $this->options['email_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Email field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_phone_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['phone_error'][ $val ] ) ) echo $this->options['phone_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Phone field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_subject_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['subject_error'][ $val ] ) ) echo $this->options['subject_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Subject field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_message_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['message_error'][ $val ] ) ) echo $this->options['message_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Message field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_attachment_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['attachment_error'][ $val ] ) ) echo $this->options['attachment_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message about the file type for the Attachment field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_attachment_upload_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['attachment_upload_error'][ $val ] ) ) echo $this->options['attachment_upload_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message while uploading a file for the Attachment field to the server", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_attachment_move_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['attachment_move_error'][ $val ] ) ) echo $this->options['attachment_move_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message while moving the file for the Attachment field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_attachment_size_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['attachment_size_error'][ $val ] ) ) echo $this->options['attachment_size_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message when file size limit for the Attachment field is exceeded", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_captcha_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['captcha_error'][ $val ] ) ) echo $this->options['captcha_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the Captcha field", 'contact-form-plugin' ); ?></span><br />
										<input type="text" maxlength="250" name="cntctfrm_form_error[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['form_error'][ $val ] ) ) echo $this->options['form_error'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Error message for the whole form", 'contact-form-plugin' ); ?></span><br />
									</div>
									<?php if ( ! $contact_form_multi_active ) { ?>
										<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val; ?>]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
									<?php } else { ?>
										<span class="bws_info cntctfrm_shortcode_for_language" style="margin-left: 5px;"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val . ' id=' . $GLOBALS['cntctfrmmlt_id_form']; ?>]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
									<?php } ?>
								</div>
							<?php }
						} ?>
					</div>
                    </td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Use the changed names of the contact form fields in the email', 'contact-form-plugin' ); ?></th>
					<td colspan="2">
						<input type="checkbox" name="cntctfrm_change_label_in_email" value="1" <?php checked( '1', $this->options['change_label_in_email'] ); ?> />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( "Action after email is sent", 'contact-form-plugin' ); ?></th>
					<td colspan="2" class="cntctfrm_action_after_send_block">
						<label><input type="radio" id="cntctfrm_action_after_send" name="cntctfrm_action_after_send" class="bws_option_affect" data-affect-show=".cntctfrm_label_lang_tab" data-affect-hide=".cntctfrm_redirect_url_input" value="1" <?php checked( '1', $this->options['action_after_send'] ); ?> /> <?php esc_html_e( "Display text", 'contact-form-plugin' ); ?></label><br />
                        <div class="cntctfrm_label_lang_tab">
						<div class="cntctfrm_label_language_tab <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? 'cntctfrm_active' : ''; ?>" id="cntctfrm_text_default"><?php esc_html_e( 'Default', 'contact-form-plugin' ); ?>
							<noscript>
								<input type="submit" class="cntctfrm_change_tab" value="default" name="cntctfrm_change_tab">
							</noscript>
						</div>
						<?php if ( ! empty( $this->options['language'] ) ) {
							foreach ( $this->options['language'] as $val ) {
								$active_tab_class = isset( $_POST["cntctfrm_change_tab"] ) && $val == $_POST["cntctfrm_change_tab"] ? "cntctfrm_active" : "";
								echo '<div class="cntctfrm_label_language_tab ' . $active_tab_class . '" id="cntctfrm_text_' . $val . '">' . $cntctfrm_lang_codes[ $val ] . ' <span class="cntctfrm_delete" rel="' . $val . '">X</span><noscript><input type="submit" class="cntctfrm_change_tab" value="' . $val . '" name="cntctfrm_change_tab"><span class="cntctfrm_del_button_wrap"><input type="submit" class="cntctfrm_delete_button" value="' . $val . '" name="cntctfrm_delete_button"></span></noscript></div>';
							}
						} ?>
						<div class="clear"></div>
						<div class="cntctfrm_language_tab cntctfrm_tab_default <?php echo ! isset( $_POST['cntctfrm_change_tab'] ) || 'default' == $_POST['cntctfrm_change_tab'] ? '' : 'hidden' ?>" style="padding: 5px 10px 5px 5px;">
							<input type="text" maxlength="250" name="cntctfrm_thank_text[default]" value="<?php echo $this->options['thank_text']['default']; ?>" /> <span class="bws_info"><?php esc_html_e( "Text", 'contact-form-plugin' ); ?></span><br />
							<?php if ( ! $contact_form_multi_active ) { ?>
								<span class="bws_info cntctfrm_shortcode_for_language"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
							<?php } else { ?>
								<span class="bws_info cntctfrm_shortcode_for_language"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form id=<?php echo $GLOBALS['cntctfrmmlt_id_form']; ?>]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
							<?php } ?>
						</div>
						<?php if ( ! empty( $this->options['language'] ) ) {
							foreach ( $this->options['language'] as $val ) {
							if ( ( isset( $_POST['cntctfrm_change_tab'] ) && $val != $_POST['cntctfrm_change_tab'] ) || ! isset($_POST['cntctfrm_change_tab'] ) )
									$labels_table_class = 'hidden';
								else
									$labels_table_class = ''; ?>
								<div class="cntctfrm_language_tab <?php echo $labels_table_class; ?> cntctfrm_tab_<?php echo $val; ?>" style="padding: 5px 10px 5px 5px;">
									<label><input type="text" maxlength="250" name="cntctfrm_thank_text[<?php echo $val; ?>]" value="<?php if ( isset( $this->options['thank_text'][ $val ] ) ) echo $this->options['thank_text'][ $val ]; ?>" /> <span class="bws_info"><?php esc_html_e( "Text", 'contact-form-plugin' ); ?></span></label><br />
									<?php if ( ! $contact_form_multi_active ) { ?>
										<span class="bws_info cntctfrm_shortcode_for_language"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val; ?>]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
									<?php } else { ?>
										<span class="bws_info cntctfrm_shortcode_for_language"><?php esc_html_e( "Use shortcode", 'contact-form-plugin' ); ?> <span class="cntctfrm_shortcode">[bestwebsoft_contact_form lang=<?php echo $val . ' id=' . $GLOBALS['cntctfrmmlt_id_form']; ?>]</span> <?php esc_html_e( "for this language", 'contact-form-plugin' ); ?></span>
									<?php } ?>
								</div>
							<?php }
						} ?>
                        </div>
						<div id="cntctfrm_before"></div>
						<br />
						<label><input type="radio" id="cntctfrm_action_after_send_url" name="cntctfrm_action_after_send" class="bws_option_affect" data-affect-hide=".cntctfrm_label_lang_tab" data-affect-show=".cntctfrm_redirect_url_input" value="0" <?php checked( '0', $this->options['action_after_send'] ); ?> /> <?php esc_html_e( "Redirect to the page", 'contact-form-plugin' ); ?></label><br />
                        <div class="cntctfrm_redirect_url_input">
						<label><input type="text" maxlength="250" name="cntctfrm_redirect_url" value="<?php echo $this->options['redirect_url']; ?>" /> <span class="bws_info"><?php esc_html_e( "Url", 'contact-form-plugin' ); ?></span></label>
                        </div>
					</td>
				</tr>
			</table>
		<?php }

		public function tab_appearance() {
			global $cntctfrm_related_plugins, $contact_form_multi_active; ?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Appearance', 'contact-form-plugin' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
				<noscript>
					<div class="error">
						<p>
							<strong>
								<?php printf(
									esc_html__( "Please enable JavaScript to change '%s', '%s', '%s', '%s' options and for fields sorting.", 'contact-form-plugin' ),
                                    esc_html__( "Form layout", 'contact-form-plugin' ),
                                    esc_html__( "Labels position", 'contact-form-plugin' ),
                                    esc_html__( "Labels align", 'contact-form-plugin' ),
                                    esc_html__( "Submit position", 'contact-form-plugin' ),
                                    esc_html__( "Add tooltips", 'contact-form-plugin' ),
                                    esc_html__( "Style options", 'contact-form-plugin' ) ); ?>
							</strong>
						</p>
					</div>
				</noscript>
				<!-- Appearance (ex Extra settings) tab -->
				<div class="cntctfrm_clear"></div>
				<form id="cntctfrm_settings_form" class="bws_form" method="post" action="admin.php?page=contact_form_pro.php&amp;action=appearance">
					<div id="cntctfrm_appearance_wrap" class="cntctfrm_appearance_<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
						<div id="<?php echo is_rtl() ? 'cntctfrm_right_table' : 'cntctfrm_left_table'; ?>">
							<table class="form-table" style="width:auto;">
								<tr valign="top">
									<th scope="row"><?php esc_html_e( "Form layout", 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<fieldset>
											<input id="cntctfrm_layout_one_column" name="cntctfrm_layout" type="radio" value="1" <?php checked( (int) $this->options['layout'] === 1 ); ?> />
											<label for="cntctfrm_layout_one_column"><?php esc_html_e( 'One column', 'contact-form-plugin' ); ?></label>
											<br/>
											<input id="cntctfrm_layout_two_columns" name="cntctfrm_layout" type="radio" value="2" <?php checked( (int) $this->options['layout'] === 2 ); ?> />
											<label for="cntctfrm_layout_two_columns"><?php esc_html_e( 'Two columns', 'contact-form-plugin' ); ?></label>
										</fieldset>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><?php esc_html_e( "Submit position", 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<fieldset>
											<input id="cntctfrm_submit_position_left" name="cntctfrm_submit_position" type="radio" value="left" <?php checked( 'left', $this->options['submit_position'] ); ?> />
											<label for="cntctfrm_submit_position_left"><?php esc_html_e( 'Left', 'contact-form-plugin' ); ?></label>
											<br/>
											<input id="cntctfrm_submit_position_right" name="cntctfrm_submit_position" type="radio" value="right" <?php checked( 'right', $this->options['submit_position'] ); ?> />
											<label for="cntctfrm_submit_position_right"><?php esc_html_e( 'Right', 'contact-form-plugin' ); ?></label>
										</fieldset>
									</td>
								</tr>
								<tr valign="top" id="cntctfrm_width">
									<th scope="row"><?php esc_html_e( 'Width', 'contact-form-plugin' ); ?></th>
									<td colspan="2">
										<fieldset>
											<label>
												<input type="radio" name="cntctfrm_width_type" value="default" <?php checked( 'default', $this->options['width']['type'] ); ?> /> <?php esc_html_e( 'Default', 'contact-form-plugin' ); ?>
											</label>
											<br />
											<label>
												<input type="radio" name="cntctfrm_width_type" value="custom" <?php checked( 'custom', $this->options['width']['type'] ); ?> /> <?php esc_html_e( 'Custom', 'contact-form-plugin' ); ?>
											</label>
										</fieldset>
										<div class="cntctfrm_width_params" <?php if ( 'custom' != $this->options['width']['type'] ) echo 'style="display: none;"'; ?>>
											<input type="number" name="cntctfrm_input_width_value" value="<?php echo $this->options['width']['input_value']; ?>" min="1" max="9999" step="1">
											<select name="cntctfrm_input_width_unit">
												<option value="%" <?php selected( '%', $this->options['width']['input_unit'] ); ?>>%</option>
												<option value="px" <?php selected( 'px', $this->options['width']['input_unit'] ); ?>><?php esc_html_e( 'px', 'contact-form-plugin' ) ?></option>
											</select>
										</div>
									</td>
								</tr>
							</table>
                            <!-- pls -->
							<?php if ( ! $this->hide_pro_tabs ) { ?>
								<div class="bws_pro_version_bloc">
									<div class="bws_pro_version_table_bloc">
										<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'contact-form-plugin' ); ?>"></button>
										<div class="bws_table_bg"></div>
										<table class="form-table bws_pro_version">
											<tr valign="top">
												<th scope="row"><?php esc_html_e( "Form align", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<fieldset>
														<input disabled="disabled" name="cntctfrm_form_align" type="radio" value="left" checked="checked">
														<label for="cntctfrm_form_align_left"><?php esc_html_e( 'Left', 'contact-form-plugin' ); ?></label>
														<br/>
														<input disabled="disabled" name="cntctfrm_form_align" type="radio" value="center">
														<label for="cntctfrm_form_align_center"><?php esc_html_e( 'Center', 'contact-form-plugin' ); ?></label>
														<br/>
														<input disabled="disabled" name="cntctfrm_form_align" type="radio" value="right">
														<label for="cntctfrm_form_align_right"><?php esc_html_e( 'Right', 'contact-form-plugin' ); ?></label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><?php esc_html_e( "Labels position", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<fieldset>
														<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="top" checked="checked">
														<label for="cntctfrm_labels_position_top"><?php esc_html_e( 'Top', 'contact-form-plugin' ); ?></label>
														<br/>
														<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="left">
														<label for="cntctfrm_labels_position_left"><?php esc_html_e( 'Left', 'contact-form-plugin' ); ?></label>
														<br/>
														<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="right">
														<label for="cntctfrm_labels_position_right"><?php esc_html_e( 'Right', 'contact-form-plugin' ); ?></label>
														<br/>
														<input disabled="disabled" name="cntctfrm_labels_position" type="radio" value="bottom">
														<label for="cntctfrm_labels_position_bottom"><?php esc_html_e( 'Bottom', 'contact-form-plugin' ); ?></label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top" id="cntctfrm_labels_align" class="cntctfrm_labels_align_show">
												<th scope="row"><?php esc_html_e( "Labels align", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<fieldset>
														<input disabled="disabled" name="cntctfrm_labels_align" type="radio" value="left" checked="checked">
														<label for="cntctfrm_labels_align_left"><?php esc_html_e( 'Left', 'contact-form-plugin' ); ?></label>
														<br/>
														<input disabled="disabled" name="cntctfrm_labels_align" type="radio" value="center">
														<label for="cntctfrm_labels_align_center"><?php esc_html_e( 'Center', 'contact-form-plugin' ); ?></label>
														<br/>
														<input disabled="disabled" name="cntctfrm_labels_align" type="radio" value="right">
														<label for="cntctfrm_labels_align_right"><?php esc_html_e( 'Right', 'contact-form-plugin' ); ?></label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><?php esc_html_e( "Errors output", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<select name="cntctfrm_error_displaying" disabled='disabled' style="max-width:400px; width:100%;">
														<option value="labels"><?php esc_html_e( "Display error messages", 'contact-form-plugin' ); ?></option>
														<option value="input_colors"><?php esc_html_e( "Color of the input field errors.", 'contact-form-plugin' ); ?></option>
														<option value="both" selected="selected"><?php esc_html_e( "Display error messages & color of the input field errors", 'contact-form-plugin' ); ?></option>
													</select>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><?php esc_html_e( "Add placeholder to the input blocks", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<input disabled='disabled' type="checkbox" name="cntctfrm_placeholder" value="1" />
												</td>
											</tr>
											<tr valign="top">
												<th scope="row"><?php esc_html_e( "Add tooltips", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<div>
														<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_name" value="1" />
														<label for="cntctfrm_tooltip_display_name"><?php esc_html_e( "Name", 'contact-form-plugin' ); ?></label>
													</div>
													<?php if ( '1' == $this->options['display_address_field'] ) { ?>
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_address" value="1" />
															<label for="cntctfrm_tooltip_display_address"><?php esc_html_e( "Address", 'contact-form-plugin' ); ?></label>
														</div>
													<?php } ?>
													<div>
														<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_email" value="1" />
														<label for="cntctfrm_tooltip_display_email"><?php esc_html_e( "Email address", 'contact-form-plugin' ); ?></label>
													</div>
													<?php if ( '1' == $this->options['display_phone_field'] ) { ?>
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_phone" value="1" />
															<label for="cntctfrm_tooltip_display_phone"><?php esc_html_e( "Phone Number", 'contact-form-plugin' ); ?></label>
														</div>
													<?php } ?>
													<div>
														<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_subject" value="1" />
														<label for="cntctfrm_tooltip_display_subject"><?php esc_html_e( "Subject", 'contact-form-plugin' ); ?></label>
													</div>
													<div>
														<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_message" value="1" />
														<label for="cntctfrm_tooltip_display_message"><?php esc_html_e( "Message", 'contact-form-plugin' ); ?></label>
													</div>
													<?php if ( '1' == $this->options['attachment_explanations'] ) { ?>
														<div>
															<input disabled='disabled' type="checkbox" name="cntctfrm_tooltip_display_attachment" value="1" />
															<label for="cntctfrm_tooltip_display_attachment"><?php esc_html_e( "Attachment", 'contact-form-plugin' ); ?></label>
														</div>
													<?php } ?>
												</td>
											</tr>
											<tr valign="top">
												<th colspan="3" scope="row">
													<input disabled='disabled' type="checkbox" name="cntctfrm_style_options" value="1" checked="checked" />
													<?php esc_html_e( "Style options", 'contact-form-plugin' ); ?>
												</th>
											</tr>
											<tr valign="top" class="cntctfrm_style_block">
												<th scope="row"><?php esc_html_e( "Text color", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( 'Label text color', 'contact-form-plugin' ); ?></div>

													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( "Placeholder color", 'contact-form-plugin' ); ?></div>
													</div>
												</td>
											</tr>
											<tr valign="top" class="cntctfrm_style_block">
												<th scope="row"><?php esc_html_e( "Errors color", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( 'Error text color', 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( 'Background color of the input field errors', 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( 'Border color of the input field errors', 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( "Placeholder color of the input field errors", 'contact-form-plugin' ); ?></div>
													</div>
												</td>
											</tr>
											<tr valign="top" class="cntctfrm_style_block">
												<th scope="row"><?php esc_html_e( "Input fields", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( "Input fields background color", 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( "Text fields color", 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<input disabled='disabled' size="8" type="text" value="" name="cntctfrm_border_input_width" /><br />
														<div class="cntctfrm_label_block"><?php esc_html_e( 'Border width in px, numbers only', 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														 <div class="cntctfrm_label_block"><?php esc_html_e( 'Border color', 'contact-form-plugin' ); ?></div>
													</div>
												</td>
											</tr>
											<tr valign="top" class="cntctfrm_style_block">
												<th scope="row"><?php esc_html_e( "Submit button", 'contact-form-plugin' ); ?></th>
												<td colspan="2">
													<div>
														<input disabled='disabled' size="8" type="text" value="" name="cntctfrm_button_width" /><br />
														<div class="cntctfrm_label_block"><?php esc_html_e( 'Width in px, numbers only', 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														 <div class="cntctfrm_label_block"><?php esc_html_e( 'Button color', 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														<div class="cntctfrm_label_block"><?php esc_html_e( "Button text color", 'contact-form-plugin' ); ?></div>
													</div>
													<div>
														<div class="wp-picker-container">
															<button type="button" class="button wp-color-result" disabled="disabled">
																<span class="wp-color-result-text"><?php esc_html_e( "Select Color", 'contact-form-plugin' ); ?></span>
															</button>
														</div>
														 <div class="cntctfrm_label_block"><?php esc_html_e( 'Border color', 'contact-form-plugin' ); ?></div>
													</div>
												</td>
											</tr>
										</table>
									</div>
									<?php $this->bws_pro_block_links(); ?>
								</div>
							<?php } ?>
                            <!-- end pls -->
						</div>
                        <!-- pls -->
						<?php if ( $this->hide_pro_tabs ) { ?>
							<div class="clear"></div>
						<?php } ?>
                        <!-- end pls -->
						<div id="<?php echo is_rtl() ? 'cntctfrm_left_table' : 'cntctfrm_right_table' ?>">
							<h3><?php esc_html_e( 'Contact Form | Preview', 'contact-form-plugin' ); ?></h3>
							<span class="bws_info"><?php esc_html_e( 'Drag the necessary field to sort fields.', 'contact-form-plugin' ); ?></span>
							<?php $classes = ( $this->options['layout'] === 1 ) ? ' cntctfrm_one_column' : ' cntctfrm_two_columns';
							$classes .= is_rtl() ? ' cntctfrm_rtl' : ' cntctfrm_ltr';
							$classes .= ( 'custom' != $this->options['width']['type'] ) ? ' cntctfrm_width_default' : ' cntctfrm_width_custom';
							if ( 'custom' == $this->options['width']['type'] ) { ?>
								<style id="cntctfrm_custom_styles" type="text/css">
									.cntctfrm_contact_form.cntctfrm_width_custom {
										max-width: <?php printf( '%d%s', $this->options['width']['input_value'], $this->options['width']['input_unit'] ); ?>;
									}
								</style>
							<?php } ?>
							<div id="cntctfrm_contact_form" class="cntctfrm_contact_form<?php echo $classes; ?>">
								<div class="cntctfrm_error_text hidden"><?php echo $this->options['form_error']['default']; ?></div>
								<div id="cntctfrm_wrap">
									<?php $cntctfrm_ordered_fields = cntctfrm_get_ordered_fields();
									for ( $i = 1; $i <= 2; $i++ ) {
										$column = ( $i == 1 ) ? 'first_column' : 'second_column'; ?>
										<ul id="cntctfrm_<?php echo $column; ?>" class="cntctfrm_column" <?php if ( $i == 2 && $this->options['layout'] === 1 ) echo 'style="display: none;"'; ?>>
											<?php foreach ( $cntctfrm_ordered_fields[ $column ] as $cntctfrm_field ) {
												switch ( $cntctfrm_field ) {
													case 'cntctfrm_contact_name':
														if ( 1 == $this->options['display_name_field'] ) { ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_label cntctfrm_label_name">
																	<label for="cntctfrm_contact_name"><?php echo $this->options['name_label']['default']; if ( 1 == $this->options['required_name_field'] ) echo '<span class="required"> ' . $this->options['required_symbol'] . '</span>'; ?></label>
																</div>
																<div class="cntctfrm_error_text hidden"><?php echo $this->options['name_error']['default']; ?></div>
																<div class="cntctfrm_input cntctfrm_input_name">
																	<div class="cntctfrm_drag_wrap"></div>
																	<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_name" id="cntctfrm_contact_name" />
																</div>
															</li>
														<?php }
														break;
													case 'cntctfrm_contact_address':
														if ( 1 == $this->options['display_address_field'] ) { ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_label cntctfrm_label_address">
																	<label for="cntctfrm_contact_address"><?php echo $this->options['address_label']['default']; if ( 1 == $this->options['required_address_field'] ) echo '<span class="required"> ' . $this->options['required_symbol'] . '</span>'; ?></label>
																</div>
																<?php if ( 1 == $this->options['required_address_field'] ) { ?>
																	<div class="cntctfrm_error_text hidden"><?php echo $this->options['address_error']['default']; ?></div>
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
																<label for="cntctfrm_contact_email"><?php echo $this->options['email_label']['default']; if ( 1 == $this->options['required_email_field'] ) echo '<span class="required"> ' . $this->options['required_symbol'] . '</span>'; ?></label>
															</div>
															<div class="cntctfrm_error_text hidden"><?php echo $this->options['email_error']['default']; ?></div>
															<div class="cntctfrm_input cntctfrm_input_email">
																<div class="cntctfrm_drag_wrap"></div>
																<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_email" id="cntctfrm_contact_email" />
															</div>
														</li>
														<?php break;
													case 'cntctfrm_contact_phone':
														if ( 1 == $this->options['display_phone_field'] ) { ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_label cntctfrm_label_phone">
																	<label for="cntctfrm_contact_phone"><?php echo $this->options['phone_label']['default']; if ( 1 == $this->options['required_phone_field'] ) echo '<span class="required"> ' . $this->options['required_symbol'] . '</span>'; ?></label>
																</div>
																<div class="cntctfrm_error_text hidden"><?php echo $this->options['phone_error']['default']; ?></div>
																<div class="cntctfrm_input cntctfrm_input_phone">
																	<div class="cntctfrm_drag_wrap"></div>
																	<input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_phone" id="cntctfrm_contact_phone" />
																</div>
															</li>
														<?php }
														break;
													case 'cntctfrm_contact_subject':
                                                        if ( isset( $this->options['visible_subject'] ) && 1 == $this->options['visible_subject'] ) { ?>
                                                            <li class="cntctfrm_field_wrap">
                                                                <div class="cntctfrm_label cntctfrm_label_subject">
                                                                    <label for="cntctfrm_contact_subject"><?php echo $this->options['subject_label']['default']; if ( 1 == $this->options['required_subject_field'] ) echo '<span class="required"> ' . $this->options['required_symbol'] . '</span>'; ?></label>
                                                                </div>
                                                                <div class="cntctfrm_error_text hidden"><?php echo $this->options['subject_error']['default']; ?></div>
                                                                <div class="cntctfrm_input cntctfrm_input_subject">
                                                                    <div class="cntctfrm_drag_wrap"></div>
                                                                    <input class="text bws_no_bind_notice" type="text" size="40" value="" name="cntctfrm_contact_subject" id="cntctfrm_contact_subject" />
                                                                </div>
                                                            </li>
														<?php } break;
													case 'cntctfrm_contact_message':
                                                        if ( isset( $this->options['visible_message'] ) && 1 == $this->options['visible_message'] ) { ?>
                                                            <li class="cntctfrm_field_wrap">
															<div class="cntctfrm_label cntctfrm_label_message">
																<label for="cntctfrm_contact_message"><?php echo $this->options['message_label']['default']; if ( 1 == $this->options['required_message_field'] ) echo '<span class="required"> ' . $this->options['required_symbol'] . '</span>'; ?></label>
															</div>
															<div class="cntctfrm_error_text hidden"><?php echo $this->options['message_error']['default']; ?></div>
															<div class="cntctfrm_input cntctfrm_input_message">
																<div class="cntctfrm_drag_wrap"></div>
																<textarea class="bws_no_bind_notice" rows="5" cols="30" name="cntctfrm_contact_message" id="cntctfrm_contact_message"></textarea>
															</div>
														    </li>
														<?php } break;
													case 'cntctfrm_contact_attachment':
														if ( 1 == $this->options['attachment'] ) { ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_label cntctfrm_label_attachment">
																	<label for="cntctfrm_contact_attachment"><?php echo $this->options['attachment_label']['default']; ?></label>
																</div>
																<div class="cntctfrm_error_text hidden"><?php echo $this->options['attachment_error']['default']; ?></div>
																<div class="cntctfrm_input cntctfrm_input_attachment">
																	<div class="cntctfrm_drag_wrap"></div>
																	<input class="bws_no_bind_notice" type="file" name="cntctfrm_contact_attachment" id="cntctfrm_contact_attachment" />
																	<?php if ( 1 == $this->options['attachment_explanations'] ) { ?>
																			<label class="cntctfrm_contact_attachment_extensions"><?php echo  $this->options['attachment_tooltip'][ 'default' ]; ?></label>
																	<?php } ?>
																</div>
															</li>
														<?php }
														break;
													case 'cntctfrm_contact_send_copy':
														if ( 1 == $this->options['send_copy'] ) { ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_checkbox cntctfrm_checkbox_send_copy">
																	<div class="cntctfrm_drag_wrap"></div>
																	<input type="checkbox" value="1" name="cntctfrm_contact_send_copy" id="cntctfrm_contact_send_copy" class="bws_no_bind_notice" style="margin: 0;" />
																	<label for="cntctfrm_contact_send_copy"><?php echo $this->options['send_copy_label']['default']; ?></label>
																</div>
															</li>
														<?php }
														break;
													case 'cntctfrm_contact_gdpr':
														if ( 1 == $this->options['gdpr'] ) { ?>
															<li class="cntctfrm_field_wrap">
																<div class="cntctfrm_checkbox cntctfrm_checkbox_gdpr">
																	<div class="cntctfrm_drag_wrap"></div>
																	<input type="checkbox" value="" name="cntctfrm_contact_gdpr" id="cntctfrm_contact_gdpr" class="bws_no_bind_notice" style="margin: 0;" />
																	<label for="cntctfrm_contact_gdpr"><?php echo $this->options['gdpr_label']['default']; ?></label>
																	<?php if( ! empty( $this->options['gdpr_link'] ) ) { ?>
																		<a href="<?php $this->options['gdpr_link'] ?>" target="_blank"><?php echo $this->options['gdpr_text_button']['default']; ?></a>
																	<?php } else { ?>
																		<span><?php echo $this->options['gdpr_text_button']['default']; ?></span>
																	<?php } ?>
																</div>
															</li>
														<?php }
														break;
													case 'cntctfrm_subscribe':
														if ( array_key_exists( 'subscriber', $cntctfrm_related_plugins ) ) {
															if ( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['subscriber']['options']['contact_form'] ) ) || ! empty( $this->options['display_subscribe'] ) ) {  ?>
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
																( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['captcha']['options']['forms']['bws_contact']['enable'] ) ) || ! empty( $this->options['display_captcha'] ) ) ) {

																$display_captcha = true;

																$captcha_label = isset( $cntctfrm_related_plugins['captcha']['options']['title'] ) ? $cntctfrm_related_plugins['captcha']['options']['title'] : '';
																if ( ! empty( $captcha_label ) ) {
																	$captcha_required_symbol = sprintf( ' <span class="required">%s</span>', ( isset( $cntctfrm_related_plugins['captcha']['options']['required_symbol'] ) ) ? $cntctfrm_related_plugins['captcha']['options']['required_symbol'] : '' );
																	$display_captcha_label = $captcha_label . $captcha_required_symbol;
																}
															}

															if ( array_key_exists( 'google-captcha', $cntctfrm_related_plugins ) &&
																( ( ! $contact_form_multi_active && ! empty( $cntctfrm_related_plugins['google-captcha']['options']['contact_form'] ) ) || ( $contact_form_multi_active && ! empty( $this->options['display_google_captcha'] ) ) ) )
																$display_google_captcha = true;

															if ( isset( $display_google_captcha ) || isset( $display_captcha ) ) { ?>
																<li class="cntctfrm_field_wrap">
																	<div class="cntctfrm_label cntctfrm_label_captcha">
																		<label><?php echo $display_captcha_label; ?></label>
																	</div>
																	<div class="cntctfrm_input cntctfrm_input_captcha">
																		<?php if ( isset( $display_captcha ) ) { ?>
																			<img src="<?php echo plugins_url( '../images/cptch.png', __FILE__ ); ?>">
																			<input type="hidden" value="1" name="cntctfrm_captcha"/>
																		<?php }
																		if ( isset( $display_google_captcha ) ) { ?>
																			<div id="gglcptch_preview">
																				<img src="<?php echo plugins_url( '../images/google-captcha.png', __FILE__ ); ?>">
																			</div>
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
											<?php if ( $i == $submit_position_value[ $cntctfrm_direction ][ $this->options['submit_position'] ] ) { ?>
												<div class="cntctfrm_input cntctfrm_input_submit" style="<?php printf( 'text-align: %s !important;', $this->options['submit_position'] ); ?>">
													<input type="button" value="<?php echo $this->options['submit_label']['default']; ?>" class="bws_no_bind_notice" style="cursor: pointer; margin: 0; text-align: center;" />
												</div>
											<?php } ?>
										</div>
									<?php } ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
						<div class="clear"></div>
						<input type="hidden" name="cntctfrm_form_appearance_submit" value="submit" />
						<input type="hidden" id="cntctfrm_layout_first_column" name="cntctfrm_layout_first_column" value="<?php echo implode( ',', $this->options['order_fields']['first_column'] ); ?>" />
						<input type="hidden" id="cntctfrm_layout_second_column" name="cntctfrm_layout_second_column" value="<?php echo implode( ',', $this->options['order_fields']['second_column'] ); ?>" />
					</div>
				</form>
		<?php }

        public function display_metabox() { ?>
            <div class="postbox">
                <h3 class="hndle">
                    <?php esc_html_e( 'Contact Form Shortcode', 'contact-form-plugin' ); ?>
                </h3>
                <div class="inside">
                    <?php esc_html_e( "Add Contact Form  to your page or post using the following shortcode:", 'contact-form-plugin' );
                    $id_form = $this->cfmlt_is_active ? ' id='. absint( $GLOBALS['cntctfrmmlt_id_form'] ) : '';
                    bws_shortcode_output( "[bestwebsoft_contact_form". $id_form . "]");?>
                </div>
            </div>
        <?php }
	}
} ?>