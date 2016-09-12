<?php 
// check the presence of the GET place category
$place_category = false;
if (isset($_GET['cat'])){
    $place_category = $_GET['cat'];
}
?>

<!-- div mappa -->
<div id="map-canvas" style="height:500px"></div>
<div id="map-legend"><h3 class='title-legenda-map'>Legenda</h3></div>

<!-- script di inizializzazione mappa  -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsPcBSA2VbFLWdr4-VpPZuMfcc0VM-_UQ"></script>
<script>
var map;
function initMap() {
    
    //dichiaro stile personalizzato
    var customMapType4 = new google.maps.StyledMapType(style_bus_k_leye,  { name:"Stile B/W" });
    var customMapTypeId4 = 'custom_style4';
    
    //inizializzo la mappa
    map = new google.maps.Map(document.getElementById('map-canvas'), {
        center: {lat: 43.8779697, lng: 11.0923458},
        /*mapTypeControlOptions: {
          mapTypeIds: [customMapTypeId4]
        },*/
        mapTypeControl: false,
        streetViewControl: false,
        zoom: 8
    });
    
    //inserisco i markers dei siti
    setMarkers(map);
    
    //imposto stile personalizzato
    map.mapTypes.set(customMapTypeId4, customMapType4);
    map.setMapTypeId(customMapTypeId4);
    
}

//variabili places
var mappagelink = "<?php echo get_map_page_permalink() ;?>";
var cat = "<?php echo $place_category ;?>";
var locations = <?php echo json_encode( list_siti_map( false, $place_category ) );?>;
var info_categories = <?php echo json_encode(place_category_info() );?>;
var infoWindowList = new Array();

function setMarkers(map) {

    var shape = {
      coords: [1, 1, 1, 20, 18, 20, 18, 1],
      type: 'poly'
    };
    
    //variabile per centrare la mappa sul perimetro dei marker
    var markerBounds = new google.maps.LatLngBounds();

    for (var i = 0; i < locations.length; i++) {
        
        //VARIABILI PLACE
        var sito = locations[i];
        var title = sito['title'];
        var link = sito['link'];
        var content = sito['content'];
        var address = sito['address'];
        var firstimg = sito['firstimg'];
        var gallery = sito['gallery'];
        var category = sito['cat'];
        var icon = sito['icon'];
        var markercolor = sito['color'];
        var iconlabel = sito['numerazione'];
        
        // ISTAZIAMENTO INFOWINDOW
        var infowindow = new google.maps.InfoWindow({
            content: "",
            width: 280
        });
        infoWindowList[i] = infowindow;
        
        // --MARKERS-- 
        
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
        
        // Extend markerBounds with each random point.
        markerPoint = new google.maps.LatLng(sito['lat'], sito['lng']);
        markerBounds.extend(markerPoint);
            
        // BIND INFOWINDOW
        if( title.length > 0 ) {
                bindInfoWindow(marker, map, infowindow, title, link, content, address, firstimg, gallery, category );
        }
        
        map.fitBounds(markerBounds);

        }
        
        // LEGEND
        var legend = document.getElementById('map-legend');
        for (var key in info_categories) {
            var title = info_categories[key][0];
            var color = info_categories[key][1];
            var style_active = "";
            if (key == cat){
                style_active = "style='font-weight:bold'";
            }
            //var icon = type.icon;
            var div = document.createElement('div');
            //div.innerHTML = '<svg height="20" width="20"><path stroke="#000" stroke-width="1" fill="'+color+'" d="M 10 3 a 8 8 0 1 0 0.0001 0" /></svg> <div class="title-cat-legenda"><a href="?cat='+key+'">'+title+'</a></div>';
            div.innerHTML = '<svg height="20" width="20"><path stroke="#000" stroke-width="1" fill="'+color+'" d="M 10 3 a 8 8 0 1 0 0.0001 0" /></svg> <div class="title-cat-legenda" '+style_active+' >'+title+'</div>';
          
            legend.appendChild(div);
        }
        
        //intructions to append to the bottom of the legend
        var divbottom = document.createElement('div');
        divbottom.innerHTML = "Clicca per filtrare per categoria ";
        if (cat !== ""){
            divbottom.innerHTML += "<br/>torna alla <a href='"+mappagelink+"'>mappa generale</a>";
        }
        //legend.appendChild(divbottom);
        
        map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(legend);
  
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

// associazione informazioni del place all'infowindow
function bindInfoWindow( marker, map, infowindow, title, link, content, address, firstimg, gallery, category ) {

    //console.log(gallery);

    //stringa html del titolo
    var strTitle = "<strong class='title-iw'><a href='"+link+"'>"+title+"</a></strong><br/>";
    //var strTitle = "<strong class='title-iw'>"+title+"</strong><br/>";

    //stringa html della descrizione
    var strIndirizzo = "";
    strIndirizzo += "<div class='tab-row'>";
    strIndirizzo += "<div class='tab-cell cell-label'>Indirizzo: </div>";
    strIndirizzo += "<div class='tab-cell'><p class='adr-iw'>"+address+"</p></div>";
    strIndirizzo += "</div>";

    //stringa html della descrizione
    var strContent = "";
    if (content !== ""){
        strContent += "<div class='tab-row'>";
        strContent += "<div class='tab-cell cell-label'>Descrizione: </div>";
        strContent += "<div class='tab-cell'><p>"+content+"</p></div>";
        strContent += "</div>";
    }

    //stringa html della galleria immagini
    var i = 0;
    var classImg = "";
    var strGallery = "<div class='gallery-iw'>";
    for (var idimg in gallery) {
        var image = gallery[idimg];
        if (i > 0){
            classImg = "img-gallery-iw";
            strGallery += "<a href='"+image['full_url']+"' data-lightbox='gallery-set'>";
            strGallery += "<img src='"+image['url']+"' class='"+classImg+"' />";
            strGallery += "</a>";
        }else{
            classImg = "img-gallery-iw-first";
            strGallery += "<a href='"+image['full_url']+"' data-lightbox='gallery-set'>";
            strGallery += "<img src='"+firstimg[0]+"' class='"+classImg+"' />";
            strGallery += "</a>";
        }
        i++;
    }
    strGallery += "<div class='clear'></div>";
    strGallery += "</div>";

    //stringa html dei valori place categories
    var strCategories = "";
    strCategories += "<div class='tab-row'>";
    strCategories += "<div class='tab-cell cell-label'>Categoria: </div>";
    strCategories += "<div class='tab-cell'><span>"+category['name']+"</span></div>";
    strCategories += "</div>";
    
    var htmlInfowindow = "<div class='container-iw'>"+strTitle+strGallery+strIndirizzo+strContent+strCategories+"</div>";
    
    marker.addListener('click', function() {
        closeAllInfoWindow();
        infowindow.setContent(htmlInfowindow);
        infowindow.open(map, marker);
    });
    
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

