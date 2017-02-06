<?php
/**
 * Contains class for the plugin.
 *
 * @package Speed Up Optimize \ Main
 * @author Carl Alberto
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class used for the main plugin functions.
 */
class Speed_Up_Optimize {

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @param string $file Name of this file.
	 * @param string $version Version of this plugin.
	 * @param array  $pluginoptions Contains various options for the plugin.
	 * @return  void
	 */
	public function __construct( $file = '', $version = '1.0.0', $pluginoptions = array() ) {
		$this->_version = $version;
		$this->_token = 'speed_up_optimize';
		$this->base = $pluginoptions['settings_prefix'];

		// Load plugin environment variables.
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		register_deactivation_hook( $this->file, array( $this, 'plugin_deactivated' ) );

		// Load frontend JS & CSS.

		// Load admin JS & CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new Speed_Up_Optimize_Admin_API();
		}

		add_action( 'wp_footer', array( $this, 'get_scripts_list' ), 0 );

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()


	/**
	 * Gets the functions.
	 */
	public function get_scripts_list() {
			$the_list = new Speed_Up_Optimize_Main;
			return $the_list->display_scripts();
	}


	/**
	 * The single instance of Speed_Up_Optimize.
	 *
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Array for plugin settings.
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $pluginoptions;

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @param	srting $hook Can be blank at the moment.
	 * @return  void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @param	string $hook Can be blank at the moment.
	 * @return  void
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'speed-up-optimize', false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
	    $domain = 'speed-up-optimize';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Speed_Up_Optimize Instance.
	 *
	 * Ensures only one instance of Speed_Up_Optimize is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Speed_Up_Optimize()
	 * @param string $file Name of this file.
	 * @param string $version Version of this plugin.
	 * @param array  $pluginoptions Contains various options for the plugin.
	 * @return Main Speed_Up_Optimize instance
	 */
	public static function instance( $file = '', $version = '1.0.0', $pluginoptions = array() ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version, $pluginoptions );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html_e( 'Cheatin&#8217; huh?', 'speed-up-optimize' ), esc_html( $this->parent->_version ) );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html_e( 'Cheatin&#8217; huh?', 'speed-up-optimize' ), esc_html( $this->parent->_version ) );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * If reset option is still new meaning this is the first time th plugins is installed, it will call all the default values.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
		if ( ( empty( get_option( $this->base . 'cb_reset' ) ) ) ) {
			$this->default_option_values();
		}
	} // End install ()

	/**
	 * This contains the default values.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function default_option_values() {
		// assign ON to set checkboxes.
		add_option( $this->base . 'cb_reset', '' );
	}

	/**
	 *  Deletes all options in the specified array. Normally this is the place to reset your options.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @param  array $option_names      names of the options to be deleted.
	 * @return  void
	 */
	public function remove_all_options( $option_names ) {
		foreach ( $option_names as $option_name ) {
			delete_option( $option_name );
		}
	}

	/**
	 *  Runs when plugin is deactivated.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function plugin_deactivated() {
		if ( ! empty( get_option( $this->base . 'cb_reset' ) ) ) {
			$option_names = array(
					$this->base . 'cb_reset',
				);
			$this->remove_all_options( $option_names );
		}
	}

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
