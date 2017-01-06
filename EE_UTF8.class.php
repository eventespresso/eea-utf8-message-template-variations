<?php
defined('EVENT_ESPRESSO_VERSION') || exit;

// define the plugin directory path and URL
define('EE_UTF8_BASENAME', plugin_basename(EE_UTF8_PLUGIN_FILE));
define('EE_UTF8_PATH', plugin_dir_path(__FILE__));
define('EE_UTF8_URL', plugin_dir_url(__FILE__));
define('EE_UTF8_ADMIN', EE_UTF8_PATH . 'admin' . DS . 'utf8' . DS);
define('EE_UTF8_FONTS_PATH', EE_UTF8_PATH . 'fonts' . DS);
/**
 * Class  EE_UTF8
 *
 * @package			Event Espresso
 * @subpackage		espresso-utf8
 * @author			Brent Christensen
 * @ version		$VID:$
 */
Class  EE_UTF8 extends EE_Addon {

	const copy_files_option_name = 'ee_plz_copy_font_files_to_uploads';

	public static function register_addon() {
		// register addon via Plugin API
        EE_Register_Addon::register(
            'UTF8',
            array(
                'version'          => EE_UTF8_VERSION,
                'min_core_version' => '4.9.26.rc.000',
                'main_file_path'   => EE_UTF8_PLUGIN_FILE,
                'autoloader_paths' => array(
                    'EE_UTF8' => EE_UTF8_PATH . 'EE_UTF8.class.php',
                ),
                // if plugin update engine is being used for auto-updates. not needed if PUE is not being used.
                'pue_options'      => array(
                    'pue_plugin_slug' => 'eea-utf8-variation',
                    'plugin_basename' => EE_UTF8_BASENAME,
                    'checkPeriod'     => '24',
                    'use_wp_update'   => false,
                ),
            )
        );
	}



    /**
     * a safe space for addons to add additional logic like setting hooks
     * that will run immediately after addon registration
     * making this a great place for code that needs to be "omnipresent"
     */
    public function after_registration()
    {
        add_action('EE_Brewing_Regular___messages_caf', array('EE_UTF8', 'add_utf8_variations'));
    }



    public static function add_utf8_variations() {
		if ( ! class_exists( 'EE_Register_Messages_Template_Variations' ) ) {
			return;
		}

		//setup variations array for all known message types.
		$message_types = array(
			'receipt', 'invoice',
			);
		$vtions = array();
		foreach ( $message_types as $message_type ) {
			$vtions[$message_type] = array(
							'default_utf8' => __('Simple UTF8', 'ee-new-variations-test' ),
							);
		}

		$variations_setup = array(
			'base_path' => EE_UTF8_PATH . 'variations/',
			'base_url' => EE_UTF8_URL . 'variations/',
			'variations' => array(
				'default' => array(
					'html' => $vtions,
					'pdf' => $vtions
					)
				)
			);
		EE_Register_Messages_Template_Variations::register( 'utf8', $variations_setup );
		if( is_admin() ){
			self::additional_admin_hooks();
		}
	}



	/**
	 * 	additional_admin_hooks
	 *
	 *  @access 	public
	 *  @return 	void
	 */
	public static function additional_admin_hooks() {
		//if the font directory is still in core, something's what and we should
		//make sure we've moved the font files into uploads directory
		if( get_option( self::copy_files_option_name ) ) {
			add_action( 'admin_init' , array( 'EE_UTF8', 'ensure_fonts_in_uploads_directory' ) );
		}
	}


	public function initialize_db(){
		//set an option so on the next normal admin request we'll copy over the files
		if( ! get_option( self::copy_files_option_name ) ) {
			add_option( self::copy_files_option_name, TRUE, NULL, FALSE );
		}
		parent::initialize_db();
	}



    /**
     * Copies all the font files from the utf8-fonts directory and from core.
     * If the files are already in the wp-content/uploads/fonts we just leave them be
     *
     * @return boolean
     * @throws \EE_Error
     */
	 public static function ensure_fonts_in_uploads_directory(){
		 $upload_fonts_directory = EVENT_ESPRESSO_UPLOAD_DIR . 'fonts' . DS ;
		 if( ! EEH_File::ensure_folder_exists_and_is_writable( $upload_fonts_directory ) ){
			 EE_Error::add_error( sprintf( __( 'The Event Espresso UTF8 Variation addon could not properly move the font files from %1$s to %2$s because the destination folder is not writeable. Please either adjust the destination folders permission or move the font files over manually', 'event_espresso' ), EE_UTF8_FONTS_PATH, $upload_fonts_directory )- __FILE__, __FUNCTION__, __LINE__ );
			 return FALSE;
		 }
		 //first copy over files in the utf8 addon
		 $addon_fonts = EEH_File::get_contents_of_folders( array( EE_UTF8_FONTS_PATH ), TRUE );
		 $core_fonts = EEH_File::get_contents_of_folders( array( EE_THIRD_PARTY . 'dompdf' . DS . 'lib' . DS . 'fonts' . DS ), TRUE );
		 //merge the arrays together, but make sure the addon files are given preference
		 //so that they get copied over first (and the core files that are the same WON'T get copied)
		 $fonts = array_merge( $addon_fonts, $core_fonts );
		 try{
			foreach( $fonts as $filepath ){
				$new_file_name = $upload_fonts_directory . basename( $filepath);
				if( !EEH_File::exists( $new_file_name )){
					EEH_File::copy( $filepath, $new_file_name , FALSE );
				}
			}
			//ok we've copied over the files, so we can mark the job as done
			delete_option( self::copy_files_option_name );
		 }catch( EE_Error $e ){
			 EE_Error::add_error( $e->getMessage(), __FILE__, __FUNCTION__, __LINE__ );
			 return FALSE;
		 }
		 return TRUE;
	 }




}
// End of file EE_UTF8.class.php
// Location: wp-content/plugins/espresso-utf8/EE_UTF8.class.php
