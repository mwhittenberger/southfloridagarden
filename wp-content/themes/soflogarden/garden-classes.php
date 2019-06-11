<?php
require_once('garden-functions.php');

class PlantMarker {
    var $user;
    var $last_letter;
    var $last_number;

    function set_user($new_user) {
        $this->user = $new_user;
    }
    function get_user() {
        return $this->user;
    }
    function get_new_plant_marker($user) {
        $this->set_user($user);


        $last_plant_marker = get_user_meta( $this->user, 'last_plant_marker', true );

        if($last_plant_marker == '') {
            $last_plant_marker = 'A1';
            update_user_meta( $this->user, 'last_plant_marker', $last_plant_marker );
            return $last_plant_marker;
        }

        $letter = preg_replace('/[^a-zA-Z]/', '', $last_plant_marker);
        $number = preg_replace('/[^0-9]/', '', $last_plant_marker);

        if($number == 99) {
            $number = 1;
            $letter = ++$letter;
        }
        else {
            $number++;
        }
        $last_plant_marker = $letter.$number;

        update_user_meta( $this->get_user(), 'last_plant_marker', $last_plant_marker );

        return $last_plant_marker;
    }
}

class UserSources {
    var $user;
    var $user_sources;

    function __construct($user) {
        $this->set_user($user);
        $this->user_sources = get_user_meta( $user, 'entered_sources', true );
    }

    function set_user($new_user) {
        $this->user = $new_user;
    }

    function get_user() {
        return $this->user;
    }

    function get_user_sources() {
        return $this->user_sources;
    }
    function add_entered_source($new_source) {
        $user_array = $this->get_user_sources();
        $user_array2 = array_map('strtolower', $user_array);

        if(!in_array(strtolower($new_source), $user_array2)) {
            array_push($user_array, $new_source);
            $user_array = implode(",",$user_array);
            update_user_meta( $this->user, 'entered_sources', $user_array );
        }
    }
}

class UserVarieties {
    var $user;
    var $user_varieties = array();
    var $system_varieties = array();

    function __construct() {
        $args = array(
            'post_type'   => 'plant',
            'post_status' => 'publish',
            'numberposts' => -1
        );
        $plants = get_posts($args);
        foreach($plants as $plant) {
            $my_varieties = get_post_meta( $plant->ID, 'recommended_varieties', true );
            $my_varieties = delete_all_between('(', ': ', $my_varieties);
            $my_varieties = explode( ',', $my_varieties);
            foreach($my_varieties as $my_variety) {
                array_push($this->system_varieties, trim($my_variety));
            }
        }
        //print_r($this->system_varieties);
    }

    function set_user($new_user) {
        $this->user = $new_user;
    }

    function get_user() {
        return $this->user;
    }

    function get_user_varieties() {
        $entered_varieties = get_user_meta( $this->user, 'entered_varieties', true );
        $this->user_varieties = explode(',', $entered_varieties);
        return $this->user_varieties;
    }

    function add_entered_variety($new_variety) {
        $user_array = $this->get_user_varieties();
        $user_array2 = array_map('strtolower', $user_array);
        $system_array = array_map('strtolower', $this->system_varieties);

        if(!in_array(strtolower($new_variety), $user_array2) && !in_array(strtolower($new_variety), $system_array)) {
            array_push($user_array, $new_variety);
            $user_array = implode(",",$user_array);
            update_user_meta( $this->user, 'entered_varieties', $user_array );
        }
    }


}

class Post {

    var $name;
    var $desc;
    var $id;
    var $image;
    var $author;
    var $post_type;

    function set_name($new_name) {
        $this->name = $new_name;
    }
    function get_name() {
        return $this->name;
    }
    function set_author($new_author) {
        $this->author = $new_author;
    }
    function get_author() {
        return $this->author;
    }
    function set_desc($new_desc) {
        $this->desc = $new_desc;
    }
    function get_desc() {
        return $this->desc;
    }
    function set_id($new_id) {
        $this->id = $new_id;
        $temp_post = $this->get_post($this->id);
        $this->set_name($temp_post->post_title);
        $this->set_desc($temp_post->post_content);
        $this->set_author($temp_post->post_author);
    }
    function get_id() {
        return $this->id;
    }
    function set_image($new_image) {
        $this->image = $new_image;
    }
    function get_image() {
        return $this->image;
    }
    function insert_post() {
        // Create post object
        $my_post = array(
            'post_title'    => $this->name,
            'post_content'  => $this->desc,
            'post_status'   => 'publish',
            'post_author'   => $this->author
        );

        // Insert the post into the database
        $new_id = wp_insert_post( $my_post );
        return $new_id;
    }
    function update_post() {
        $my_post = array(
            'ID'           => $this->id,
            'post_title'   => $this->name,
            'post_content' => $this->desc,
        );

        wp_update_post( $my_post );
    }
    function get_post() {
        $post = get_post( $this->id );
        return $post;
    }
    function delete_post() {
        wp_delete_post( $this->id );
    }
    function set_post_meta($key, $value) {
        update_post_meta( $this->id, $key, $value );
    }
    function get_post_meta($key) {
        return get_post_meta( $this->id, $key, true );
    }
}

class GardenLocation extends Post
{

    function set_type($new_type) {
        update_post_meta( $this->id, 'type', $new_type );
    }
    function get_type() {
        return get_post_meta( $this->id, 'type', true );
    }
    function insert_post() {
        // Create post object
        $my_post = array(
            'post_title'    => $this->name,
            'post_content'  => $this->desc,
            'post_status'   => 'publish',
            'post_author'   => $this->author,
            'post_type'     => 'location'
        );

        // Insert the post into the database
        $new_id = wp_insert_post( $my_post );
        return $new_id;
    }
}

class PlantedPlant extends Post
{
    function insert_post() {
        // Create post object
        $my_post = array(
            'post_title'    => $this->name,
            'post_content'  => $this->desc,
            'post_status'   => 'publish',
            'post_author'   => $this->author,
            'post_type'     => 'planted'
        );

        // Insert the post into the database
        $new_id = wp_insert_post( $my_post );
        return $new_id;
    }
    function set_location($new_location) {
        update_post_meta( $this->id, 'location', $new_location );
    }
    function get_location() {
        return get_post_meta( $this->id, 'location', true );
    }

    function set_plant_variety($new_plant_variety) {
        update_post_meta( $this->id, 'plant_variety', $new_plant_variety );
    }
    function get_plant_variety() {
        return get_post_meta( $this->id, 'plant_variety', true );
    }

    function set_plant_quantity($new_plant_quantity) {
        update_post_meta( $this->id, 'plant_quantity', $new_plant_quantity );
    }
    function get_plant_quantity() {
        return get_post_meta( $this->id, 'plant_quantity', true );
    }

    function set_plant_starting_status($new_plant_starting_status) {
        update_post_meta( $this->id, 'plant_starting_status', $new_plant_starting_status );
    }
    function get_plant_starting_status() {
        return get_post_meta( $this->id, 'plant_starting_status', true );
    }

    function set_plant_source($new_plant_source) {
        update_post_meta( $this->id, 'plant_source', $new_plant_source );
    }
    function get_plant_source() {
        return get_post_meta( $this->id, 'plant_source', true );
    }

    function set_plant_marker($new_plant_marker) {
        update_post_meta( $this->id, 'marker', $new_plant_marker );
    }
    function get_plant_marker() {
        return get_post_meta( $this->id, 'marker', true );
    }

    function set_germination_rate($new_germination_rate) {
        update_post_meta( $this->id, 'germination_rate', $new_germination_rate );
    }
    function get_germination_rate() {
        return get_post_meta( $this->id, 'germination_rate', true );
    }

    function set_germinated($new_germinated) {
    update_post_meta( $this->id, 'germinated', $new_germinated );
}
    function get_germinated() {
        return get_post_meta( $this->id, 'germinated', true );
    }

    function set_germination_date_1($new_germination_date_1) {
        update_post_meta( $this->id, 'germination_date_1', $new_germination_date_1 );
    }
    function get_germination_date_1() {
        return get_post_meta( $this->id, 'germination_date_1', true );
    }

    function set_germination_date_2($new_germination_date_2) {
        update_post_meta( $this->id, 'germination_date_2', $new_germination_date_2 );
    }
    function get_germination_date_2() {
        return get_post_meta( $this->id, 'germination_date_2', true );
    }
}


?>