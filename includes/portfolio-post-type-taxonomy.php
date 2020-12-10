<?php 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Register Portfolio Post Type and Taxonomy
 */
function efpw_register_portfolio_post_type_and_taxonomy() {

    // Register Portfolio Post Type
    register_post_type( 'portfolio',
        array(
            'labels'    => array(
                'name'          => esc_html__( 'Portfolios', 'Post Type General Name', 'efpw' ),
                'singular_name' => esc_html__( 'Portfolio', 'Post Type Singular Name', 'efpw' )
            ),
            'supports'  => array( 'title', 'editor', 'thumbnail' ),
            'menu_icon' => 'dashicons-portfolio',
            'public'    => true,
        )
    );

    // Register Portfolio Taxonomy
    register_taxonomy(
        'portfolio_cat',
        'portfolio',
        array(
            'hierarchical'      => true,
            'label'             => esc_html__( 'Categories', 'efpw' ),
            'query_var'         => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'       => 'portfolio-category',
                'with_front' => true
            )
        )
    );

}

add_action( 'init', 'efpw_register_portfolio_post_type_and_taxonomy' );