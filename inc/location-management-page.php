<?php 

    echo get_the_content();

    $args = array( 
        'post_type' => 'places',
        //'place-category' => 'location-management',
        'tax_query' => array(array(
            'taxonomy'=>'place-category',
            'field'=>'slug',
            'terms'=>array('location-management')
            ))
        );
    
    echo "<div class='lm-list'>";
    
    $the_query = new WP_Query ($args);
    while ( $the_query->have_posts() ) :
        $the_query->the_post(); 

        //proprieta generali
        $place_id = get_the_ID();
        $place_title = get_the_title();
        $place_link = get_the_permalink();
        $place_desc = rwmb_meta('descrizione');
        $place_addr = rwmb_meta('address');
        
        $place_gallery = array_shift (rwmb_meta('galleria'));
        if (count($place_gallery)>0){
            foreach ($place_gallery as $image){
                $img_url = $image['url'];
                $img_fullurl = $image['full_url'];
            }
        }else{
            $img_url = "";
            $img_fullurl = "";
        }
        
        
        ?>

        <article class="lm-item-list">

                <!-- begin .post-thumb -->
                <?php if($img_url!=""){ ?>
                <div class="place-thumb">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo $img_url?>" class="place-thumb-img" />
                    </a>
                </div>
                <?php } ?>
                <!-- end .post-thumb -->

                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                <?php echo $place_desc; ?>

        </article>
        <div style="clear:both"></div>
        
<?php
        wp_reset_query();
    endwhile;
    
    echo "</div>";
