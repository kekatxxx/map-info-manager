<?php

add_filter( 'rwmb_meta_boxes', 'mim_meta_boxes' );
function mim_meta_boxes( $meta_boxes ) {
    $meta_boxes[] = array(
        'title'      => __( 'Informazioni sito', 'mim-admin' ),
        'post_types' => 'places',
        'fields'     => array(
            array(
                    'id'   => 'descrizione',
                    'name' => __( 'Descrizione', 'mim-admin' ),
                    'type' => 'textarea',
            ),
            array(
                    'id'   => 'address',
                    'name' => __( 'Indirizzo', 'mim-admin' ),
                    'type' => 'text',
                    'std'  => __( 'Prato', 'mim-admin' ),
            ),
            array(
                    'id'            => 'map',
                    'name'          => __( 'Posizione', 'mim-admin' ),
                    'type'          => 'map',
                    // Default location: 'latitude,longitude[,zoom]' (zoom is optional)
                    'std'           => '43.8779697,11.0923458,12',
                    // Name of text field where address is entered. Can be list of text fields, separated by commas (for ex. city, state)
                    'address_field' => 'address',
                    'api_key'       => 'AIzaSyCsPcBSA2VbFLWdr4-VpPZuMfcc0VM-_UQ'
            ),
            array(
                    'name'             => __( 'Galleria immagini', 'rwmb' ),
                    'desc'             => 'Galleria immagini',
                    'id'               => 'galleria',
                    'type'             => 'image_advanced',
                    'max_file_uploads' => 5,
            ),
            array(
                    'id'   => 'numerazione',
                    'name' => __( 'Numerazione', 'mim-admin' ),
                    'type' => 'number'
            ),
        ),
    );
    return $meta_boxes;
}
