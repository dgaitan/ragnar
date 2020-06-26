<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://dgaitan.dev
 * @since      1.0.0
 *
 * @package    Ragnar
 * @subpackage Ragnar/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ragnar
 * @subpackage Ragnar/includes
 * @author     David Gaitán <jdavid.gaitan@gmail.com>
 */
class Ragnar {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ragnar_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The Theme or Plugin Slug
	 *
	 * @since 	1.0.0
	 * @access  protected
	 * @var 	string
	 */
	protected $instance_slug;

	/**
     * The single instance of the class
     *
     * @var Ragnar
     */
    protected static $_instance = null;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'RAGNAR_VERSION' ) ) {
			$this->version = RAGNAR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ragnar';

		$this->load_constants();
		$this->load_core_classes();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Prepare the instance
	 *
	 * @return Ragnar
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}

	/**
	 * Set the instance slug
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $instance_slug
	 */
	public function set_instance_slug( $instance_slug = '' ) {
		$this->instance_slug = $instance_slug;
	}

	/**
	 * Get the instance slug.
	 * 
	 * The theme or plugin slug, this is in dependes
	 * where are you're working.
	 *
	 * @since 	1.0.0
	 * @access	public
	 * @return 	string
	 */
	public function get_instance_slug() {
		return ! empty( $this->instance_slug ) ? $this->instance_slug : $this->plugin_name;
	}

	/**
	 * Load Constants
	 *
	 * @since 	1.0.0
	 * @access 	private
	 */
	private function load_constants() {
		$this->maybe_define_constant( 'RAGNAR_SLUG', $this->plugin_name );
		$this->maybe_define_constant( 'RAGNAR_INSTANCE_SLUG', $this->get_instance_slug() );
        $this->maybe_define_constant( 'RAGNAR_PATH', plugin_dir_path() );
        $this->maybe_define_constant( 'RAGNAR_INCLUDES_PATH', RAGNAR_PATH . "/includes" );
	}

	/**
	 * Load all the core class from Ragnar Collection
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_core_classes() {

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ragnar_Loader. Orchestrates the hooks of the plugin.
	 * - Ragnar_i18n. Defines internationalization functionality.
	 * - Ragnar_Admin. Defines all hooks for the admin area.
	 * - Ragnar_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ragnar-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ragnar-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ragnar-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ragnar-public.php';

		$this->loader = new Ragnar_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ragnar_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ragnar_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ragnar_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ragnar_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ragnar_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Define constants if not present.
	 *
	 * @since  1.1.0
	 *
	 * @return boolean
	 */
	protected function maybe_define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}
