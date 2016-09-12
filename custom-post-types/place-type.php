<?php

/* ==================================================

Places Post Type Functions

================================================== */


$args = array(
    "label"                         => _x('Categorie siti', 'category label', "mim-admin"), 
    "singular_label"                => _x('Categoria siti', 'category singular label', "mim-admin"), 
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => false,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => false,
    'query_var'                     => true
);
register_taxonomy( 'place-category', 'place', $args );

add_action('init', 'places_register');  
function places_register() {
		
    $labels = array(
        'name' => _x('Siti', 'post type general name', "mim-admin"),
        'singular_name' => _x('Sito', 'post type singular name', "mim-admin"),
        'add_new' => _x('Aggiungi nuovo', 'place', "mim-admin"),
        'add_new_item' => __('Aggiungi un nuovo sito', "mim-admin"),
        'edit_item' => __('Modifica sito', "mim-admin"),
        'new_item' => __('Nuovo sito', "mim-admin"),
        'view_item' => __('Vedi sito', "mim-admin"),
        'search_items' => __('Cerca siti', "mim-admin"),
        'not_found' =>  __('Nessun sito inserito', "mim-admin"),
        'not_found_in_trash' => __('Cestino vuoto', "mim-admin"),
        'parent_item_colon' => ''
    );
		
    $args = array(  
        'labels' => $labels,  
        'public' => true,  
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_nav_menus' => false,
        'menu_icon'=> 'dashicons-location',
        'hierarchical' => false,
        'rewrite' => false,
        'supports' => array('title'),
        'has_archive' => true,
        'taxonomies' => array('place-category')
       );  
  
    register_post_type( 'places' , $args );  
}  


add_filter("manage_edit-places_columns", "places_edit_columns");   
function places_edit_columns($columns){  
        $columns = array(  
            "cb" => "<input type=\"checkbox\" />", 
            //"thumbnail" => "",
            "title" => __("Sito", "mim-admin"),
            "author" => __("Autore", "mim-admin"),
            "place-category" => __("Categoria", "mim-admin") 
        );  
  
        return $columns;  
}

?>