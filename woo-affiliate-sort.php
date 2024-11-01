<?php

/**
 *
 * @package   WooCommerce Affiliate Sort
 * @author    Abdelrahman Ashour < abdelrahman.ashour38@gmail.com >
 * @license   GPL-2.0+
 * @copyright 2018 Ash0ur


 * Plugin Name:  WooCommerce Affiliate Sort
 * Description:  A plugin that uses users clicks to order affiliate/external products by popularity.
 * Version:      1.0.0
 * Author:       Abdelrahman Ashour
 * Author URI:   https://profiles.wordpress.org/ashour
 * Contributors: ashour
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
		exit;
}


if ( ! class_exists( 'WooCommerce_affiliate_sort' ) ) :

	class WooCommerce_affiliate_sort {


		public static function init() {
			$theCalc = new self();
		}

		public function __construct() {

			$this->define_constants();
			$this->setup_actions();
		}



		public function define_constants() {

			   define( 'WOOAFFPRO_BASE_URL', trailingslashit( plugins_url( 'woocommerce-affiliate-sort' ) ) );
			   define( 'WOOAFFPRO_ASSETS_URL', trailingslashit( WOOAFFPRO_BASE_URL . 'assets' ) );
			   define( 'WOOAFFPRO_PATH', plugin_dir_path( __FILE__ ) );
		}

		public static function plugin_activated() {

			if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				die( 'WooCommerce plugin must be active' );

			}

		}



		public function frontend_enqueue_global() {

			if ( ! wp_script_is( 'jquery', 'registered' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			wp_enqueue_script( 'WOOAFFPRO_actions', WOOAFFPRO_ASSETS_URL . 'js/actions.js', array( 'jquery' ), WC_VERSION, true );

			wp_localize_script(
				'WOOAFFPRO_actions',
				'WOOAFFPRO_ajax_data',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'WOOAFFPRO_ajax_nonce' ),
				)
			);
		}


		public function setup_actions() {

			add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_global' ) );

			add_action( 'wp_ajax_nopriv_increase_external_populatiry', array( $this, 'increase_external_populatiry_func' ) );

			add_action( 'wp_ajax_increase_external_populatiry', array( $this, 'increase_external_populatiry_func' ) );
		}


		public function increase_external_populatiry_func() {

			check_ajax_referer( 'WOOAFFPRO_ajax_nonce', 'nonce' );

			$product_id = absint( $_POST['product_id'] );

			if ( $product_id && wc_get_product( $product_id )->get_type() == 'external' ) {

				$lastCount = get_post_meta( $product_id, 'total_sales', true );

				if ( ! empty( $lastCount ) ) {

					$updated = update_post_meta( $product_id, 'total_sales', $lastCount + 1 );

				} else {
					$updated = update_post_meta( $product_id, 'total_sales', 1 );
				}
			}

			wp_die();
		}


	}



	add_action( 'plugins_loaded', array( 'WooCommerce_affiliate_sort', 'init' ), 10 );

	register_activation_hook( __FILE__, array( 'WooCommerce_affiliate_sort', 'plugin_activated' ) );

endif;
