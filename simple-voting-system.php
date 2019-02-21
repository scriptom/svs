<?php
/**
 * Plugin Name: Sistema de votacion sencillo
 * Description: Plugin que provee de un shortcode para la votacion/clasificacion de entradas en WordPress. Vease https://octuweb.com/como-crear-sistemas-de-votacion-para-web/ para mas informacion
 * Author: Tomás El Fakih, Marcel Marín
 * Version: 1.0
 */


define( 'SVS_BASE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SVS_TEMPLATES_DIR', SVS_BASE_DIR . 'templates/' );

define( 'SVS_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'SVS_ASSETS_URL', SVS_BASE_URL . 'assets/' );

define( 'SVS_MAX_SCORE', 5 );

/**
 * Cargamos los estilos en la cabecera
 */
add_action( 'wp_enqueue_scripts', 'svs_add_scripts', 99 );
function svs_add_scripts() {
	global $post;

	wp_enqueue_style( 'rating-css', SVS_ASSETS_URL . 'css/stars.css' );
	wp_enqueue_script( 'cookie-handler', SVS_ASSETS_URL . 'js/cookie.handler.js' );
	wp_enqueue_script( 'voting-logic', SVS_ASSETS_URL . 'js/voting.js', array( 'jquery', 'cookie-handler' ) );

	$context = array('ajaxurl' => admin_url( 'admin-ajax.php' ), 'max_score' => SVS_MAX_SCORE);
	if ( $post ) $context['post_id'] = $post->ID;
	wp_localize_script( 'voting-logic', 'context', $context );
}

/**
 * Shortcode a ser invocado en las entradas para tener la estructura HTML deseada
 */
add_shortcode( 'simple_voting_system', 'svs_shortcode' );
function svs_shortcode( $attrs = array() ) {
	// Necesitamos obtener el porcentaje de votos iniciales
	$puntaje = (int) get_post_meta( get_the_ID(), '_post_rating_avg', true );
	include SVS_TEMPLATES_DIR . 'star-voting.php';
}

/**
 * Manejador AJAX para el sistema de votacion
 */
add_action( "wp_ajax_svs_vote", 'svs_ajax_handler', 10 );
add_action( "wp_ajax_nopriv_svs_vote", 'svs_ajax_handler', 10 );
function svs_ajax_handler() {
	/* Tenemos que:
		1) Obtener el ID del post y el puntaje obtenido
		2) Obtener los datos almacenados del post
		3) Recalcular: Numero de votos, puntaje neto, puntaje tratado
		4) Actualizar el post_meta
		5) Devolver un json con la data

		Nota: Solamente escuchamos peticiones POST
	*/
	$retval = '';
	if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		if ( ! empty( $_POST['post_id'] ) ) {
			$post_id = $_POST['post_id'];
			$vote_score = $_POST['vote_score'];

			// Obtenemos los meta de las publicaciones
			$post_votes = get_post_meta( $post_id, '_post_votes', true );
			$post_rating_raw = get_post_meta( $post_id, '_post_rating_raw', true );
			$post_rating_avg = get_post_meta( $post_id, '_post_rating_avg', true );

			if ( ! $post_votes ) {
				// No tenemos votos, este es el primero
				$post_votes = 1;
				$post_rating_raw = $vote_score;
			} else {
				// Si tenemos votos, aumentemos la cantidad
				$post_votes = ((int) $post_votes) + 1;
				$post_rating_raw = ((int)$post_rating_raw) + $vote_score;
			}

			// Re-calculamos el nuevo puntaje
			$max_posible = $post_votes * SVS_MAX_SCORE;
			$post_rating_avg = $post_rating_raw * 100 / $max_posible;

			// Guardamos en el post meta
			update_post_meta( $post_id, '_post_votes', $post_votes );
			update_post_meta( $post_id, '_post_rating_raw', $post_rating_raw );
			update_post_meta( $post_id, '_post_rating_avg', $post_rating_avg );

			// Llenamos un JSON con la data que nos interesa: Votos totales y puntaje promedio
			$retval = json_encode( array(
				'votos' => $post_votes,
				'puntaje' => $post_rating_raw,
				'porcentaje' => $post_rating_avg
			) );

		}
	}
	echo $retval;

	wp_die();
}



add_filter( 'the_excerpt', 'do_shortcode', 11 );