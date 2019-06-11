<?php
require_once('garden-classes.php');

//add new garden location on form submit
add_action( 'gform_pre_submission_1', 'add_location', 10, 2 );
function add_location( $form ) {

    $name = rgpost( 'input_1' );
    $type = rgpost( 'input_2' );
    $notes = rgpost( 'input_4' );

    $nl = new GardenLocation();
    $nl->set_name($name);
    $nl->set_desc($notes);
    $nl->set_author(get_current_user_id());
    $myid = $nl->insert_post();
    $nl->set_id($myid);
    $nl->set_type($type);

}


//general shared post manipulation function
add_action("wp_ajax_tweak_post", "tweak_post");
add_action("wp_ajax_nopriv_tweak_post", "tweak_post");

function tweak_post()
{

   // if (!wp_verify_nonce($_REQUEST['nonce'], "my_user_vote_nonce")) {
   //     exit("No naughty business please");
   // }

    $post_action = $_REQUEST["post_action"];
    $post_id = $_REQUEST["post_id"];
    $post_meta = $_REQUEST["post_meta"];
    $post_meta_value = $_REQUEST["post_meta_value"];
    $new_garden_location = $_REQUEST["new_garden_location"];

    if($post_action == 'moveplant') {
        $pp = new PlantedPlant();
        $pp->set_id($post_id);
        $pp->set_location($new_garden_location);
        $result['dothis'] = "refresh";
    }

    if($post_action == 'delete') {

        $plant_loop = new WP_Query( array( 'post_type' => 'planted', 'posts_per_page' => -1, 'orderby' => 'title',
            'order'   => 'ASC', 'author' => get_current_user_id(), 'meta_query' => array(
                array(
                    'key'     => 'location',
                    'value'   => $post_id
                )
            ) ) );
        if ( $plant_loop->have_posts() ) :
            while ( $plant_loop->have_posts() ) : $plant_loop->the_post();
                $me = new Post();
                $me->set_id(get_the_ID());
                $me->delete_post();
            endwhile;
        endif;
        wp_reset_postdata();

        $me = new Post();
        $me->set_id($post_id);
        $me->delete_post();
        $result['dothis'] = "refresh";
    }

    $result['type'] = "success";
    $result = json_encode($result);
    echo $result;

    die();

}

function delete_all_between($beginning, $end, $string) {
    $beginningPos = strpos($string, $beginning);
    $endPos = strpos($string, $end);
    if ($beginningPos === false || $endPos === false) {
        return $string;
    }

    $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

    return delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
}

//get options for plant variety autofill
add_action("wp_ajax_auto_complete_plant_variety", "auto_complete_plant_variety");
add_action("wp_ajax_nopriv_auto_complete_plant_variety", "auto_complete_plant_variety");

function auto_complete_plant_variety()
{

    $plant = $_REQUEST["plant"];

    $args = array(
        'name'        => $plant,
        'post_type'   => 'plant',
        'post_status' => 'publish',
        'numberposts' => 1
    );
    $my_posts = get_posts($args);
    if( $my_posts ) :
        $varieties = get_post_meta( $my_posts[0]->ID, 'recommended_varieties', true );
    endif;

    $varieties = delete_all_between('(', ': ', $varieties);

    $uv = new UserVarieties();
    $uv->set_user(get_current_user_id());
    $user_array = $uv->get_user_varieties();
    $user_array = implode(", ",$user_array);
    $varieties .= $user_array;

    $result['varieties'] = $varieties;
    $result['type'] = "success";
    $result = json_encode($result);
    echo $result;

    die();

}


//get options for plant source autofill
add_action("wp_ajax_auto_complete_plant_source", "auto_complete_plant_source");
add_action("wp_ajax_nopriv_auto_complete_plant_source", "auto_complete_plant_source");

function auto_complete_plant_source()
{

    $user = $_REQUEST["user"];

    $us = new UserSources($user);
    $sources = $us->get_user_sources();

    //echo "sources is ".$sources;
    //$user_array = implode(", ",$sources);

    $result['sources'] = $sources;
    $result['type'] = "success";
    $result = json_encode($result);
    echo $result;

    die();

}

//get details for a planted plant for updating
add_action("wp_ajax_get_plant_details", "get_plant_details");
add_action("wp_ajax_nopriv_get_plant_details", "get_plant_details");

function get_plant_details()
{

    $plant_id = $_REQUEST["plant_id"];
    $plant_type_id = $_REQUEST["plant_id"];
    $user = $_REQUEST["user"];

    $us = new UserSources($user);
    $sources = $us->get_user_sources();

    $result['sources'] = $sources;

    $args = array(
        'name'        => $plant_id,
        'post_type'   => 'plant',
        'post_status' => 'publish',
        'numberposts' => 1
    );
    $my_posts = get_posts($args);
    if( $my_posts ) :
        $varieties = get_post_meta( $my_posts[0]->ID, 'recommended_varieties', true );
    endif;

    $varieties = delete_all_between('(', ': ', $varieties);

    $uv = new UserVarieties();
    $uv->set_user($user);
    $user_array = $uv->get_user_varieties();
    $user_array = implode(", ",$user_array);
    $varieties .= $user_array;

    $result['varieties'] = $varieties;

    $pp = new PlantedPlant();
    $pp->set_id($plant_id);

    $args = array(
        'name'        => $pp->get_name(),
        'post_type'   => 'plant',
        'post_status' => 'publish',
        'numberposts' => 1
    );
    $my_posts = get_posts($args);
    if( $my_posts ) :
        $title = get_the_title($my_posts[0]->ID);
    endif;

    $result["plant_type"] = $title;
    $result["plant_marker"] = $pp->get_plant_marker();
    $result["plant_variety"] = $pp->get_plant_variety();
    $result["plant_quantity"] = $pp->get_plant_quantity();
    $result["plant_source"] = $pp->get_plant_source();
    $result["plant_notes"] = $pp->get_desc();
    $result["plant_status"] = $pp->get_plant_starting_status();
    $result["plant_germination"] = $pp->get_germinated();
    $result["plant_germination_rate"] = $pp->get_germination_rate();
    $result["plant_germination_date1"] = $pp->get_germination_date_1();
    $result["plant_germination_date2"] = $pp->get_germination_date_2();

    $result['type'] = "success";
    $result = json_encode($result);
    echo $result;

    die();

}

//get garden locations for a specific user
add_action("wp_ajax_get_locations", "get_locations");
add_action("wp_ajax_nopriv_get_locations", "get_locations");

function get_locations()
{

    $user = $_REQUEST["user"];
    $exception = $_REQUEST["exception"];

    $garden_locations = array();

    $loop = new WP_Query( array( 'post_type' => 'location', 'posts_per_page' => -1, 'orderby' => 'title',
        'order'   => 'ASC', 'author' => get_current_user_id() ) );
    if ( $loop->have_posts() ) {
        while ( $loop->have_posts() ) {
            $loop->the_post();

            if($exception != get_the_ID())
                array_push($garden_locations, array('gl_id' => get_the_ID(), 'gl_name' => get_the_title()));
        }
    }
    wp_reset_postdata();


    $result['locations'] = $garden_locations;
    $result['type'] = "success";
    $result = json_encode($result);
    echo $result;

    die();

}


//after plant form submitted, add it to a garden location
add_action("wp_ajax_add_plant_to_location", "add_plant_to_location");
add_action("wp_ajax_nopriv_add_plant_to_location", "add_plant_to_location");

function add_plant_to_location()
{

    $plant = $_REQUEST["plant"];
    $user = $_REQUEST["user"];

    if($user == '') {
        $user = get_current_user_id();
    }

    $variety = $_REQUEST["variety"];
    $quantity = $_REQUEST["quantity"];
    $status = $_REQUEST["status"];
    $source = $_REQUEST["source"];
    $location = $_REQUEST["location"];

    $planted = new PlantedPlant();
    $planted->set_name($plant);
    $planted->set_author($user);
    $planted->set_desc('');
    $myid = $planted->insert_post();
    $planted->set_id($myid);
    $planted->set_location($location);
    $planted->set_plant_variety($variety);
    $planted->set_plant_quantity($quantity);
    $planted->set_plant_starting_status($status);
    $planted->set_plant_source($source);

    $pm = new PlantMarker();

    $planted->set_plant_marker($pm->get_new_plant_marker($user));

    $uv = new UserVarieties();
    $uv->set_user($user);
    $uv->add_entered_variety($variety);

    $us = new UserSources($user);
    $us->add_entered_source($source);


    $result['type'] = "success";
    $result = json_encode($result);
    echo $result;

    die();

}