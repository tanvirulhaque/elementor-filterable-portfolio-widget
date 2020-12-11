<?php 

/**
 * Load more portfolio Ajax function
 */
function efpw_load_more_portfolio() {

	$args = $_POST['query'];

    $query = new WP_Query( $args );

    ob_start();

    while ( $query->have_posts() ) : $query->the_post();

        $portfolio_id = get_the_ID();
        $portfolio_title = get_the_title();
        $project_category = get_the_terms( $portfolio_id, 'portfolio_cat' );

        if ( $project_category && ! is_wp_error( $project_category ) ) {

            $project_cat_list = array();

            foreach ( $project_category as $category ) {
                $project_cat_list[] = $category->slug;
            }

            $project_assigned_cat = join( " ", $project_cat_list );

        } else {

            $project_assigned_cat = '';

        }

        if ( $project_category && ! is_wp_error( $project_category ) ) {

            $project_cat_list = array();

            foreach ( $project_category as $category ) {
                $project_cat_list[] = $category->name;
            }

            $project_assigned_cat_name = join( ", ", $project_cat_list );

        } else {

            $project_assigned_cat_name = '';

        }
        ?>

        <div class="column column-33 mix <?php echo esc_attr( $project_assigned_cat ); ?> mix_all" style="display: inline-block; opacity: 1;">
            <div class="portfolio__single-item">
                <img src="<?php echo get_the_post_thumbnail_url( $portfolio_id, 'large' ); ?>" class="img-responsive" alt="<?php echo $portfolio_title; ?>">

                <div class="portfolio__single-item__overlay">
                    <div class="portfolio__single-item__heading">
                        <h4 class="portfolio__single-item__title"><a href="<?php echo get_permalink(); ?>"><?php echo $portfolio_title; ?></a></h4>
                        <div class="portfolio__single-item__desc">
                            <p><?php echo esc_html( $project_assigned_cat_name ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php
    endwhile;
    wp_reset_query();

    $html = ob_get_contents();
	ob_get_clean();

	$args['offset'] = intval( $args['offset'] ) + $args['posts_per_page'];

	wp_send_json_success( [
		'query' => $args,
		'posts' => $html,
	] );

    wp_die();
}

add_action( 'wp_ajax_efpw_load_more_portfolio', 'efpw_load_more_portfolio' );
add_action( 'wp_ajax_nopriv_efpw_load_more_portfolio', 'efpw_load_more_portfolio' );