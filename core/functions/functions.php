<?php

/**
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * It is also available at this URL: http://www.gnu.org/licenses/gpl-3.0.txt
 */

/*-----------------------------------------------------------------------------------*/
/* SETTINGS */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('ilrc_setting')) {

	function ilrc_setting($id, $default = '' ) {

		$settings = get_option('ilrc_settings');

		if(isset($settings[$id]) && !empty($settings[$id])):

			return $settings[$id];

		else:

			return $default;

		endif;

	}

}

?>
