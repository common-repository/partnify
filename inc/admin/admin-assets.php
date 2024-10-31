<?php
/**
 * Admin Enqueue.
 *
 */


class Partnify_Admin_Assets {
	var $assets_path;
	public function __construct() {
		$this->assets_path = plugin_dir_url( PARTNIFY_PLUGIN_FILE );
		// add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'partnify_admin_scripts' ) );
    }
    
    function partnify_admin_scripts() {

        wp_enqueue_style( 'partnify-stylesheet', $this->assets_path . 'assets/css/style.css');
        // Scripts
        wp_register_script( 'partnify-ajax-script', $this->assets_path . 'assets/js/partnify-ajax.js', array( 'jquery' ), '', 1 );

        $parnify_vars = array(
            'ajaxurl' => 'admin-ajax.php',
        );
        wp_localize_script( 'partnify-ajax-script', 'partnify_vars', $parnify_vars );
        wp_enqueue_script( 'partnify-ajax-script' );
        
        $screen = get_current_screen();
        // echo $screen
        if ( $screen->id == 'toplevel_page_partnify-settings' ) {
            wp_enqueue_script( 'partnify-parsley', $this->assets_path . 'assets/js/parsley.min.js', array( 'jquery' )  );
            wp_enqueue_script( 'partnify-parsley-custom', $this->assets_path . 'assets/js/parsley-custom.js', array( 'jquery' )  );
        }

    }
}
new Partnify_Admin_Assets();
