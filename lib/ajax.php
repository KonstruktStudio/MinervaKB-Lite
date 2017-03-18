<?php
/**
 * Project: Minerva KB Lite
 * Copyright: 2015-2017 @KonstruktStudio
 */

require_once( MINERVA_KB_PLUGIN_DIR . 'lib/helpers/settings-builder.php' );

class MinervaKB_Ajax {

	const NONCE = 'minerva_kb_nonce';
	const NONCE_KEY = 'minerva_kb_ajax_nonce';

	public function __construct($deps) {
		$this->register();
	}

	/**
	 * Registers actions handlers
	 */
	public function register() {

		// save settings
		add_action( 'wp_ajax_mkb_save_settings', array( $this, 'save_settings' ) );

		// reset settings
		add_action( 'wp_ajax_mkb_reset_settings', array( $this, 'reset_settings' ) );

		// search
		add_action( 'wp_ajax_mkb_kb_search', array( $this, 'ajax_kb_search' ) );
		add_action( 'wp_ajax_nopriv_mkb_kb_search', array( $this, 'ajax_kb_search' ) );

		// pageview tracking
		add_action( 'wp_ajax_mkb_article_pageview', array( $this, 'article_pageview' ) );
		add_action( 'wp_ajax_nopriv_mkb_article_pageview', array( $this, 'article_pageview' ) );
	}

	public static function get_nonce() {
		return self::NONCE;
	}

	public static function get_nonce_key() {
		return self::NONCE_KEY;
	}

	protected function send_security_error() {
		echo json_encode( array(
			'status' => 1,
			'errors' => array(
				'global' => array(
					array(
						'code' => 4001,
						'error_message' => __( 'Security or timeout error. Sorry, you cannot currently perform this action. Try to refresh the page or login.', 'minerva-kb' )
					)
				)
			)
		) );

		wp_die();
	}

	/**
	 * Checks user and checks if he is admin
	 */
	protected function check_admin_user() {
		if ( ! current_user_can( 'administrator' ) ) {
			$this->send_security_error();
		}

		$this->check_user();
	}

	/**
	 * Checks if user is really user
	 */
	protected function check_user() {
		if ( ! check_ajax_referer( self::get_nonce(), 'nonce_value', false ) ) {
			$this->send_security_error();
		}
	}

	/**
	 * Search handler
	 */
	public function ajax_kb_search() {
		$this->check_user();

		global $post;

		$search = trim( $_POST['search'] );
		$search_results = array();
		$is_specific_topics = isset( $_POST['topics'] ) && $_POST['topics'] != '';

		// search by content
		$query_args = array(
			'post_type' => MKB_Options::option( 'article_cpt' ),
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
			's' => $search,
			'order_by' => 'relevance'
		);

		if ( $is_specific_topics ) {
			$specific_topics_query = array(
				array(
					'taxonomy' => MKB_Options::option( 'article_cpt_category' ),
					'field' => 'term_id',
					'terms' => array_map( function ( $string_id ) {
						return (int) $string_id;
					}, explode( ',', $_POST['topics'] ) ),
					'operator' => 'IN',
				),
			);

			$query_args['tax_query'] = $specific_topics_query;
		}

		$search_loop = new WP_Query( $query_args );

		if ( $search_loop->have_posts() ) :
			while ( $search_loop->have_posts() ) : $search_loop->the_post();
				$topics_list = wp_get_post_terms( $post->ID, MKB_Options::option( 'article_cpt_category' ), array( "fields" => "names" ) );

				array_push( $search_results, array(
					"id" => $post->ID,
					"title" => get_the_title(),
					"link" => get_the_permalink(),
					"topics" => $topics_list
				) );
			endwhile;
		endif;
		wp_reset_postdata();

		$status = 0;

		echo json_encode( array(
			'search' => $search,
			'result' => $search_results,
			'status' => $status
		) );

		wp_die();
	}

	/**
	 *
	 * @param $post_id
	 * @param $key
	 */
	protected function update_count_meta( $post_id, $key ) {
		$now = time();
		$begin_of_day = strtotime( "midnight", $now );

		$current_count_meta_raw = get_post_meta( $post_id, $key, true );
		$current_count_meta = array();

		if ( $current_count_meta_raw ) {
			$current_count_meta = json_decode( $current_count_meta_raw, true );
		}

		if ( ! array_key_exists( $begin_of_day, $current_count_meta ) ) {
			$current_count_meta[ $begin_of_day ] = 0;
		}

		$current_day_count = (int) $current_count_meta[ $begin_of_day ];
		$current_count_meta[ $begin_of_day ] = ++ $current_day_count;

		update_post_meta( $post_id, $key, json_encode( $current_count_meta ) );
	}

	/**
	 * Article pageview
	 */
	public function article_pageview() {
		$this->check_user();

		$article_id = (int) $_POST['id'];
		$article    = get_post( $article_id );

		if ( $article === null ) {
			wp_die();
		}

		$current_views = (int) get_post_meta( $article_id, '_mkb_views', true );
		update_post_meta( $article_id, '_mkb_views', ++ $current_views );

		$this->update_count_meta( $article_id, '_mkb_views_meta' );

		$status = 0;

		echo json_encode( array(
			'status' => $status
		) );

		wp_die();
	}

	/**
	 * Saves plugin settings
	 */
	public function save_settings() {
		$this->check_admin_user();

		$settings = $_POST['settings'];

		if ( ! $settings || empty( $settings ) ) {
			wp_die();
		}

		MKB_Options::save( $settings );

		$status = 0;

		echo json_encode( array(
			'status' => $status
		) );

		wp_die();
	}

	/**
	 * Resets plugin settings
	 */
	public function reset_settings() {
		$this->check_admin_user();

		MKB_Options::reset();

		$status = 0;

		echo json_encode( array(
			'status' => $status
		) );

		wp_die();
	}
}
