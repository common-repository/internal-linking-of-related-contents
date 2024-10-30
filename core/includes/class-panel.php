<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if( !class_exists( 'ilrc_panel' ) ) {

	class ilrc_panel {

		public $panel_fields;
    public $plugin_slug;
    public $plugin_optionname;

		/**
		 * Constructor
		 */

		public function __construct( $fields = array() ) {

			$this->panel_fields = $fields;
			$this->plugin_slug = 'ilrc_panel_';
			$this->plugin_optionname = 'ilrc_settings';

			add_action('admin_menu', array(&$this, 'admin_menu') ,11);
			add_action('admin_init', array(&$this, 'add_script') ,11);
			add_action('admin_init', array(&$this, 'save_option') ,11);

		}

		/**
		 * Create option panel menu
		 */

		public function admin_menu() {

			global $admin_page_hooks;

			if ( !isset( $admin_page_hooks['tip_plugins_panel']) ) :

				add_menu_page(
					esc_html__('TIP Plugins', 'internal-linking-related-contents'),
					esc_html__('TIP Plugins', 'internal-linking-related-contents'),
					'manage_options',
					'tip_plugins_panel',
					NULL,
					plugins_url('/assets/images/tip-icon.png', dirname(__FILE__)),
					64
				);

			endif;

			add_submenu_page(
				'tip_plugins_panel',
				esc_html__('Internal Linking of Related Contents', 'internal-linking-related-contents'),
				esc_html__('Internal Linking of Related Contents', 'internal-linking-related-contents'),
				'manage_options',
				'ilrc_panel',
				array(&$this, 'ilrc_panel')
			);

			if ( isset( $admin_page_hooks['tip_plugins_panel'] ) )
				remove_submenu_page( 'tip_plugins_panel', 'tip_plugins_panel' );

		}

		/**
		 * Loads the plugin scripts and styles
		 */

		public function add_script() {

			 global $wp_version, $pagenow;

			 $file_dir = plugins_url('/assets/', dirname(__FILE__));
			 wp_enqueue_style ( 'ilrc_notice', $file_dir.'css/notice.css' );

			 if ( $pagenow == 'admin.php' ) {

				wp_enqueue_style ( 'wp-color-picker' );

				wp_enqueue_style ( 'ilrc_panel', $file_dir.'css/panel.css' );
				wp_enqueue_style ( 'ilrc_free_pro_table', $file_dir.'css/free_pro_table.css' );
				wp_enqueue_style ( 'ilrc_panel_googlefonts', '//fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i');

				wp_enqueue_script( 'jquery');
				wp_enqueue_script( 'jquery-ui-core', array('jquery'));
				wp_enqueue_script( 'jquery-ui-tabs', array('jquery'));
			 	wp_enqueue_script( 'jquery-ui-sortable', array('jquery'));

				wp_enqueue_script( 'ilrc_panel', $file_dir.'js/panel.js',array('jquery','thickbox', 'wp-color-picker'),'1.0',TRUE );

				wp_localize_script( 'ilrc_panel', 'ilrc_pluginData', array(
					'path'   => $file_dir )
				);

			 }

		}

		/**
		 * Message after the options saving
		 */

		public function save_message () {

			global $ilrc_message;
			$plugin_slug = $this->plugin_slug;

			if (isset($ilrc_message))
				echo '<div id="message" class="updated fade message_save ' . $plugin_slug . 'message"><p><strong> ' . $ilrc_message . '</strong></p></div>';

		}

		/**
		 * Sanitize icon function
		 */

		public function sanitize_template_function($k) {

			$allowedOptions = array(
				'template-1',
				'template-2',
				'template-3',
			);

			if ( in_array($k, $allowedOptions)) {

				return $k;

			} else {

				return 'template-2';

			}

		}

		/**
		 * Sanitize count function
		 */

		public function sanitize_count_function($k) {

			$allowedOptions = array(
				'1',
				'2',
				'3'
			);

			if ( in_array($k, $allowedOptions)) {

				return $k;

			} else {

				return '2';

			}

		}

		/**
		 * Sanitize offset function
		 */

		public function sanitize_offset_function($k) {

			$allowedOptions = array(
				'1',
				'2',
				'3',
				'4',
				'5',
				'6'
			);

			if ( in_array($k, $allowedOptions)) {

				return $k;

			} else {

				return '2';

			}

		}

		/**
		 * Sanitize count function
		 */

		public function sanitize_enginesearch_function($k) {

			$allowedOptions = array(
				'categories',
				'tags',
			);

			if ( in_array($k, $allowedOptions)) {

				return $k;

			} else {

				return 'categories';

			}

		}

		/**
		 * Sanitize boolean function
		 */

		public function sanitize_targetattribute_function($k) {

			return ( $k == '_blank' ) ? '_blank' : '';

		}

		/**
		 * Sanitize boolean function
		 */

		public function sanitize_relattribute_function($k) {

			return ( $k == 'nofollow' ) ? 'nofollow' : '';

		}

		/**
		 * Sanitize boolean function
		 */

		public function sanitize_pixel_function($k) {

			return absint(str_replace('px', '', $k)) . 'px';

		}

		/**
		 * Sanitize postID function
		 */

		public function sanitize_postID_function($k) {

		  if ( isset($k) ) :

		  	foreach ($k as $v) {

		  		$postTitle = get_the_title($v);

		  		if ( true == post_exists($postTitle) ) {

		  			$error = false;

		  		} elseif ( false == post_exists($postTitle) ) {

		  			$error = true;

		  		}

		  	}

		  	return ($error == false ) ? $k : array();

			else:

				return array();

			endif;

		}

		/**
		 * Sanitize taxonomies function
		 */

		public function sanitize_taxID_function($k) {

		  if ( isset($k) ) :

		  	foreach ($k as $v) {

		  		$term = get_term($v);

		  		if ( true == term_exists($term->name) ) {

		  			$error = false;

		  		} elseif ( false == term_exists($term->name) ) {

		  			$error = true;

		  		}

		  	}

		  	return ($error == false ) ? $k : array();

		  else:

		    return array();

		  endif;

		}

		/**
		* Multidimensional Array sanitize function
		*/

		public function array_sanitize_function($id, $value) {

			switch ($id) {

				case 'ilrc_cta':

					$tosave = sanitize_text_field($value);

				break;

				case 'ilrc_margintop':
				case 'ilrc_marginbottom':

					$tosave = $this->sanitize_pixel_function($value);

				break;

				case 'ilrc_template':

					$tosave = $this->sanitize_template_function($value);

				break;

				case 'ilrc_backgroundcolor':
				case 'ilrc_backgroundcolorhover':
				case 'ilrc_textcolor':
				case 'ilrc_ctatextcolor':

					$tosave = sanitize_hex_color($value);

				break;

				case 'ilrc_count':

					$tosave = $this->sanitize_count_function($value);

				break;

				case 'ilrc_offset':

					$tosave = $this->sanitize_offset_function($value);

				break;

				case 'ilrc_enginesearch':

					$tosave = $this->sanitize_enginesearch_function($value);

				break;

				case 'ilrc_targetattribute':

					$tosave = $this->sanitize_targetattribute_function($value);

				break;

				case 'ilrc_relattribute':

					$tosave = $this->sanitize_relattribute_function($value);

				break;

				case 'ilrc_hookpriority':

					$tosave = absint($value);

				break;

			}

			return $tosave;

		}

		/**
		 * Save options function
		 */

		public function save_option() {

			global $ilrc_message;

			$ilrc_setting = get_option($this->plugin_optionname);

			if ( $ilrc_setting != false ) :

				$ilrc_setting = maybe_unserialize( get_option( $this->plugin_optionname ) );

			else :

				$ilrc_setting = array();

			endif;

			if (isset($_GET['action']) && ($_GET['action'] == 'ilrc_backup_download')) {

				header("Cache-Control: public, must-revalidate");
				header("Pragma: hack");
				header("Content-Type: text/plain");
				header('Content-Disposition: attachment; filename="ilrc_backup.dat"');
				echo serialize($this->get_options());
				exit;

			}

			if (isset($_GET['action']) && ($_GET['action'] == 'ilrc_backup_reset')) {

				update_option( $this->plugin_optionname,'');
				wp_redirect(admin_url('admin.php?page=ilrc_panel&tab=Import_Export'));
				exit;

			}

			if (isset($_POST['ilrc_upload_backup']) && check_admin_referer('ilrc_restore_options', 'ilrc_restore_options')) {

				if ($_FILES["ilrc_upload_file"]["error"] <= 0) {

					$options = unserialize(file_get_contents($_FILES["ilrc_upload_file"]["tmp_name"]));

					if ($options) {

						foreach ($options as $option) {
							update_option( $this->plugin_optionname, unserialize($option->option_value));

						}

					}

				}

				wp_redirect(admin_url('admin.php?page=ilrc_panel&tab=Import_Export'));
				exit;

			}

			if ( $this->ilrc_request('ilrc_save_settings_action') !== null ) {

				if (
					!current_user_can('manage_options') ||
					!isset($_POST['ilrc_save_nonces']) ||
					!wp_verify_nonce(esc_attr($_POST['ilrc_save_nonces']), 'ilrc_save_options' )
				) {
					exit;
				}

				foreach ( $this->panel_fields as $element ) {

					if ( isset($element['tab']) && $element['tab'] == $_GET['tab'] ) {

						foreach ($element as $value ) {

							if ( isset($value['id']) ) {

								if ( strpos($value["id"], 'toexclude') && !isset($_POST[$value["id"]])) {
									$current[$value['id']] = array();
								} else {
									$current[$value['id']] = $this->array_sanitize_function($value['id'], $_POST[$value["id"]]);
								}

								update_option( $this->plugin_optionname, array_merge( $ilrc_setting, $current) );

							}

							$ilrc_message = esc_html__('Options saved successfully.', 'internal-linking-related-contents' );

						}

					}

				}

			}

		}

		/**
		 * Get options
		 */

		public function get_options() {

			global $wpdb;
			return $wpdb->get_results("SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name = '".$this->plugin_optionname."'");

		}

		/**
		 * Request function
		 */

		public function ilrc_request($id) {

			if (isset($_REQUEST[$id]))
				return sanitize_text_field($_REQUEST[$id]);

		}

		/**
		 * Option panel
		 */

		public function ilrc_panel() {

			global $ilrc_message;

			$ilrcForm = new ilrc_form();
			$plugin_slug =  $this->plugin_slug;

			if (!isset($_GET['tab']))
				$_GET['tab'] = "Plugin_Settings";

			foreach ( $this->panel_fields as $element) {

				if (isset($element['type'])) :

					switch ( $element['type'] ) {

						case 'navigation':

							echo $ilrcForm->elementStart('div', $plugin_slug . 'tabs', FALSE );

								echo $ilrcForm->elementStart('div', $plugin_slug . 'header', FALSE );

									echo $ilrcForm->elementStart('div', FALSE, 'left plugin_description' );

										echo $ilrcForm->element('h2', FALSE, 'maintitle', esc_html__( 'Internal Linking of Related Contents','internal-linking-related-contents'));
										echo $ilrcForm->element('span', FALSE, FALSE, esc_html__( 'Version: ','internal-linking-related-contents') . ILRC_VERSION);
										echo $ilrcForm->link('https://www.themeinprogress.com', FALSE, FALSE, '_blank', FALSE, esc_html__( 'by ThemeinProgress','internal-linking-related-contents') );
										echo $ilrcForm->link('https://internal-linking-related-contents-pro.demo.themeinprogress.com/free-settings/', FALSE, FALSE, '_blank', FALSE, esc_html__( ' - Documentation','internal-linking-related-contents') );
										echo $ilrcForm->link('https://wordpress.org/support/plugin/internal-linking-of-related-contents/', FALSE, FALSE, '_blank', FALSE, esc_html__( ' - Support','internal-linking-related-contents') );
										echo $ilrcForm->link('https://wordpress.org/support/plugin/internal-linking-of-related-contents/reviews/', FALSE, FALSE, '_blank', FALSE, esc_html__( ' - Rate this plugin on WordPress.org','internal-linking-related-contents') );

									echo $ilrcForm->elementEnd('div');

									echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

								echo $ilrcForm->elementEnd('div');

								$this->save_message();

								echo $ilrcForm->htmlList('ul', FALSE, $plugin_slug . 'navigation', $element['item'], esc_attr($_GET['tab']));

						break;

						case 'end-tab':

								echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

							echo $ilrcForm->elementEnd('div');

						break;

					}

				endif;

			if (isset($element['tab'])) :

				switch ( $element['tab'] ) {

					case esc_attr($_GET['tab']):

						foreach ($element as $value) {

							if (isset($value['type'])) :

								switch ( $value['type'] ) {

								case 'start-form':

									echo $ilrcForm->elementStart('div', str_replace(' ', '', $value['name']), FALSE );

										echo $ilrcForm->formStart('post', '?page=ilrc_panel&tab=' . esc_attr($_GET['tab']) );
										echo $ilrcForm->input('ilrc_save_nonces', FALSE, FALSE, 'hidden', esc_attr(wp_create_nonce( 'ilrc_save_options' )));

								break;

								case 'end-form':

										echo $ilrcForm->formEnd();

									echo $ilrcForm->elementEnd('div');

								break;

								case 'start-open-container':

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'container' );

										echo $ilrcForm->element('h5', FALSE, 'element-open', $value['name'] );

										echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'mainbox ilrc_openbox' );

								break;

								case 'end-container':

										echo $ilrcForm->elementEnd('div');

									echo $ilrcForm->elementEnd('div');

								break;

								case 'text':

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box' );

										echo $ilrcForm->elementStart('div', FALSE, 'input-left' );

											echo $ilrcForm->label($value['id'], $value['name']);

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->elementStart('div', FALSE, 'input-right' );

											echo $ilrcForm->input($value['id'], $value['id'], FALSE, $value['type'], sanitize_text_field(ilrc_setting($value['id'], $value['std'])));
											echo $ilrcForm->element('p', FALSE, FALSE, $value['desc']);

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

									echo $ilrcForm->elementEnd('div');

								break;

								case 'select':

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box');

										echo $ilrcForm->elementStart('div', FALSE, 'input-left' );

											echo $ilrcForm->label($value['id'], $value['name']);

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->elementStart('div', FALSE, 'input-right' );

											echo $ilrcForm->select($value['id'], $value['id'], FALSE, $value['options'], ilrc_setting($value['id'], $value['std']), FALSE);
											echo $ilrcForm->element('p', FALSE, FALSE, $value['desc']);

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

									echo $ilrcForm->elementEnd('div');

								break;

								case "save-button":

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box WIP_plugin_save_box');

										echo $ilrcForm->input('ilrc_save_settings_action', FALSE, 'button', 'submit', esc_html__('Save content', 'custom-thank-you-page' ));

									echo $ilrcForm->elementEnd('div');

								break;

								case 'color':

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box' );

										echo $ilrcForm->elementStart('div', FALSE, 'input-left' );

											echo $ilrcForm->label($value['id'], $value['name']);

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->elementStart('div', FALSE, 'input-right' );

											echo $ilrcForm->color($value['id'], $value['id'], $plugin_slug . 'color', 'text',
sanitize_hex_color(ilrc_setting($value['id'], $value['std'])), $value['std']);
											echo $ilrcForm->element('p', FALSE, FALSE, $value['desc']);

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

									echo $ilrcForm->elementEnd('div');

								break;

								case 'import_export':

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box' );

										echo $ilrcForm->elementStart('div', FALSE, 'input-left' );

											echo $ilrcForm->label(FALSE, esc_html__('Current plugin settings','internal-linking-related-contents'));

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->elementStart('div', FALSE, 'input-right' );

											echo $ilrcForm->textarea(FALSE, FALSE, 'widefat code', serialize($this->get_options()), TRUE);

											$exportURL = esc_url('?page=ilrc_panel&tab=Import_Export&action=ilrc_backup_download');
											echo $ilrcForm->link($exportURL, FALSE, 'button button-secondary', '_self', FALSE, esc_html__( 'Download current plugin settings','internal-linking-related-contents') );

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

									echo $ilrcForm->elementEnd('div');

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box' );

										echo $ilrcForm->elementStart('div', FALSE, 'input-left' );

											echo $ilrcForm->label(FALSE, esc_html__('Reset plugin settings','internal-linking-related-contents'));

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->elementStart('div', FALSE, 'input-right' );

											$resetURL = esc_url('?page=ilrc_panel&tab=Import_Export&action=ilrc_backup_reset');
											echo $ilrcForm->link($resetURL, FALSE, 'button-secondary ilrc_restore_settings', '_self', FALSE, esc_html__( 'Reset plugin settings','internal-linking-related-contents') );

											echo $ilrcForm->element('p', FALSE, FALSE, esc_html__( 'If you click the button above, the plugin options return to its default values','internal-linking-related-contents'));

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

									echo $ilrcForm->elementEnd('div');

									echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box' );

										echo $ilrcForm->elementStart('div', FALSE, 'input-left' );

											echo $ilrcForm->label(FALSE, esc_html__('Import plugin settings','internal-linking-related-contents'));

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->elementStart('div', FALSE, 'input-right' );

											echo $ilrcForm->input('ilrc_upload_file', FALSE, FALSE, 'file', FALSE);
											echo $ilrcForm->input('ilrc_upload_backup', 'ilrc_upload_backup', 'button-primary', 'submit', esc_html__( 'Import plugin settings','internal-linking-related-contents'));
											function_exists('wp_nonce_field') ? wp_nonce_field('ilrc_restore_options', 'ilrc_restore_options') : '' ;

										echo $ilrcForm->elementEnd('div');

										echo $ilrcForm->element('div', FALSE, 'clear', FALSE);

									echo $ilrcForm->elementEnd('div');

								break;

								case 'free_vs_pro':

								echo $ilrcForm->elementStart('div', FALSE, $plugin_slug . 'box' );

									echo $ilrcForm->tableStart(FALSE, $plugin_slug . ' card table free-pro', 0, 0 );

									echo $ilrcForm->tableElementStart('tbody', FALSE, 'table-body');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'table-head');

											echo $ilrcForm->tableElement('th', FALSE, 'large');

											echo $ilrcForm->tableElementStart('th', FALSE, 'indicator');
												echo esc_html__('Free', 'internal-linking-related-contents');
											echo $ilrcForm->tableElementEnd('th');

											echo $ilrcForm->tableElementStart('th', FALSE, 'indicator');
												echo esc_html__('Premium', 'internal-linking-related-contents');
											echo $ilrcForm->tableElementEnd('th');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Custom colors', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Option to edit the cta text', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Option to edit the top and bottom margin', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Shortcode', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Related content based of post category', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Related content based of post tag', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Option to edit the_content hook priority', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

											echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Templates', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo esc_html__('3', 'internal-linking-related-contents');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo esc_html__('12', 'internal-linking-related-contents');

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Related contents inside each post', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo esc_html__('1 to 3', 'internal-linking-related-contents');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo esc_html__('1 to 20', 'internal-linking-related-contents');

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Interval between each related content', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo esc_html__('1 to 6 paragraphs', 'internal-linking-related-contents');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo esc_html__('1 to 20 paragraphs', 'internal-linking-related-contents');

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Inline related posts', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE );

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Grouped related posts', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('Now you can group all related posts in one place within your content. This feature simplifies navigation and provides your readers with quick access to all related articles, enhancing the user experience of your website.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Related content based of category and post tags', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('Besides the options available on the free version, you can load the related contents based of categories and post tags.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Custom keywords', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can use custom keywords as Engine Search but you will need to set the keywords for each post to generate the list of related contents.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Option to exclude specific categories', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can select one or more categories to exclude from the related contents.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Option to exclude specific tags', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can select one or more post tags to exclude from the related contents.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Option to exclude specific posts', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can select one or more posts to exclude from the related contents.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Device selection', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can select the device where you want to display the related contents.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Option to edit the font size', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can set a different font size, based of the user device (mobile,tablet and desktop).', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Featured image', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can display the featured image of related content, choosing one of premium available template.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Order by option', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can select how to order the related contents.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Sort order option', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can select the ordering of related contents.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Shortcode generator', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('For only WordPress 3.9.0 and higher versions is available a dynamic shortcode generator, to add a specific related post inside the WordPress content.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');



										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Advanced shortcode', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('Use the new shortcode "[ilrc_advanced]" to display a list of related contents or a specific related post, based on available related posts.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');




										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Gutenberg block', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('Starting from the version 1.0.9, you can use the Gutenberg block to add a specific related post inside the WordPress content.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('AMP support', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('Display rightly the related posts in AMP pages.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Custom post types support', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('You can enable the related contents for specific custom post types.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'feature-row');

											echo $ilrcForm->tableElementStart('td', FALSE, 'large');

												echo $ilrcForm->elementStart('div', FALSE, 'feature-wrap' );

													echo $ilrcForm->elementStart('h4', FALSE, FALSE );

														echo esc_html__('Automatic data import', 'internal-linking-related-contents');

													echo $ilrcForm->elementEnd('h4');

													echo $ilrcForm->elementStart('div', FALSE, 'feature-inline-row' );

														echo $ilrcForm->element('span', FALSE, 'info-icon dashicon dashicons dashicons-info', FALSE );

														echo $ilrcForm->elementStart('span', FALSE, 'feature-description' );

															echo esc_html__('After the activation of Internal Linking of Related Contents Pro, all settings will be imported automatically from the free version.', 'internal-linking-related-contents');

														echo $ilrcForm->elementEnd('span');

													echo $ilrcForm->elementEnd('div');

												echo $ilrcForm->elementEnd('div');

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-no-alt', FALSE);

											echo $ilrcForm->tableElementEnd('td');

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->element('span', FALSE, 'dashicon dashicons dashicons-yes', FALSE);

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

										echo $ilrcForm->tableElementStart('tr', FALSE, 'upsell-row');

											echo $ilrcForm->tableElement('td', FALSE, FALSE);
											echo $ilrcForm->tableElement('td', FALSE, FALSE);

											echo $ilrcForm->tableElementStart('td', FALSE, 'indicator');

												echo $ilrcForm->link(esc_url(ILRC_SALE_PAGE . 'ilrc-freepro-table'), FALSE, 'button button-primary', '_blank', FALSE, esc_html__( 'Upgrade to Premium','internal-linking-related-contents') );

											echo $ilrcForm->tableElementEnd('td');

										echo $ilrcForm->tableElementEnd('tr');

									echo $ilrcForm->tableElementEnd('tbody');

									echo $ilrcForm->tableEnd();

								echo $ilrcForm->elementEnd('div');

								break;

								}

							endif;

						}

					}

				endif;

			}

		}

	}

}

?>
