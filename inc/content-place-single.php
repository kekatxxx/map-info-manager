<?php

/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Smoke
 * @since Smoke 1.0
 */

//script lightbox

wp_enqueue_script( 'lightbox', get_template_directory_uri().'/js/lightbox.min.js', array(), '', true );
wp_enqueue_style( 'lightbox', get_template_directory_uri() . '/css/lightbox.css');
wp_enqueue_style( 'lightbox', get_template_directory_uri() . '/css/screen.css');

//categorie places
$id_place = get_the_ID();
$cat = wp_get_post_terms( $id_place , 'place-category' );
$place_cat = $cat[0];


?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-place'); ?>>
       
    
	<div class="entry-content">
            
            <div class="content-single-place work_item <?php echo $class_align?>">
                
                <!-- CATEGORIA E INDIRIZZO -->
                <div class="tab-row">
                    <div class="tab-cell cell-label">
                        Categoria:
                    </div>
                    <div class="tab-cell">
                        <?php echo $place_cat->name;?>
                    </div>
                </div>
                <div class="tab-row">
                    <div class="tab-cell cell-label">
                        Indirizzo: 
                    </div>
                    <div class="tab-cell">
                        <?php echo rwmb_meta('address');?>
                    </div>
                </div>
                <div class="tab-row">
                    <div class="tab-cell cell-label">
                        Descrizione:
                    </div>
                    <div class="tab-cell">
                        <?php echo rwmb_meta('descrizione');?>
                    </div>
                </div>
                
                <!--div class="cat-single-place">
                    <p>Categoria: <?php echo $place_cat->name;?><br/>
                    Indirizzo: <?php echo rwmb_meta('address');?>
                    </p>
                </div-->
                
                <!-- TEXTAREA -->
                <!--div class="textarea-single-place">
                    <p><?php echo rwmb_meta('descrizione');?></p>
                </div-->
                
                <!-- GALLERIA IMMAGINI -->
                <?php if (rwmb_meta('galleria') != ""){?>
                <div class='gallery-single-place <?php echo $class_align?>'>

                    <?php
                    $i = 1;
                    foreach (rwmb_meta('galleria',array('type'=>'image')) as $image){

                        if ($i == 1 && $class_align != ""){
                            $class = "first";
                        }else{
                            $class = "other";
                        }
                        echo "<a href='".$image['full_url']."' data-lightbox='gallery-set'>";
                        echo "<img src='".$image['full_url']."' class='img-gallery-single-place $class'  />";
                        echo "</a>";
                        $i++;
                    }
                    ?>
                    <div class='clear' style='margin-bottom:20px'></div>

                </div>
                <?php }?>
                    
            </div>
            
            <div class="work_item">
                
                <!-- MAPPA -->
                <div id="map-canvas" style="height:400px;"></div>

            </div>
            
            
            
            
        </div>
        
        <?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
        

</article><!-- #post-## -->

<!-- script di inizializzazione mappa  -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsPcBSA2VbFLWdr4-VpPZuMfcc0VM-_UQ"></script>
<script>
var map;
//variabili places
var locations = <?php echo json_encode( list_siti_map( $id_place )); ?>;
var color_categories = <?php echo json_encode( place_category_info() );?>;
var infoWindowList = new Array();

//ciclo per avere le coordinate place
for (var i = 0; i < locations.length; i++) {
var sito = locations[i];
var lat = sito['lat'];
var lng = sito['lng'];
}

function initMap() {
    
    //dichiaro stile personalizzato
    var customMapType4 = new google.maps.StyledMapType(style_bus_k_leye,  { name:"Stile B/W" });
    var customMapTypeId4 = 'custom_style4';
    
    //inizializzo la mappa
    map = new google.maps.Map(document.getElementById('map-canvas'), {
        center: {lat: lat, lng: lng},
        /*mapTypeControlOptions: {
          mapTypeIds: [google.maps.MapTypeId.ROADMAP, customMapTypeId4]
        },*/
        mapTypeControl: false,
        zoom: 13
    });
    
    //inserisco i markers dei siti
    setMarkers(map);
    
    //imposto stile personalizzato
    map.mapTypes.set(customMapTypeId4, customMapType4);
    map.setMapTypeId(customMapTypeId4);
    
}

function setMarkers(map) {

    for (var i = 0; i < locations.length; i++) {
        
        //VARIABILI PLACE
        var sito = locations[i];
        var title = sito['title'];
        var markercolor = sito['color'];
        var iconlabel = sito['numerazione'];
        
        //ISTANZIAMENTO MARKER
        /*
        var marker = new Marker({
            position: {lat: sito['lat'], lng: sito['lng']},
            map: map,
            //shape: shape,
            title: title,
            icon: {
                    path: SQUARE_PIN,
                    fillColor: iconcolor,
                    fillOpacity: 1,
                    strokeColor: '#484848',
                    strokeWeight: 1
            }
        });*/
        
        var marker = new MarkerWithLabel({
            position: {lat: sito['lat'], lng: sito['lng']},
            map: map,
            title: title,
            labelContent: iconlabel,
            labelAnchor: new google.maps.Point(6, 4),
            labelClass: "labels", // the CSS class for the label
            labelInBackground: false,
            icon: pinSymbol(markercolor)
        });
        
        }
  
}

// crea la forma del marker
function pinSymbol(color) {
    return {
        path: 'M 0 0 a 8 8 0 1 0 0.0001 0',
        fillColor: color,
        fillOpacity: 1,
        strokeColor: '#000',
        strokeWeight: 1,
        scale: 1
    };
}

//chiude tutte le infowindow
function closeAllInfoWindow(){

    for(var i=0; i<infoWindowList.length; i++){

        infowindow = infoWindowList[i];
        infowindow.close();
    }
}

google.maps.event.addDomListener(window, 'load', initMap);

</script>
