<?php
/*
Plugin Name: WP Photo Album Plugin
Plugin URI: https://github.com/chavaone/wp_photo_albums_plugin
Description: A photo album wordpress plugin.
Version: 1.0
Author: Marcos Chavarría Teijeiro
Author URI: http://aquelando.info
License: GPL2
*/
/*
Copyright 2012  Francis Yaconiello  <francis@yaconiello.com>
          2014  Marcos Chavarría Teijeiro <chavarria1991@gmail.com>

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

if(!class_exists('WP_Photo_Album_Plugin'))
{
	class WP_Photo_Album_Plugin
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Initialize Settings
			require_once(sprintf("%s/settings.php", dirname(__FILE__)));
			$WP_Photo_Album_Plugin_Settings = new WP_Photo_Album_Plugin_Settings();

			// Register custom post types
			require_once(sprintf("%s/post-types/post_type_template.php", dirname(__FILE__)));
			$Album_Post_Type = new Album_Post_Type();

			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate

		// Add the settings link to the plugins page
		function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=wp_plugin_template">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}


	} // END class WP_Photo_Album_Plugin
} // END if(!class_exists('WP_Photo_Album_Plugin'))

if(class_exists('WP_Photo_Album_Plugin'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('WP_Photo_Album_Plugin', 'activate'));
	register_deactivation_hook(__FILE__, array('WP_Photo_Album_Plugin', 'deactivate'));

	// instantiate the plugin class
	$wp_plugin_template = new WP_Photo_Album_Plugin();

}
