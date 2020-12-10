<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Filterable Portfolio Widget.
 *
 * @since 1.0.0
 */
class EFPW_Filterable_Portfolio extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve Filterable Portfolio widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'filterable-portfolio';
    }

    /**
     * Get widget title.
     *
     * Retrieve Filterable Portfolio widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title() {
        return __( 'Filterable Portfolio', 'efpw' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve Filterable Portfolio widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Filterable Portfolio widget belongs to.
     *
     * @return array Widget categories.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_categories() {
        return [ 'general' ];
    }

    /**
     * Register Filterable Portfolio widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'efpw' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'url',
            [
                'label'       => __( 'URL to embed', 'efpw' ),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'input_type'  => 'url',
                'placeholder' => __( 'https://your-link.com', 'efpw' ),
            ]
        );

        $this->end_controls_section();

    }

    /**
     * Render Filterable Portfolio widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {

        $settings           = $this->get_settings_for_display();
        $project_categories = get_terms( 'portfolio_cat' );

        ?>

        <script>
            jQuery(document).ready(function ($) {
                jQuery(".grid").mixitup({
                    targetSelector: ".mix"
                });
            });
        </script>

        <div class="efpw-filterable-portfolio-section">

            <div class="portfolio__filter text-center">
                <ul class="list-unstyled">
                    <li class="filter active" data-filter="all">all</li>

                    <?php
                    if ( ! empty( $project_categories ) && ! is_wp_error( $project_categories ) ) {
                        foreach ( $project_categories as $category ) {
                            echo '<li class="filter" data-filter="' . $category->slug . '">' . $category->name . '</li>';
                        }
                    }
                    ?>

                </ul>
            </div>

            <div class="row grid">
                <?php
                $query = new WP_Query( array( 'posts_per_page' => 9, 'post_type' => 'portfolio' ) );

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

                    <div class="col-md-4 mix <?php echo esc_attr( $project_assigned_cat ); ?>">
                        <div class="portfolio__single-item">
                            <img src="<?php echo get_the_post_thumbnail_url( $portfolio_id, 'large' ); ?>"
                                 class="img-responsive" alt="<?php echo $portfolio_title; ?>">

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
                ?>
            </div>
        </div>

        <?php

    }

}
