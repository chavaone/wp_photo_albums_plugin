<?php
if(!class_exists('Album_Post_Type'))
{
	/**
	 * A PostTypeTemplate class that provides 3 additional meta fields
	 */
	class Album_Post_Type
	{
		const POST_TYPE	= "albums";

    	public function __construct()
    	{
    		// register actions
    		add_action('init', array(&$this, 'init'));
    		add_action('admin_init', array(&$this, 'admin_init'));
    	}

    	public function init()
    	{
    		// Initialize Post Type
    		$this->create_post_type();
    		add_action('save_post', array(&$this, 'save_post'));
            add_filter ("manage_edit-albums_columns", array(&$this, "edit_columns"));
            add_action ("manage_posts_custom_column", array(&$this, "custom_columns"));
    	}


    	/**
    	 * Create the post type
    	 */
    	public function create_post_type()
    	{
    		$labels = array(
                'name' => _x('Álbums', 'post type general name'),
                'singular_name' => _x('Álbum', 'post type singular name'),
                'add_new' => _x('Engadir novo', 'events'),
                'add_new_item' => __('Engadir novo album'),
                'edit_item' => __('Editar Álbum'),
                'new_item' => __('Novo Álbum'),
                'view_item' => __('Ver Álbum'),
                'search_items' => __('Buscar Álbum'),
                'not_found' =>  __('Non se atoparon álbums'),
                'not_found_in_trash' => __('Non se atoparon álbums no lixo'),
                'parent_item_colon' => '',
            );

            $args = array(
                'label' => __('Álbum'),
                'labels' => $labels,
                'public' => true,
                'can_export' => true,
                'show_ui' => true,
                '_builtin' => false,
                'capability_type' => 'post',
                'menu_icon' => get_bloginfo('template_url') . '/img/album-icon.png',
                'menu_position' => 5,
                'hierarchical' => false,
                'rewrite' => array( "slug" => "albums" ),
                'supports'=> array(
                  'title',
                  'revisions',
                  'thumbnail',
                  'comments',
                  'editor'
                  ) ,
                'show_in_nav_menus' => true,
                'has_archive' => true
            );

            register_post_type(self::POST_TYPE, $args);
    	}


    	public function save_post($post_id, $post )
        {
            /* Verify the nonce before proceeding. */
            if ( !isset( $_POST['albums_post_nonce'] ) || !wp_verify_nonce( $_POST['albums_post_nonce'], basename( __FILE__ ) ) )
              return $post_id;

            /* Get the post type object. */
            $post_type = get_post_type_object( $post->post_type );

            /* Check if the current user has permission to edit the post. */
            if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
              return $post_id;

            $new_meta_value = ( isset( $_POST['photos'] ) ? $_POST['photos'] : '' );

            /* Get the meta key. */
            $meta_key = 'photos';

            /* Get the meta value of the custom field key. */
            $meta_value = get_post_meta( $post_id, $meta_key, true );

            /* If a new meta value was added and there was no previous value, add it. */
            if ( $new_meta_value && '' == $meta_value )
              add_post_meta( $post_id, $meta_key, $new_meta_value, true );

            /* If the new meta value does not match the old value, update it. */
            elseif ( $new_meta_value && $new_meta_value != $meta_value )
              update_post_meta( $post_id, $meta_key, $new_meta_value );

            /* If there is no new meta value but an old value exists, delete it. */
            elseif ( '' == $new_meta_value && $meta_value )
              delete_post_meta( $post_id, $meta_key, $meta_value );
        }


        public function edit_columns($columns)
        {
          $columns = array(
              "cb" => "<input type=\"checkbox\" />",
              "title" => "Album",
              "col_album_thumb" => "Portada",
              );

          return $columns;
        }


        public function custom_columns($column)
        {

          global $post;

          $custom = get_post_custom();

          switch ($column)
          {
          case "col_album_thumb":
            $thumb_id = get_post_thumbnail_id();
            $thumb_url = wp_get_attachment_image_src($thumb_id,'thumbnail-size', true);
            echo '<img src="' . $thumb_url[0] . '" height="175px"/>';
            break;
          }
        }


    	public function admin_init()
    	{
            add_action('admin_print_styles', array(&$this, 'metabox_admin_styles'));
            add_action('admin_print_scripts', array(&$this, 'metabox_admin_scripts'));
    		add_action('add_meta_boxes', array(&$this, 'add_meta_boxes'));
    	}


        public function metabox_admin_styles ()
        {
          global $post_type;

          if ($post_type == 'albums')
          {
            wp_enqueue_style('albums',plugins_url ("../css/albums.css", __FILE__));
          }
        }

        public function metabox_admin_scripts ()
        {
            global $post_type;

            if ($post_type == 'albums')
            {
              wp_enqueue_script('jqueryx', 'http://code.jquery.com/jquery-2.1.1.min.js');
              wp_enqueue_script('handlebars', 'http://builds.handlebarsjs.com.s3.amazonaws.com/handlebars-v1.3.0.js');
              wp_enqueue_script('photo_albums', plugins_url ("../js/admin_photo_albums.js", __FILE__),
                array('jqueryx', 'handlebars'), false, true);
            }
        }

    	/**
    	 * hook into WP's add_meta_boxes action hook
    	 */
    	public function add_meta_boxes()
    	{
    		// Add this metabox to every selected post
            add_meta_box(
              'albums-class',      // Unique ID
              'Fotos do Álbum',    // Title
              array(&$this, 'add_inner_meta_boxes'),   // Callback function
              'albums',         // Admin page (or post type)
              'normal',         // Context
              'default'         // Priority
            );
    	} // END public function add_meta_boxes()

		/**
		 * called off of the add meta box
		 */
		public function add_inner_meta_boxes($post)
		{
			// Render the job order metabox
			include(sprintf("%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE));
		} // END public function add_inner_meta_boxes($post)

	} // END class Post_Type_Template
} // END if(!class_exists('Post_Type_Template'))
