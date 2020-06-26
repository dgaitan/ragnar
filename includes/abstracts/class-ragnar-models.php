<?php
/**
 * Ragnar Abstract Model Class
 *
 * @since 1.0.0
 * @package ragnar/includes
 * @subpackage abstracts
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abstract that implements the shared
 * methods used on all post types interpreted
 * as models.
 *
 * @version  1.0.0
 * @package  ragnar/includes/abstracts
 */
abstract class Ragnar_Model extends Ragnar_WP_Helpers {
    /**
     * Theme or Plugin Slug
     *
     * @var string
     */
    private $instance_slug;

    /**
     * Post Type Slug
     *
     * @var string
     */
    public $post_type_slug;

    /**
     * Singular Post Type Name
     *
     * @var string
     */
    public $singular_name;

    /**
     * Plural Post Type Name
     *
     * @var string
     */
    public $plural_name;

    /**
     * Post Type Slug
     *
     * @var string
     */
    public $slug;

    /**
     * Post Type Description
     *
     * @var string
     */
    public $description;

    /**
     * Post Type Element Supports
     *
     * @var array
     */
    public $supports;

    /**
     * Post Type Icon on Dash
     *
     * @var string
     */
    public $icon;

    /**
     * Run a Query to DB
     *
     * @param array $args
     * @return array
     */
    public function where( $args = array() ) {
        $args    = $this->get_args( $args );
        $results = get_posts( $args );

        if ( method_exists( $this, 'set_custom_fields' ) && $results ) {
            foreach ( $results as $item ) {
                $item = $this->set_custom_fields( $item );
            }
        }
        return $results;
    }

    /**
     * Get an object by ID
     *
     * @param integer $id
     * @return array
     */
    public function get( $id = 0 ) {
        $post = get_post( $id );
        
        if ( method_exists( $this, 'set_custom_fields' ) ) {
            $post = $this->set_custom_fields( $post );
        }

        return $post;
    }

    /**
     * Post Type Register
     *
     * @return array
     */
    public function post_type_register() {
        
        if ( property_exists( $this, 'post_type_slug' ) && method_exists( $this, 'post_type_args' ) ) {
            register_post_type( $this->post_type_slug, $this->post_type_args() );
        }
    }

    /**
     * Post Type Args
     *
     * @return array
     */
    public function post_type_args() {
        return array(
            'label'                 => $this->plural_name,
            'description'           => $this->description,
            'labels'                => $this->get_register_labels(),
            'supports'              => $this->supports,
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => $this->icon,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'rewrite'               => $this->get_rewrite_setup(),
            'capability_type'       => 'page',
        );
    }

    /**
     * Register custom taxonomies
     *
     * @return void
     */
    public function register_custom_taxonomies() {
        if ( property_exists( $this, 'taxonomies' ) ) {
            foreach ( $this->taxonomies as $key => $value ) {
                $labels = array(
                    'name'                       => _x( $value['plural'], 'Taxonomy General Name', $this->instance_slug ),
                    'singular_name'              => _x( $value['singular'], 'Taxonomy Singular Name', $this->instance_slug ),
                    'menu_name'                  => __( $value['plural'], $this->instance_slug ),
                    'all_items'                  => __( 'All Items', $this->instance_slug ),
                    'parent_item'                => __( 'Parent Item', $this->instance_slug ),
                    'parent_item_colon'          => __( 'Parent Item:', $this->instance_slug ),
                    'new_item_name'              => __( 'New Item Name', $this->instance_slug ),
                    'add_new_item'               => __( 'Add New Item', $this->instance_slug ),
                    'edit_item'                  => __( 'Edit Item', $this->instance_slug ),
                    'update_item'                => __( 'Update Item', $this->instance_slug ),
                    'view_item'                  => __( 'View Item', $this->instance_slug ),
                    'separate_items_with_commas' => __( 'Separate items with commas', $this->instance_slug ),
                    'add_or_remove_items'        => __( 'Add or remove items', $this->instance_slug ),
                    'choose_from_most_used'      => __( 'Choose from the most used', $this->instance_slug ),
                    'popular_items'              => __( 'Popular Items', $this->instance_slug ),
                    'search_items'               => __( 'Search Items', $this->instance_slug ),
                    'not_found'                  => __( 'Not Found', $this->instance_slug ),
                    'no_terms'                   => __( 'No items', $this->instance_slug ),
                    'items_list'                 => __( 'Items list', $this->instance_slug ),
                    'items_list_navigation'      => __( 'Items list navigation', $this->instance_slug ),
                );
                $rewrite = array(
                    'slug'                       => $value['slug'],
                    'with_front'                 => true,
                    'hierarchical'               => false,
                );
                $args = array(
                    'labels'                     => $labels,
                    'hierarchical'               => true,
                    'public'                     => true,
                    'show_ui'                    => true,
                    'show_admin_column'          => true,
                    'show_in_nav_menus'          => true,
                    'show_tagcloud'              => true,
                    'rewrite'                    => $rewrite,
                );

                register_taxonomy( $key, array( $this->post_type_slug ), $args );
            }
        }

    }

    //
    // ====== Private Functions ======
    //

    /**
     * Method to retrieve the args for a query
     *
     * @param array $args
     * @return array
     */
    private function get_args( $args = array() ) {
        $defaults = array(
            'post_type'      => $this->post_type_slug,
            'posts_per_page' => get_option( 'posts_per_page' ),
            'post_status'    => 'publish'
        );

        return wp_parse_args( $args, $defaults );
    }
    
    /**
     * Register Labels
     *
     * @since 1.0.0
     * @return array
     */
    private function get_register_labels() {
        return array(
            'name'                  => _x( $this->plural_name, 'Post Type General Name', $this->instance_slug ),
            'singular_name'         => _x( $this->singular_name, 'Post Type Singular Name', $this->instance_slug ),
            'menu_name'             => __( $this->plural_name, $this->instance_slug ),
            'name_admin_bar'        => __( $this->singular_name, $this->instance_slug ),
            'archives'              => __( 'Item Archives', $this->instance_slug ),
            'attributes'            => __( 'Item Attributes', $this->instance_slug ),
            'parent_item_colon'     => __( 'Parent Item:', $this->instance_slug ),
            'all_items'             => __( 'All Items', $this->instance_slug ),
            'add_new_item'          => __( 'Add New Item', $this->instance_slug ),
            'add_new'               => __( 'Add New', $this->instance_slug ),
            'new_item'              => __( 'New Item', $this->instance_slug ),
            'edit_item'             => __( 'Edit Item', $this->instance_slug ),
            'update_item'           => __( 'Update Item', $this->instance_slug ),
            'view_item'             => __( 'View Item', $this->instance_slug ),
            'view_items'            => __( 'View Items', $this->instance_slug ),
            'search_items'          => __( 'Search Item', $this->instance_slug ),
            'not_found'             => __( 'Not found', $this->instance_slug ),
            'not_found_in_trash'    => __( 'Not found in Trash', $this->instance_slug ),
            'featured_image'        => __( 'Featured Image', $this->instance_slug ),
            'set_featured_image'    => __( 'Set featured image', $this->instance_slug ),
            'remove_featured_image' => __( 'Remove featured image', $this->instance_slug ),
            'use_featured_image'    => __( 'Use as featured image', $this->instance_slug ),
            'insert_into_item'      => __( 'Insert into item', $this->instance_slug ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', $this->instance_slug ),
            'items_list'            => __( 'Items list', $this->instance_slug ),
            'items_list_navigation' => __( 'Items list navigation', $this->instance_slug ),
            'filter_items_list'     => __( 'Filter items list', $this->instance_slug ),
        );
    }

    /**
     * Post Type Rewrite Setup
     *
     * @return array
     */
    private function get_rewrite_setup() {
        return array(
            'slug'                  => $this->slug,
            'with_front'            => true,
            'pages'                 => true,
            'feeds'                 => true,
        );
    }
}