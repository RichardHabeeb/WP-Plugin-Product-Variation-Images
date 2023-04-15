<?php
/*
Plugin Name: Product Variation Images
Description: Allows users to add additional images to product variations in the product editor tab and display on front end by appending image to the product template front end.
Plugin URI: https://github.com/Matthewpco/WP-Plugin-Product-Variation-Images
Version: 1.3.0
Author: Gary Matthew Payne | BuiltMighty
Author URI: https://wpwebdevelopment.com/
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


// Add custom field to product variation settings
add_action( 'woocommerce_product_after_variable_attributes', 'pvi_add_variation_images_field', 10, 3 );
function pvi_add_variation_images_field( $loop, $variation_data, $variation ) {
	woocommerce_wp_text_input( array(
        'id' => 'pvi_variation_images_top[' . $loop . ']',
        'label' => __( 'Additional Images for top section (comma-separated URLs)', 'woocommerce' ),
        'value' => get_post_meta( $variation->ID, '_pvi_variation_images_top', true )
    ) );
	
    woocommerce_wp_text_input( array(
        'id' => 'pvi_variation_images[' . $loop . ']',
        'label' => __( 'Additional Images for spec section (comma-separated URLs)', 'woocommerce' ),
        'value' => get_post_meta( $variation->ID, '_pvi_variation_images', true )
    ) );
}


// Save custom field value
add_action( 'woocommerce_save_product_variation', 'pvi_save_variation_images_field', 10, 2 );
function pvi_save_variation_images_field( $variation_id, $i ) {
	if ( isset( $_POST['pvi_variation_images_top'][$i] ) ) {
        update_post_meta( $variation_id, '_pvi_variation_images_top', esc_attr( $_POST['pvi_variation_images_top'][$i] ) );
    }
    if ( isset( $_POST['pvi_variation_images'][$i] ) ) {
        update_post_meta( $variation_id, '_pvi_variation_images', esc_attr( $_POST['pvi_variation_images'][$i] ) );
    }
}


// Display additional images on the product page
add_filter( 'woocommerce_available_variation', 'pvi_display_variation_images', 10, 3 );
function pvi_display_variation_images( $data, $product, $variation ) {
	if ( $images = get_post_meta( $variation->get_id(), '_pvi_variation_images_top', true ) ) {
        $data['pvi_variation_images_top'] = array_map( 'trim', explode( ',', $images ) );
    }
    if ( $images = get_post_meta( $variation->get_id(), '_pvi_variation_images', true ) ) {
        $data['pvi_variation_images'] = array_map( 'trim', explode( ',', $images ) );
    }
    return $data;
}


// Enqueue script to handle image display on the frontend
add_action( 'wp_enqueue_scripts', 'pvi_enqueue_scripts' );
function pvi_enqueue_scripts() {
    if ( is_product() ) {
        wp_enqueue_script( 'pvi-variation-images', plugin_dir_url( __FILE__ ) . '/js/variation-images.js', array('jquery'), '1.0', true );
    }
}