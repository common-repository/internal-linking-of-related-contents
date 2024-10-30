<?php

/*
Plugin Name: Internal Linking of Related Contents
Plugin URI: https://www.themeinprogress.com/internal-linking-related-contents-pro/
Description: Internal Linking of Related Contents allows you to automatically insert inline related posts within your WordPress articles.
Version: 1.1.5
Text Domain: internal-linking-related-contents
Author: ThemeinProgress
Author URI: https://www.themeinprogress.com
License: GPL3
Domain Path: /languages/

Copyright 2024  ThemeinProgress  (email : support@wpinprogress.com)

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

define( 'ILRC_NAME', 'Internal Linking Related Contents' );
define( 'ILRC_VERSION', '1.1.5' );
define( 'ILRC_PLUGIN_FOLDER', plugins_url(false, __FILE__ ) );
define( 'ILRC_ITEM_SLUG', 'ilrc');
define( 'ILRC_UPGRADE_LINK', 'https://www.themeinprogress.com/internal-linking-of-related-contents-pro/' );
define( 'ILRC_SALE_PAGE', 'https://www.themeinprogress.com/internal-linking-of-related-contents-pro/?ref=2&campaign=');

if( !class_exists( 'ilrc_init' ) ) {

	class ilrc_init {

		/**
		* Constructor
		*/

		public function __construct() {

			add_action('admin_init', array(&$this, 'disable_plugins') );
			add_action('plugins_loaded', array(&$this, 'plugin_setup') );
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_action_links' ), 10, 2 );
			add_action('wp_enqueue_scripts', array(&$this,'site_scripts') );

		}

		/**
		* Disable pro version
		*/

		public function disable_plugins() {

			if (is_plugin_active('internal-linking-related-contents-pro/init.php'))
				deactivate_plugins('internal-linking-related-contents-pro/init.php');

		}

		/**
		* Plugin settings link
		*/

		public function plugin_action_links( $links ) {

			$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=ilrc_panel') ) .'">' . esc_html__('Settings','internal-linking-related-contents') . '</a>';
			$links[] = '<a target="_blank" href="'. esc_url(ILRC_SALE_PAGE . 'action_link') .'">' . esc_html__('Upgrade to PRO','internal-linking-related-contents') . '</a>';
			return $links;

		}

		/**
		* Site scripts
		*/

		public function site_scripts() {

			wp_enqueue_style (
				'ilrc_style',
				plugins_url('/assets/css/style.css',
				__FILE__ ),
				array(),
				null
			);

		}

		/**
		* Plugin setup
		*/

		public function plugin_setup() {

			load_plugin_textdomain( 'internal-linking-related-contents', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

			require_once dirname(__FILE__) . '/core/functions/functions.php';
			require_once dirname(__FILE__) . '/core/functions/style.php';
			require_once dirname(__FILE__) . '/core/shortcode/shortcode.php';
			require_once dirname(__FILE__) . '/core/includes/class-related-contents.php';
			require_once dirname(__FILE__) . '/core/includes/class-form.php';
			require_once dirname(__FILE__) . '/core/includes/class-panel.php';
			require_once dirname(__FILE__) . '/core/includes/class-notice.php';

			if ( is_admin() == 1 )
				require_once dirname(__FILE__) . '/core/admin/panel.php';



		}

	}

	new ilrc_init();

}

?>
