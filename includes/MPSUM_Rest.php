<?php
/**
 * Easy Updates Manager REST API
 *
 * Initializes the REST API
 *
 * @since 7.0.0
 *
 * @package WordPress
 */
class MPSUM_Rest {

	/**
	* Holds the class instance.
	*
	* @since 7.0.0
	* @access static
	* @var MPSUM_Rest $instance
	*/
	private static $instance = null;

	/**
	* Set a class instance.
	*
	* Set a class instance.
	*
	* @since 6.0.0
	* @access static
	*
	*/
	public static function run() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	} //end get_instance

	/**
	* Class constructor.
	*
	* Initialize the class
	*
	* @since 6.0.0
	* @access private
	*
	*/
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

	} //end constructor

	public function rest_api_init() {
		register_rest_route(
			'eum/v1',
			'/get/',
			array(
				'methods' => 'get',
				'callback' => array( $this, 'return_options' ),
				'permission_callback' => array( $this, 'permission_callback' )
			)
		);
		register_rest_route(
			'eum/v1',
			'/save/(?P<id>[a-zA-Z0-9-]+)/(?P<value>[a-zA-Z0-9-]+)',
			array(
				'methods' => 'post',
				'callback' => array( $this, 'save_options' ),
				'permission_callback' => array( $this, 'permission_callback' ),
			)
		);
	}

	public function save_options( $request ) {

		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );
		if ( empty( $options ) ) {
			$options = MPSUM_Admin_Core::get_defaults();
		}

		$id = sanitize_text_field( $request->get_param( 'id' ) );
		$value = sanitize_text_field( $request->get_param( 'value' ) );
error_log( $id );
		switch ( $id ) {
			case 'automatic-updates-default':
				$options[ 'automatic_development_updates' ] = 'off';
				$options[ 'automatic_major_updates' ] = 'off';
				$options[ 'automatic_minor_updates' ] = 'on';
				$options[ 'automatic_plugin_updates' ] = 'default';
				$options[ 'automatic_theme_updates' ] = 'default';
				$options[ 'automatic_translation_updates' ] = 'on';
				$options[ 'automatic_updates' ] = 'default';
				break;
			case 'automatic-updates-on':
				$options[ 'automatic_development_updates' ] = 'off';
				$options[ 'automatic_major_updates' ] = 'on';
				$options[ 'automatic_minor_updates' ] = 'on';
				$options[ 'automatic_plugin_updates' ] = 'on';
				$options[ 'automatic_theme_updates' ] = 'on';
				$options[ 'automatic_translation_updates' ] = 'on';
				$options[ 'automatic_updates' ] = 'on';
				break;
			case 'automatic-updates-off':
				$options[ 'automatic_development_updates' ] = 'off';
				$options[ 'automatic_major_updates' ] = 'off';
				$options[ 'automatic_minor_updates' ] = 'off';
				$options[ 'automatic_plugin_updates' ] = 'off';
				$options[ 'automatic_theme_updates' ] = 'off';
				$options[ 'automatic_translation_updates' ] = 'off';
				$options[ 'automatic_updates' ] = 'off';
				break;
			case 'automatic-updates-custom':
				$options[ 'automatic_updates' ] = 'custom';
				break;
			case 'automatic-major-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_major_updates' ] = 'on';
				} else {
					$options[ 'automatic_major_updates' ] = 'off';
				}
				break;
			case 'automatic-minor-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_minor_updates' ] = 'on';
				} else {
					$options[ 'automatic_minor_updates' ] = 'off';
				}
				break;
			case 'automatic-development-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_development_updates' ] = 'on';
				} else {
					$options[ 'automatic_development_updates' ] = 'off';
				}
				break;
			case 'automatic-translation-updates':
				if( 'on' == $value ) {
					$options[ 'automatic_translation_updates' ] = 'on';
				} else {
					$options[ 'automatic_translation_updates' ] = 'off';
				}
				break;
			case 'automatic-plugin-updates-default':
				$options[ 'automatic_plugin_updates' ] = 'default';
				break;
			case 'automatic-plugin-updates-on':
				$options[ 'automatic_plugin_updates' ] = 'on';
				break;
			case 'automatic-plugin-updates-off':
				$options[ 'automatic_plugin_updates' ] = 'off';
				break;
			case 'automatic-plugin-updates-individual':
				$options[ 'automatic_theme_updates' ] = 'individual';
				break;
			case 'automatic-theme-updates-default':
				$options[ 'automatic_theme_updates' ] = 'default';
				break;
			case 'automatic-theme-updates-on':
				$options[ 'automatic_theme_updates' ] = 'on';
				break;
			case 'automatic-theme-updates-off':
				$options[ 'automatic_theme_updates' ] = 'off';
				break;
			case 'automatic-theme-updates-individual':
				$options[ 'automatic_theme_updates' ] = 'individual';
				break;
			case 'disable-updates':
				if( 'on' == $value ) {
					$options[ 'all_updates' ] = 'on';
				} else {
					$options[ 'all_updates' ] = 'off';
				}
				break;
			case 'logs':
				if( 'on' == $value ) {
					$options[ 'logs' ] = 'on';
				} else {
					MPSUM_Logs::drop();
					$options[ 'logs' ] = 'off';
				}
				break;
			case 'browser-nag':
				if( 'on' == $value ) {
					$options[ 'misc_browser_nag' ] = 'on';
				} else {
					$options[ 'misc_browser_nag' ] = 'off';
				}
				break;
			case 'version-footer':
				if( 'on' == $value ) {
					$options[ 'misc_wp_footer' ] = 'on';
				} else {
					$options[ 'misc_wp_footer' ] = 'off';
				}
				break;
		}

		// Save options
		MPSUM_Updates_Manager::update_options( $options, 'core' );

		return $options;
	}

	public function return_options() {

		// Get options
		$options = MPSUM_Updates_Manager::get_options( 'core', true );
		if ( empty( $options ) ) {
			$options = MPSUM_Admin_Core::get_defaults();
		}

		// Set automatic updates defaults if none is selected
		if ( ! isset( $options[ 'automatic_updates' ] ) || 'unset' === $options[ 'automatic_updates' ] ) {

			// Check to see if automatic updates are on
			// Prepare for mad conditionals
			if ( 'off' == $options[ 'automatic_development_updates' ] && 'on' == $options[ 'automatic_major_updates' ] && 'on' == $options[ 'automatic_minor_updates' ] && 'on' == $options[ 'automatic_plugin_updates' ] && 'on' == $options[ 'automatic_theme_updates' ] && 'on' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'on';
			} elseif( 'off' == $options[ 'automatic_development_updates' ] && 'off' == $options[ 'automatic_major_updates' ] && 'off' == $options[ 'automatic_minor_updates' ] && 'off' == $options[ 'automatic_plugin_updates' ] && 'off' == $options[ 'automatic_theme_updates' ] && 'off' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'off';
			} elseif( 'off' == $options[ 'automatic_development_updates' ] && 'off' == $options[ 'automatic_major_updates' ] && 'on' == $options[ 'automatic_minor_updates' ] && 'default' == $options[ 'automatic_plugin_updates' ] && 'default' == $options[ 'automatic_theme_updates' ] && 'on' == $options[ 'automatic_translation_updates' ] ) {
				$options[ 'automatic_updates' ] = 'default';
			} else {
				$options[ 'automatic_updates' ] = 'custom';
			}
		}

		// return
		return $options;
	}

	public function permission_callback() {

		if ( current_user_can( 'install_plugins' ) ) {
			return true;
		}
		return false;
	}
}
