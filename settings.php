<?php
if(!class_exists('WP_Photo_Album_Plugin_Settings'))
{
	class WP_Photo_Album_Plugin_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
            add_action('admin_init', array(&$this, 'admin_init'));
        	add_action('admin_menu', array(&$this, 'add_menu'));
		} // END public function __construct

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
        	// register your plugin's settings
        	register_setting('wp_album_plugin-group', 'flickr_api_key');
        	register_setting('wp_album_plugin-group', 'fb_key');

        	// add your settings section
        	add_settings_section(
        	    'wp_album_plugin-section',
        	    'Chaves das APIS',
        	    array(&$this, 'settings_section_wp_album_plugin'),
        	    'wp_album_plugin'
        	);

        	// add your setting's fields
            add_settings_field(
                'wp_album_plugin-flickr_api_key',
                'Flickr API Key',
                array(&$this, 'settings_field_input_text'),
                'wp_album_plugin',
                'wp_album_plugin-section',
                array(
                    'field' => 'flickr_api_key'
                )
            );
            add_settings_field(
                'wp_album_plugin-fb_key',
                'Facebook API Key',
                array(&$this, 'settings_field_input_text'),
                'wp_album_plugin',
                'wp_album_plugin-section',
                array(
                    'field' => 'fb_key'
                )
            );
            // Possibly do additional admin_init tasks
        } // END public static function activate

        public function settings_section_wp_album_plugin()
        {
            echo 'Estas chaves son útiles para empregar as fotos de Flickr, Facebook ou outras redes sociais...';
        }

        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
        } // END public function settings_field_input_text($args)

        /**
         * add a menu
         */
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
        	add_options_page(
        	    'Photo Albums Settings',
        	    'WP Photo Albums Plugin',
        	    'manage_options',
        	    'wp_album_plugin',
        	    array(&$this, 'plugin_settings_page')
        	);
        } // END public function add_menu()

        /**
         * Menu Callback
         */
        public function plugin_settings_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}

        	// Render the settings template
        	include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
        } // END public function plugin_settings_page()
    } // END class WP_Photo_Album_Plugin_Settings
} // END if(!class_exists('WP_Photo_Album_Plugin_Settings'))
