<?php
/*
Plugin Name: Advanced Custom Fields: External Media Field
Description: Add YouTube, Vimeo, Soundcloud or external images as embedded assets via ACF.
Version: 0.0.2
Author: Moritz Jacobs
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


class acf_field_external_media_plugin
{

	private $plugin_slug = "acf-external-media";
	
	/*
	*  Construct
	*
	*  @description:
	*  @since: 3.6
	*  @created: 1/04/13
	*/

	function __construct()
	{
		// set text domain
		/*
		$domain = 'acf-external_media';
		$mofile = trailingslashit(dirname(__File__)) . 'lang/' . $domain . '-' . get_locale() . '.mo';
		load_textdomain( $domain, $mofile );
		*/


		// version 4+
		add_action('acf/register_fields', array($this, 'register_fields'));

		require 'lib/plugin-update-checker-master/plugin-update-checker.php';
		$update_checker = PucFactory::buildUpdateChecker(
			'http://deviant.local/npwp-plugins/updates/?action=get_metadata&slug='.$this->plugin_slug, __FILE__, $this->plugin_slug);
	}


	/*
	*  register_fields
	*
	*  @description:
	*  @since: 3.6
	*  @created: 1/04/13
	*/

	function register_fields()
	{
		include_once('external-media.php');
	}

}

new acf_field_external_media_plugin();

?>
