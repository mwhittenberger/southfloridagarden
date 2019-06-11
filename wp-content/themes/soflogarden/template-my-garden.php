<?php /* Template Name: My Garden */

require_once('login-redirect.php');
get_header(); ?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <main role="main">
                <!-- section -->
                <section>

                    <h1><?php the_title(); ?></h1>

                    <?php if (have_posts()): while (have_posts()) : the_post(); ?>

                        <!-- article -->
                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                            <?php the_content(); ?>

                        </article>
                        <!-- /article -->

                    <?php endwhile; ?>

                    <?php endif; ?>

                </section>
                <!-- /section -->

                <div class="addone">
                    Add a new gardening location
                </div>
                <div class="hidden-add">
                    <div class="hidden-inner">
                    <?php echo do_shortcode('[gravityform id="1" title="false" description="false"]'); ?>
                    </div>
                </div>

                <div class="locations">

                    <?php
                    $loop = new WP_Query( array( 'post_type' => 'location', 'posts_per_page' => -1, 'orderby' => 'title',
                        'order'   => 'ASC', 'author' => get_current_user_id() ) );


                    if ( $loop->have_posts() ) :
                        while ( $loop->have_posts() ) : $loop->the_post();
                        $location = NEW GardenLocation();
                        $location->set_id(get_the_ID());
                        $planting_location = get_the_ID();

                        ?>

                        <div class="location">
                            <span class="location-title"><?php echo get_the_title(); ?></span> - <span class="location-type"><?php echo $location->get_type(); ?></span>
                            <div class="location-notes"><?php the_content(); ?></div>
                            <div class="plants">
                                <?php
                                $plant_loop = new WP_Query( array( 'post_type' => 'planted', 'posts_per_page' => -1, 'orderby' => 'title',
                                    'order'   => 'ASC', 'author' => get_current_user_id(), 'meta_query' => array(
                                        array(
                                            'key'     => 'location',
                                            'value'   => $planting_location
                                        )
                                    ) ) );
                                if ( $plant_loop->have_posts() ) :
                                while ( $plant_loop->have_posts() ) : $plant_loop->the_post();

                                    $this_plant = new PlantedPlant();
                                    $this_plant->set_id(get_the_ID());
                                    $plant_id = get_the_ID();
                                    $plant_slug = $this_plant->get_name();

                                    $args = array(
                                        'name'        => $plant_slug,
                                        'post_type'   => 'plant',
                                        'post_status' => 'publish',
                                        'numberposts' => 1
                                    );
                                    $my_posts = get_posts($args);
                                    if( $my_posts ) :
                                        $plant_type_id = $my_posts[0]->ID;
                                        $title = get_the_title($my_posts[0]->ID);
                                        $my_img_url = get_the_post_thumbnail_url($my_posts[0]->ID, 'thumbnail');
                                    endif;

                                ?>
                                    <div class="plantedplant">
                                        <div><img src="<?php echo $my_img_url; ?>"></div>
                                        <div>
                                            <span class="planted-type"><?php echo $title; ?></span>
                                            <div class="planted-details">
                                                Marker: <span style="font-weight: bold"><?php echo $this_plant->get_plant_marker(); ?></span><br>
                                                Variety: <span style="font-weight: bold"><?php echo $this_plant->get_plant_variety(); ?></span><br>
                                                Quantity: <span style="font-weight: bold"><?php echo $this_plant->get_plant_quantity(); ?></span>
                                            </div>
                                        </div>
                                        <div class="plant-actions">
                                            <div class="plant-info" data-post_id="<?php echo $plant_id; ?>"><i class="fa fa-info-circle" aria-hidden="true"></i> Details</div>
                                            <div class="update-plant" plant_type_id="<?php echo $plant_type_id; ?>" data-post_id="<?php echo $plant_id; ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Update</div>
                                            <div class="move-plant" garden_location="<?php echo $planting_location; ?>"><i class="fa fa-arrows" aria-hidden="true"></i> Change Location</div>
                                            <div class="remove-plant action-button" data-post_id="<?php echo $plant_id; ?>" data-action="delete"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove</div>
                                        </div>
                                        <div class="new-location">
                                            <select class="new_plant_location">
                                                <option>Specify the new location</option>
                                            </select>
                                            <input type="hidden" name="plant_id" value="<?php echo $plant_id; ?>">
                                        </div>
                                    </div>
                                <?php
                                endwhile;
                                endif;
                                wp_reset_postdata();
                                ?>
                            </div>
                            <div class="actions">
                                <div data-toggle="modal" data-target="#addPlantModal" data-locationID="<?php echo $planting_location; ?>"><?php gimmeSVG('newplant'); ?><div>Add a Plant</div></div>
                                <div><?php gimmeSVG('journal'); ?><div>Journal</div></div>
                                <div class=""><?php gimmeSVG('edit'); ?><div>Edit</div></div>
                                <div class="action-button delete" data-post_id="<?php echo $planting_location; ?>" data-action="delete"><?php gimmeSVG('delete'); ?><div>Remove</div></div>

                            </div>

                        </div>

                        <?php
                        endwhile;

                    else : ?>
                        <p style="text-align: center">You currently have no gardening locations in our system. Please <span class="addone">add one</span> to get started.</p>
                    <?php
                    endif;
                    wp_reset_postdata();
                    ?>


                </div>

            </main>
        </div>
    </div>
</div>
<div class="modal fade" id="addPlantModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add a Plant</h4>
            </div>
            <div class="modal-body">
                <div class="gform_wrapper">
                    <form id="add_a_plant_to_location" method="post">
                <select class="choose-plant fancy-select" required name="choose-plant" id="choose-plant" style="width: 100%" tabindex="1">
                    <option value="">Choose a Plant</option>
                    <?php
                    $loop = new WP_Query( array( 'post_type' => 'plant', 'posts_per_page' => -1, 'orderby' => 'title',
                        'order'   => 'ASC' ) );
                    if ( $loop->have_posts() ) :
                    while ( $loop->have_posts() ) : $loop->the_post(); ?>
                    <option plantImg="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>" value="<?php global $post; echo $post->post_name; ?>"><?php echo get_the_title(); ?></option>
                    <?php endwhile; endif; wp_reset_postdata(); ?>
                </select>
                        <ul id="add_plant_fields" class="gform_fields top_label form_sublabel_below description_below">
                            <li id="variety-wrap" class="autocomplete gfield gfield_contains_required field_sublabel_below field_description_below gfield_visibility_visible"><label class="gfield_label" for="input_1_1">Variety<span class="gfield_required">*</span></label><div class="ginput_container ginput_container_text"><input name="plant_variety" id="plant_variety" required type="text" value="" class="large" tabindex="2" aria-required="true" aria-invalid="false" /></div></li>
                            <li id="num-of-plants-wrap" class="gfield gfield_contains_required field_sublabel_below field_description_below gfield_visibility_visible"><label class="gfield_label" for="input_1_1">Number of Plants<span class="gfield_required">*</span></label><div class="ginput_container ginput_container_text"><input name="num_of_plants" required id="num_of_plants" type="number" value="" class="large" tabindex="3" aria-required="true" aria-invalid="false" /></div></li>
                            <li id="plant-status-wrap" class="gfield gfield_contains_required field_sublabel_below field_description_below gfield_visibility_visible"><label class="gfield_label" for="input_1_2">Seed or Seedling<span class="gfield_required">*</span></label><div class="ginput_container ginput_container_select"><select name="plant_status" required id="plant_status" class="large gfield_select" tabindex="4" aria-required="true" aria-invalid="false"><option value="">Choose a Status</option><option value="Seed">Seed</option><option value="Seedling">Seedling</option><option value="Plant">Fully Grown Plant</option></select></div><div class="gfield_description">Specify the stage of growth for this plant</div></li>
                            <li id="notes-wrap" class="gfield field_sublabel_below field_description_below gfield_visibility_visible"><label class="gfield_label" for="input_1_1">Notes</label><div class="ginput_container ginput_container_text"><input name="notes" id="notes" type="text" value="" class="large" tabindex="5" aria-required="true" aria-invalid="false"></div></li>
                            <li id="source-wrap" class="autocomplete gfield field_sublabel_below field_description_below gfield_visibility_visible"><label class="gfield_label" for="input_1_1">Source Acquired From</label><div class="ginput_container ginput_container_text"><input name="source" id="source" type="text" value="" class="large" tabindex="5" aria-required="true" aria-invalid="false"></div></li>
                            <input type="submit" class="btn-primary" tabindex="6" style="margin-top: 15px;">
                            <input type="hidden" name="location" id="location">
                        </ul>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="updatePlantModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Update</h4>
            </div>
            <div class="modal-body">
                Enter updates for <span id="name_plant_type"></span> - <span id="name_plant_marker"></span>
                <?php echo do_shortcode('[gravityform id="3" title="false" description="false"]'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>

