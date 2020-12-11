<?php
/*
Plugin Name: Elementor Filterable Portfolio Widget
Plugin URI: http://wordpress.org
Description: Elementor Filterable Portfolio Widget
Author: Tanvirul Haque
Author URI: https://profiles.wordpress.org/tanvirul/
Version: 1.0.0
Text Domain: efpw
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }


if ( ! class_exists( 'EFPW_Elementor_Filterable_Portfolio' ) ) {
    /**
     * Elementor Filterable Portfolio Class
     *
     * The main class that initiates and runs the plugin.
     *
     * @since 1.0.0
     */
    class EFPW_Elementor_Filterable_Portfolio {

        /**
         * Plugin Version
         *
         * @since 1.0.0
         *
         * @var string The plugin version.
         */
        const VERSION = '1.0.0';

        /**
         * Minimum Elementor Version
         *
         * @since 1.0.0
         *
         * @var string Minimum Elementor version required to run the plugin.
         */
        const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

        /**
         * Minimum PHP Version
         *
         * @since 1.0.0
         *
         * @var string Minimum PHP version required to run the plugin.
         */
        const MINIMUM_PHP_VERSION = '7.0';

        /**
         * Instance
         *
         * @since 1.0.0
         *
         * @access private
         * @static
         *
         * @var EFPW_Elementor_Filterable_Portfolio The single instance of the class.
         */
        private static $_instance = null;

        /**
         * Instance
         *
         * Ensures only one instance of the class is loaded or can be loaded.
         *
         * @since 1.0.0
         *
         * @access public
         * @static
         *
         * @return EFPW_Elementor_Filterable_Portfolio An instance of the class.
         */
        public static function instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;

        }

        /**
         * Constructor
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function __construct() {

            add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );

        }

        /**
         * Define Constants
         */
        private function define_constants() {

            define( 'EFPW_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
            define( 'EFPW_PATH', plugin_dir_path( __FILE__ ) );

        }

        /**
         * Load Textdomain
         *
         * Load plugin localization files.
         *
         * Fired by `init` action hook.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function i18n() {

            load_plugin_textdomain( 'efpw', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

        }

        /**
         * On Plugins Loaded
         *
         * Checks if Elementor has loaded, and performs some compatibility checks.
         * If All checks pass, inits the plugin.
         *
         * Fired by `plugins_loaded` action hook.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function on_plugins_loaded() {

            if ( $this->is_compatible() ) {
                add_action( 'elementor/init', [ $this, 'init' ] );
            }

        }

        /**
         * Compatibility Checks
         *
         * Checks if the installed version of Elementor meets the plugin's minimum requirement.
         * Checks if the installed PHP version meets the plugin's minimum requirement.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function is_compatible() {

            // Check if Elementor installed and activated
            if ( ! did_action( 'elementor/loaded' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
                return false;
            }

            // Check for required Elementor version
            if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
                return false;
            }

            // Check for required PHP version
            if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
                return false;
            }

            return true;

        }

        /**
         * Initialize the plugin
         *
         * Load the plugin only after Elementor (and other plugins) are loaded.
         * Load the files required to run the plugin.
         *
         * Fired by `plugins_loaded` action hook.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function init() {
        
            $this->i18n();
            $this->define_constants();
            $this->includes();

            add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        }

        /**
         * Include plugin files
         */
        public function includes() {

            require_once( EFPW_PATH . 'includes/portfolio-post-type-taxonomy.php' );
            require_once( EFPW_PATH . 'includes/plugin-function.php' );

        }

        /**
         * Enqueue plugin scripts
         */
        public function enqueue_scripts() {

            wp_enqueue_style( 'elementor-filterable-portfolio', EFPW_URL . 'assets/css/elementor-filterable-portfolio.css' );

            wp_enqueue_script( 'mixitup', EFPW_URL . 'assets/js/jquery.mixitup.min.js', ['jquery'], '1.5.5', true );

            wp_enqueue_script( 'elementor-filterable-portfolio', EFPW_URL . 'assets/js/elementor-filterable-portfolio.js', ['jquery'], '1.0.0', true );

            wp_localize_script( 'elementor-filterable-portfolio', 'loadMorePortfolio', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

        }

        /**
         * Init Widgets
         *
         * Include widgets files and register them
         *
         * @since 1.0.0
         *
         * @access public
         */ 
        public function init_widgets() {

            // Include Widget files
            require_once( EFPW_PATH . 'includes/widgets/filterable-portfolio.php' );

            // Register widget
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \EFPW_Filterable_Portfolio() );

        }
        
        /**
         * Admin notice
         *
         * Warning when the site doesn't have Elementor installed or activated.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_missing_main_plugin() {

            if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor */
                esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'efpw' ),
                '<strong>' . esc_html__( 'Elementor Filterable Portfolio Widget', 'efpw' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'efpw' ) . '</strong>'
            );

            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have a minimum required Elementor version.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_minimum_elementor_version() {

            if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

            $message = sprintf(
                /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'efpw' ),
                '<strong>' . esc_html__( 'Elementor Filterable Portfolio Widget', 'efpw' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'efpw' ) . '</strong>',
                 self::MINIMUM_ELEMENTOR_VERSION
            );

            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have a minimum required PHP version.
         *
         * @since 1.0.0
         *
         * @access public
         */
        public function admin_notice_minimum_php_version() {

            if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

            $message = sprintf(
                /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'efpw' ),
                '<strong>' . esc_html__( 'Elementor Filterable Portfolio Widget', 'efpw' ) . '</strong>',
                '<strong>' . esc_html__( 'PHP', 'efpw' ) . '</strong>',
                 self::MINIMUM_PHP_VERSION
            );

            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

        }

    }

    EFPW_Elementor_Filterable_Portfolio::instance();
}



