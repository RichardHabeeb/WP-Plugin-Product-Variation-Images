<?php
/*
Plugin Name: Product Variation Images
Description: Allows users to add additional images to product variations in the product editor tab and display on front end by appending image to the product template front end.
Plugin URI: https://github.com/Matthewpco/WP-Plugin-Product-Variation-Images
Version: 1.4.0
Author: Gary Matthew Payne | BuiltMighty, Richard Habeeb
Author URI: https://wpwebdevelopment.com/
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


function pvi_convert_urls_to_ids($urls_str) {
    $urls = explode(",", $urls_str);
    $images = array();
    foreach($urls as $url) {
        if(!is_numeric($url)) {
            $images[] = attachment_url_to_postid((($url[0] == "/") ? get_site_url() : "" ) . $url);
        } else {
            $images[] = $url;
        }
    }
    return join(",", $images);
}

function pvi_convert_ids_to_urls($ids_str) {
    $ids = explode(",", $ids_str);
    $urls = array();
    foreach($ids as $id) {
        $urls[] = wp_get_attachment_url($id);
    }
    return join(",", $urls);
}


// Add custom field to product variation settings
add_action( 'woocommerce_product_after_variable_attributes', 'pvi_add_variation_images_field', 10, 3 );
function pvi_add_variation_images_field( $loop, $variation_data, $variation ) {

    $urls = get_post_meta( $variation->ID, '_pvi_variation_images', true );
    $image_ids = pvi_convert_urls_to_ids($urls);
?>
    <div>
        <p class="form-field form-row form-row-full">
            <label for="pvi-variation-images-<?= $loop ?>">Specification</label>
            <br>
            <a class="button-primary pvi-btn-add-image" href="#" aria-controls="pvi-variation-images-<?= $loop ?>">
                Add Specification Image
            </a>
            <input type="hidden" id="pvi-variation-images-<?= $loop ?>" name="pvi_variation_images[<?= $loop ?>]" value="<?= $image_ids ?>">
        </p>
    </div>

    <?php

    //woocommerce_wp_hidden_input( array(
    //    'id' => 'pvi-variation-images-top-' . $loop,
    //    'name' => 'pvi_variation_images_top[' . $loop . ']',
    //    'value' => get_post_meta( $variation->ID, '_pvi_variation_images_top', true )
    //) );
}


// Save custom field value
add_action( 'woocommerce_save_product_variation', 'pvi_save_variation_images_field', 10, 2 );
function pvi_save_variation_images_field( $variation_id, $i ) {
    if ( isset( $_POST['pvi_variation_images_top'][$i] ) ) {
        update_post_meta( $variation_id, '_pvi_variation_images_top', pvi_convert_ids_to_urls( esc_attr( $_POST['pvi_variation_images_top'][$i] ) ) );
    }
    if ( isset( $_POST['pvi_variation_images'][$i] ) ) {
        update_post_meta( $variation_id, '_pvi_variation_images', pvi_convert_ids_to_urls ( esc_attr( $_POST['pvi_variation_images'][$i] ) ) );
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


// Enqueue script to handle admin image upload
add_action( 'admin_enqueue_scripts', 'pvi_admin_enqueue_scripts' );
function pvi_admin_enqueue_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'pvi-variation-images', plugin_dir_url( __FILE__ ) . '/js/media-library.js', array('jquery'), '1.0', true );
}
