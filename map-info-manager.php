<?php

/*
Plugin Name: Map Info Manager
Plugin URI:  http://www.floychecco.biz
Description: Crea e gestisci una mappa interattiva con gMaps
Version:     1.5
Author:      Floychecco
Author URI:  http://www.floychecco.biz
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/* INCORPORAMENTO STILI AGGIUNTIVI
================================================== */
wp_enqueue_style('mim-style',  plugins_url('mim-style.css', __FILE__));

/* POST TYPE 'SITO'
================================================== */
require_once('custom-post-types/place-type.php');

/* IMPORTAZIONE STILE PERSONALIZZATO MAPPA
================================================== */
wp_enqueue_script('map-custom-style', plugins_url('mapstyles/style-paledown.js', __FILE__), array(),false, true);
wp_enqueue_script('map-custom-style-bus', plugins_url('mapstyles/style-bus-k-leye.js', __FILE__), array(),false, true);

/* LIGHTBOX LIBRARY
================================================== */
wp_enqueue_script('lightbox-script', plugins_url('lightbox/lightbox.min.js', __FILE__), array(),false, true);
wp_enqueue_style('lightbox-style',  plugins_url('lightbox/lightbox.css', __FILE__));

/* MAP ICONS
================================================== */
if (!is_admin() || !is_single()){
    add_action( 'wp_enqueue_scripts', 'mapicon_enqueue_scripts' );
}
function mapicon_enqueue_scripts(){
    //wp_enqueue_script('map-icons-script', plugins_url('map-icons-master/dist/js/map-icons.js', __FILE__), array(), false, true);
    wp_enqueue_script('marker-with-label-script', plugins_url('markerwithlabel.js', __FILE__), array(), false, true);
    //wp_enqueue_style('map-icons-style',  plugins_url('map-icons-master/dist/css/map-icons.css', __FILE__));
    //wp_enqueue_style('fontawesome-style',  plugins_url('font-awesome/css/font-awesome.min.css', __FILE__));

    
    
}

/* META BOX FRAMEWORK
================================================== */
//define( 'RWMB_URL', '/meta-box/' );
include_once('meta-box/meta-box.php');
include_once('meta-box/mim-meta-box.php');

/* SELEZIONE PAGINA PER MAPPA NEL PANNELLO ADMIN
================================================== */

function mim_settings_api_init() {

    // Add the section to reading settings so we can add our fields to it
    add_settings_section(
        'map_setting_section',
        'Pagina mappa interattiva',
        'mim_setting_section_callback_function',
        'reading'
    );

    // Add the field with the names and function to use for our new settings, put it in our new section
    add_settings_field(
        'mim_map_page_id',
        'Pagina mappa interattiva',
        'mim_setting_callback_function',
        'reading',
        'map_setting_section'
    );

    // Register our setting in the "reading" settings section
    register_setting( 'reading', 'mim_map_page_id' );

}
add_action( 'admin_init', 'mim_settings_api_init');

/*
* Settings section callback function
*/
function mim_setting_section_callback_function() {
    
    echo '<p>Seleziona la pagina che mostrerà la mappa</p>';
}

/*
* Callback function for our example setting
*/
function mim_setting_callback_function() {
    $mim_private_page_id = esc_attr( get_option( 'mim_map_page_id' ) );
    
    $args = array(
	'sort_order' => 'asc',
	'sort_column' => 'post_title',
	'hierarchical' => 1,
	'exclude' => '',
	'include' => '',
	'meta_key' => '',
	'meta_value' => '',
	'authors' => '',
	'child_of' => 0,
	'parent' => -1,
	'exclude_tree' => '',
	'number' => '',
	'offset' => 0,
	'post_type' => 'page',
	'post_status' => 'publish'
    ); 
    $pages = get_pages($args); 
    echo "<select name='mim_map_page_id'>";
    foreach ($pages as $page){
        //print_r($page);
        $option_html = "<option value='$page->ID'";
        if ($page->ID === intval($mim_private_page_id)){
            $option_html .= " selected='selected'";
        }
        $option_html .= " >$page->post_title</option>";
        echo $option_html;
    }
    echo "</select>";
}


/* INSERIMENTO MAPPA NELLA PAGINA SELEZIONATA
================================================== */

function mim_page_load() {
    
    if(is_page(esc_attr(get_option('mim_map_page_id' )))){
        add_filter( 'the_content', 'filter_map_page' );
    }
    
}
add_action( 'wp', 'mim_page_load' );

function filter_map_page($content) {
  
    include "inc/map-page.php";
    return;
}

/* INSERIMENTO LOCATION MANAGMENT
================================================== */

function mim_page_lm_load() {
    
    if(is_page('location-management')){
        add_filter( 'the_content', 'filter_lm_page' );
    }
    
}
add_action( 'wp', 'mim_page_lm_load' );

function filter_lm_page($content) {
  
    include "inc/location-management-page.php";
    return;
}



/* SINGOLO PLACE
================================================== */
function mim_singleplace_load() {
    
    if(is_single && 'places' == get_post_type()){
        add_filter( 'the_content', 'filter_singleplace_page' );
        //add_filter( 'the_title', 'filter_singleplace_title' );
    }
    
}
add_action( 'wp', 'mim_singleplace_load' );

function filter_singleplace_page($content) {
  
    include "inc/content-place-single.php";
    return;
}

function filter_singleplace_title($content) {
  
    echo "<span class='num-single-place-title'>01</span>";
    echo $content;
    return;
}

/* GET THE MAP PAGE LINK
================================================== */
function get_map_page_permalink(){
    return get_permalink(get_option('mim_map_page_id' ));
}

/* GET THE TITLE AND THE COLOR OF THE CATEGORY
================================================== */
function place_category_info(){
    //$path = plugin_dir_url( __FILE__ )."icons/";
    $place_category_info = array(
        'abbandono'     => array('Abbandono','#000000'),
        'sottoutilizzo' => array('Sottoutilizzo','#666666') ,
        'rovina'        => array('Rovina','#844428') ,
        'riutilizzo'    => array('Riutilizzo','#c99702') ,
        'location-management'=> array('Location management','#0f8893')
    );
    return $place_category_info;
}

/* GET THE DATA OF PLACES
 * case 1: all the places
 * case 2: places by category
 * case 3: single place by id
================================================== */

function list_siti_map( $id_place = false, $cat = false ){
    
    $a_places = array();
    $icon_info = place_category_info();
    $args = array( 
        'post_type' => 'places',
        'nopaging' => true
        );
    
    if ( $cat &&  array_key_exists($cat, $icon_info)){
        $args = array( 
        'post_type' => 'places',
        'nopaging' => true,
            'tax_query' => array(
		array(
			'taxonomy' => 'place-category',
			'field' => 'slug',
			'terms' => $cat
		)
            )
        );
    }
    
    if ( $id_place ){
        $args = array( 
        'post__in'=> array($id_place),
        'post_type' => 'places',
        'nopaging' => true
        );
    }
    
    //controllo prima immagine principale sito
    $the_query = new WP_Query ($args);
    while ( $the_query->have_posts() ) :
        $the_query->the_post(); 

        //proprieta generali
        $single_place = array();
        $single_place['id'] = get_the_ID();
        $single_place['title'] = get_the_title();
        $single_place['link'] = get_the_permalink();
        $single_place['content'] = rwmb_meta('descrizione');
        $single_place['address'] = rwmb_meta('address');
        $single_place['gallery'] = rwmb_meta('galleria');
        
        $galleria = rwmb_meta('galleria');
        $first_img = array_shift($galleria);
        $first_img_res = wp_get_attachment_image_src($first_img['ID'], array('250','150'));
        $single_place['firstimg'] = $first_img_res;
        
        //$single_place['numerazione'] = rwmb_meta('numerazione'); da scommentare quando li avranno inseriti
        $num = strval(rand(1 , 99));
        if (strlen($num) == 1){
            $num = "0".$num;
        }
        $single_place['numerazione'] = $num;
        
        //proprieta geografiche
        $location = get_post_meta( get_the_ID(), 'map', true );
        $coords = explode(',', $location);
        $single_place['lat'] = floatval($coords[0]);
        $single_place['lng'] = floatval($coords[1]);
        
        //categorie places
        $cat = wp_get_post_terms( get_the_ID(), 'place-category' );
        $single_place['cat'] = $cat[0];
        
        //colore marker default
        $icon_color = '#666666';
        //colore marker della categoria
        if (array_key_exists($cat[0]->slug, $icon_info)){
            $icon_color = $icon_info[$cat[0]->slug][1];
        }
        $single_place['color']=  $icon_color;

        //aggiungo il valore del place nell'array totale
        array_push($a_places, $single_place);

    endwhile;

    wp_reset_query();
    wp_reset_postdata();

    return $a_places;
}