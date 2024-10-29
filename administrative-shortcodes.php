<?php
/*
Plugin Name:  Administrative Shortcodes
Version:      0.3.4
Author:       Hassan Derakhshandeh
Description:  A set of shortcodes for the site admins.
Text Domain:  administrative-shortcodes
Domain Path:  /languages

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * If shortcode
 *
 * Run shortcode only if a desired condition is met
 *
 * Originally published as Conditional Tags Shortcode
 * @link https://wordpress.org/plugins/conditional-tags-shortcode/
 */
function administrative_shortcodes_if( $atts, $content = null ) {

	foreach( $atts as $key => $value ) {
		/* normalize empty attributes */
		if( is_int( $key ) ) {
			$key = $value;
			$value = true;
		}

		$reverse_logic = false;
		if( substr( $key, 0, 4 ) == 'not_' ) {
			$reverse_logic = true;
			$key = substr( $key, 4 );
		}
		if( function_exists( $key ) ) {
			$values = ( true === $value ) ? null : array_filter( explode( ',', $value ) );
			$result = call_user_func( $key, $values );
			if( $result !== $reverse_logic ) {
				return do_shortcode( $content );
			}
		}
	}

	return '';
}
add_shortcode( 'if', 'administrative_shortcodes_if' );

/**
 * Swich To Blog shortcode
 *
 * Enables running shortcode in the context of another website in WPMU
 */
function administrative_shortcodes_switch_to_blog( $atts, $content = null ) {
	global $wpdb;

	extract( shortcode_atts(
		array(
			'id' => 0,
			'url' => '',
		),
		$atts,
		'switch_to_blog' )
	);

	if( $id == 0 && ! empty( $url ) ) {
		if( is_subdomain_install() ) {
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE domain = %s and path = %s /* get_blog_id_from_url */", $url, '/' ) );
		} else {
			$id = $wpdb->get_var( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE domain = %s and path = %s /* get_blog_id_from_url */", DOMAIN_CURRENT_SITE, trailingslashit( PATH_CURRENT_SITE ) . trailingslashit( $url ) ) );
		}
	}

	$output = '';
	if( $id != 0 && ! empty( $content ) ) {
		switch_to_blog( $id );
		$output = do_shortcode( $content );
		restore_current_blog();
	}

	return $output;
}
if( is_multisite() )
	add_shortcode( 'switch_to_blog', 'administrative_shortcodes_switch_to_blog' );

/**
 * Iterator shortcode
 *
 * Run shortcode only after certain repeat times
 * Requires an "id" parameter which should be a unique string (anything)
 */
function administrative_shortcodes_iterator( $atts, $content = null ) {
	static $count = array();

	extract( shortcode_atts(
		array(
			'id' => '',
			'repeat' => 1
		),
		$atts,
		'iterator' )
	);

	$output = '';
	if( ! empty( $id ) ) {
		if( ! isset( $count[$id] ) ) {
			// do not show any output for the first time
			$count[$id] = 0;
		} elseif( 0 == $count[$id] % $repeat ) {
			$output = do_shortcode( $content );
			$count[$id] = 0;
		}

		$count[$id]++;
	}

	return $output;
}
add_shortcode( 'iterator', 'administrative_shortcodes_iterator' );

/**
 * Get Template Part shortcode
 *
 * Loads a template file from parent or child theme
 */
function administrative_shortcodes_get_template( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'slug' => '',
		'name' => null
	), $atts, 'get_template' );

	ob_start();
	get_template_part( $atts['slug'], $atts['name'] );
	return ob_get_clean();
}
add_shortcode( 'get_template', 'administrative_shortcodes_get_template' );

/**
 * Scheduler shortcode
 *
 * Show content if certain date has passed
 */
function administrative_shortcodes_scheduler( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'date' => '',
		'repeat' => '' // @TODO
	), $atts, 'scheduler' );

	$epoch = strtotime( $atts['date'] );
	if ( $epoch < time() ) {
		return do_shortcode( $content );
	}
}
add_shortcode( 'scheduler', 'administrative_shortcodes_scheduler' );

/**
 * Date shortcode
 *
 * Shows current date, using desired format
 */
function administrative_shortcodes_date( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'format' => get_option( 'date_format' ),
	), $atts, 'date' );

	return date_i18n( $atts['format'] );
}
add_shortcode( 'date', 'administrative_shortcodes_date' );

/**
 * loginoutlink shortcode
 *
 * Display a link to login page if user is not logged-in, or a logout page if they are
 */
function administrative_shortcodes_loginoutlink( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'login' => __( 'Log in', 'administrative-shortcodes' ),
		'logout' => __( 'Log out', 'administrative-shortcodes' ),
		'redirect' => '',
	), $atts, 'loginoutlink' );

	if ( ! is_user_logged_in() )
		$link = '<a href="' . esc_url( wp_login_url( $atts['redirect'] ) ) . '">' . $atts['login'] . '</a>';
	else
		$link = '<a href="' . esc_url( wp_logout_url( $atts['redirect'] ) ) . '">' . $atts['logout'] . '</a>';

	/** This filter is documented in wp-includes/general-template.php */
	return apply_filters( 'loginout', $link );
}
add_shortcode( 'loginoutlink', 'administrative_shortcodes_loginoutlink' );

/**
 * wp_login_form shortcode
 *
 * Display the login form
 */
function administrative_shortcodes_login_form( $atts, $content = null ) {
	$atts['echo'] = false;
	return wp_login_form( $atts );
}
add_shortcode( 'login_form', 'administrative_shortcodes_login_form' );

/**
 * Custom Field shortcode
 * Display a custom field from a chosen post
 */
function administrative_shortcodes_custom_field( $atts, $content = null ) {
	$atts = shortcode_atts( array(
		'key' => '',
		'post_id' => get_the_id(),
		'type' => '',
		'after' => '',
		'before' => '',
	), $atts, 'custom_field' );

	if ( $output = get_post_meta( $atts['post_id'], $atts['key'], true ) ) {
		$output = $atts['before'] . $output . $atts['after'];
	}

	return $output;
}
add_shortcode( 'custom_field', 'administrative_shortcodes_custom_field' );

/**
 * Shows current post id
 */
function administrative_shortcodes_the_id() {
	return get_the_id();
}
add_shortcode( 'the_id', 'administrative_shortcodes_the_id' );

/**
 * Disable shortcode
 * Shortcode to disable some content from showing up in the page
 */
function administrative_shortcodes_disable( $atts, $content ) {
	return '';
}
add_shortcode( 'disable', 'administrative_shortcodes_disable' );

/**
 * Path shortcodes
 * Shortcode to get the path to something
 */
function administrative_shortcodes_call( $atts, $content, $code ) {
	return $code();
}
add_shortcode( 'home_url', 'administrative_shortcodes_call' );
add_shortcode( 'get_template_directory', 'administrative_shortcodes_call' );
add_shortcode( 'get_template_directory_uri', 'administrative_shortcodes_call' );
add_shortcode( 'get_stylesheet_directory', 'administrative_shortcodes_call' );
add_shortcode( 'get_stylesheet_directory_uri', 'administrative_shortcodes_call' );

/**
 * Returns a random number between "min" (default 0) and "max" (default 100)
 */
function administrative_shortcodes_rand( $atts ) {
	$atts = shortcode_atts( array(
		'min' => 0,
		'max' => 100,
	), $atts, 'rand' );

	return rand( (int) $atts['min'], (int) $atts['max'] );
}
add_shortcode( 'rand', 'administrative_shortcodes_rand' );

/**
 * Get a value from global variables.
 */
function administrative_shortcodes_get_var( $atts ) {
	$atts = shortcode_atts( array(
		'get' => '',
		'post' => '',
	), $atts, 'get_var' );

	$value = '';
	if ( isset( $_GET[ $atts['get'] ] ) ) {
		$value = $_GET[ $atts['get'] ];
	} else if ( isset( $_POST[ $atts['post'] ] ) ) {
		$value = $_POST[ $atts['post'] ];
	}

	return $value;
}
add_shortcode( 'get_var', 'administrative_shortcodes_get_var' );