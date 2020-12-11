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
            'portfolio_filter',
            [
                'label' => __( 'Filter', 'efpw' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_filter',
            [
                'label'       => __( 'Show Filter', 'efpw' ),
                'type'        => \Elementor\Controls_Manager::SWITCHER ,
                'label_on' => __( 'Show', 'efpw' ),
                'label_off' => __( 'Hide', 'efpw' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'filterable-portfolio',
            [
                'label' => __( 'Portfolio', 'efpw' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'portfolio_per_page',
            [
                'label'     => __( 'Show Item Number', 'efpw' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'min'       => -1,
                'max'       => 100,
                'step'      => 1,
                'default'   => 9,
            ]
        );

        $this->add_control(
            'enable_load_more',
            [
                'label'       => __( 'Enable Load More', 'efpw' ),
                'type'        => \Elementor\Controls_Manager::SWITCHER ,
                'label_on' => __( 'Yes', 'efpw' ),
                'label_off' => __( 'No', 'efpw' ),
                'return_value' => 'yes',
                'default' => 'yes',
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

        $settings = $this->get_settings_for_display();

        ?>

        <script>
            jQuery(document).ready(function ($) {
                jQuery(".grid").mixitup({
                    targetSelector: ".mix"
                });
            });
        </script>

        <div class="efpw-filterable-portfolio-section container">

            <?php 
            $project_categories = get_terms( 'portfolio_cat' );
            
            if ( ( 'yes' == $settings['show_filter'] ) && $project_categories ) {
            ?>

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

            <?php } ?>

            <div class="row grid">
                <?php
                $query = new WP_Query( 
                    array( 
                        'post_type'      => 'portfolio',
                        'posts_per_page' => $settings['portfolio_per_page'], 
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                        'post_status'    => 'publish',
                    ) 
                );

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

                    <div class="column column-33 mix <?php echo esc_attr( $project_assigned_cat ); ?>">
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
                ?>
            </div>

            <?php if ( $settings['enable_load_more']  && $query ) { ?>

                <?php 
                $args = array(
                    'post_type'      => 'portfolio',
                    'posts_per_page' => 3,
                    'offset'         => $settings['portfolio_per_page'],
                    'orderby'        => 'date',
                    'order'          => 'DESC',
                    'post_status'    => 'publish',
                );
                ?>
                
                <div class="efpw-portfolio-load-more text-center">
                    <a href="#" id="efpw-load-more" class="button" data-query="<?php echo esc_js( json_encode( $args ) );?>">Load More</a>
                </div>
            <?php } ?>

        </div>

        <?php

    }

}
