<?php
/*
 *  Author: Todd Motto | @toddmotto
 *  URL: html5blank.com | @html5blank
 *  Custom functions, support, custom post types and more.
 */

/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Register custom navigation walker
require_once('wp-bootstrap-navwalker.php');
require_once('garden-functions.php');
require_once('garden-classes.php');

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

if (!isset($content_width))
{
    $content_width = 900;
}

if (function_exists('add_theme_support'))
{
    // Add Menu Support
    add_theme_support('menus');

    // Add Thumbnail Theme Support
    add_theme_support('post-thumbnails');
    add_image_size('large', 700, '', true); // Large Thumbnail
    add_image_size('medium', 250, '', true); // Medium Thumbnail
    add_image_size('small', 120, '', true); // Small Thumbnail
    add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

    // Add Support for Custom Backgrounds - Uncomment below if you're going to use
    /*add_theme_support('custom-background', array(
	'default-color' => 'FFF',
	'default-image' => get_template_directory_uri() . '/img/bg.jpg'
    ));*/

    // Add Support for Custom Header - Uncomment below if you're going to use
    /*add_theme_support('custom-header', array(
	'default-image'			=> get_template_directory_uri() . '/img/headers/default.jpg',
	'header-text'			=> false,
	'default-text-color'		=> '000',
	'width'				=> 1000,
	'height'			=> 198,
	'random-default'		=> false,
	'wp-head-callback'		=> $wphead_cb,
	'admin-head-callback'		=> $adminhead_cb,
	'admin-preview-callback'	=> $adminpreview_cb
    ));*/

    // Enables post and comment RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Localisation Support
    load_theme_textdomain('html5blank', get_template_directory() . '/languages');
}

/*------------------------------------*\
	Functions
\*------------------------------------*/

// HTML5 Blank navigation

function html5blank_nav()
{
    wp_nav_menu( array(
            'menu'              => 'primary',
            'theme_location'    => 'primary',
            'depth'             => 2,
            'container'         => 'div',
            'container_class'   => 'collapse navbar-collapse',
            'container_id'      => 'bs-example-navbar-collapse-1',
            'menu_class'        => 'nav navbar-nav',
            'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
            'walker'            => new WP_Bootstrap_Navwalker())
    );
}

// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts()
{
    if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {

        //wp_register_script('conditionizr', get_template_directory_uri() . '/js/lib/conditionizr-4.3.0.min.js', array(), '4.3.0'); // Conditionizr
        //wp_enqueue_script('conditionizr'); // Enqueue it!

        //wp_register_script('modernizr', get_template_directory_uri() . '/js/lib/modernizr-2.7.1.min.js', array(), '2.7.1'); // Modernizr
        //wp_enqueue_script('modernizr'); // Enqueue it!

        wp_register_script('html5blankscripts', get_template_directory_uri() . '/js/scripts.js', array('jquery'), '1.0.0'); // Custom scripts
        wp_enqueue_script('html5blankscripts'); // Enqueue it!
        wp_localize_script( 'html5blankscripts', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
        wp_register_script('bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '1.0.0'); // Custom scripts
        wp_enqueue_script('bootstrap'); // Enqueue it!
        wp_register_script('select2', get_template_directory_uri() . '/js/select2.full.min.js', array('jquery'), '1.0.0'); // Custom scripts
        wp_enqueue_script('select2'); // Enqueue it!

    }
}

// Load HTML5 Blank conditional scripts
function html5blank_conditional_scripts()
{
    if (is_page('pagenamehere')) {
        wp_register_script('scriptname', get_template_directory_uri() . '/js/scriptname.js', array('jquery'), '1.0.0'); // Conditional script(s)
        wp_enqueue_script('scriptname'); // Enqueue it!
    }
}

// Load HTML5 Blank styles
function html5blank_styles()
{
    //wp_register_style('normalize', get_template_directory_uri() . '/normalize.css', array(), '1.0', 'all');
    //wp_enqueue_style('normalize'); // Enqueue it!

    wp_register_style('html5blank', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
    wp_enqueue_style('html5blank'); // Enqueue it!

    wp_register_style('select2', get_template_directory_uri() . '/css/select2.min.css', array(), '1.0', 'all');
    wp_enqueue_style('select2'); // Enqueue it!
}

//Because we have hardcoded critical CSS in the header file, load style sheet in the footer
function styles_in_wp_foot() {
    echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/style.css' .'" type="text/css" media="all" />';
}
//add_action( 'wp_footer', 'styles_in_wp_foot' );

// Register HTML5 Blank Navigation
function register_html5_menu()
{
    register_nav_menus(array( // Using array to specify more menus if needed
        'header-menu' => __('Header Menu', 'html5blank'), // Main Navigation
        'sidebar-menu' => __('Sidebar Menu', 'html5blank'), // Sidebar Navigation
        'extra-menu' => __('Extra Menu', 'html5blank') // Extra Navigation if needed (duplicate as many as you need!)
    ));
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '')
{
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var)
{
    return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist)
{
    return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes)
{
    global $post;
    if (is_home()) {
        $key = array_search('blog', $classes);
        if ($key > -1) {
            unset($classes[$key]);
        }
    } elseif (is_page()) {
        $classes[] = sanitize_html_class($post->post_name);
    } elseif (is_singular()) {
        $classes[] = sanitize_html_class($post->post_name);
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if (function_exists('register_sidebar'))
{
    // Define Sidebar Widget Area 1
    register_sidebar(array(
        'name' => __('Widget Area 1', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));

    // Define Sidebar Widget Area 2
    register_sidebar(array(
        'name' => __('Widget Area 2', 'html5blank'),
        'description' => __('Description for this widget-area...', 'html5blank'),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}

add_action( 'gform_after_submission', 'tgm_io_remove_form_entry' );
/**
 * Prevents Gravity Form entries from being stored in the database.
 *
 * @global object $wpdb The WP database object.
 * @param array $entry  Array of entry data.
 */
function tgm_io_remove_form_entry( $entry ) {

    global $wpdb;
    // Prepare variables.
    $lead_id                = $entry['id'];
    $lead_table             = RGFormsModel::get_lead_table_name();
    $lead_notes_table       = RGFormsModel::get_lead_notes_table_name();
    $lead_detail_table      = RGFormsModel::get_lead_details_table_name();
    $lead_detail_long_table = RGFormsModel::get_lead_details_long_table_name();
    // Delete from lead detail long.
    $sql = $wpdb->prepare( "DELETE FROM $lead_detail_long_table WHERE lead_detail_id IN(SELECT id FROM $lead_detail_table WHERE lead_id = %d)", $lead_id );
    $wpdb->query( $sql );
    // Delete from lead details.
    $sql = $wpdb->prepare( "DELETE FROM $lead_detail_table WHERE lead_id = %d", $lead_id );
    $wpdb->query( $sql );
    // Delete from lead notes.
    $sql = $wpdb->prepare( "DELETE FROM $lead_notes_table WHERE lead_id = %d", $lead_id );
    $wpdb->query( $sql );
    // Delete from lead.
    $sql = $wpdb->prepare( "DELETE FROM $lead_table WHERE id = %d", $lead_id );
    $wpdb->query( $sql );

    // Finally, ensure everything is deleted (like stuff from Addons).
    GFAPI::delete_entry( $lead_id );
}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style()
{
    global $wp_widget_factory;
    remove_action('wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ));
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination()
{
    global $wp_query;
    $big = 999999999;
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', get_pagenum_link($big)),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $wp_query->max_num_pages
    ));
}

// Custom Excerpts
function html5wp_index($length) // Create 20 Word Callback for Index page Excerpts, call using html5wp_excerpt('html5wp_index');
{
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post($length)
{
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt($length_callback = '', $more_callback = '')
{
    global $post;
    if (function_exists($length_callback)) {
        add_filter('excerpt_length', $length_callback);
    }
    if (function_exists($more_callback)) {
        add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article($more)
{
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar()
{
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove($tag)
{
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html )
{
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Custom Gravatar in Settings > Discussion
function html5blankgravatar ($avatar_defaults)
{
    $myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
    $avatar_defaults[$myavatar] = "Custom Gravatar";
    return $avatar_defaults;
}

// Threaded Comments
function enable_threaded_comments()
{
    if (!is_admin()) {
        if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
            wp_enqueue_script('comment-reply');
        }
    }
}

// Custom Comments Callback
function html5blankcomments($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment;
    extract($args, EXTR_SKIP);

    if ( 'div' == $args['style'] ) {
        $tag = 'div';
        $add_below = 'comment';
    } else {
        $tag = 'li';
        $add_below = 'div-comment';
    }
    ?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
    <?php if ( 'div' != $args['style'] ) : ?>
    <div id="div-comment-<?php comment_ID() ?>" class="comment-body">
<?php endif; ?>
    <div class="comment-author vcard">
        <?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['180'] ); ?>
        <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
    </div>
    <?php if ($comment->comment_approved == '0') : ?>
    <em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
    <br />
<?php endif; ?>

    <div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
            <?php
            printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
        ?>
    </div>

    <?php comment_text() ?>

    <div class="reply">
        <?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </div>
    <?php if ( 'div' != $args['style'] ) : ?>
    </div>
<?php endif; ?>
<?php }

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'html5blank_header_scripts'); // Add Custom Scripts to wp_head
add_action('wp_print_scripts', 'html5blank_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_enqueue_scripts', 'html5blank_styles'); // Add Theme Stylesheet
add_action('init', 'register_html5_menu'); // Add HTML5 Blank Menu
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'html5wp_pagination'); // Add our HTML5 Pagination

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head','wp_oembed_add_discovery_links', 10 );
remove_action('wp_head','wp_oembed_add_host_js');

// Add Filters
add_filter('avatar_defaults', 'html5blankgravatar'); // Custom Gravatar in Settings > Discussion
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
// add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected classes (Commented out by default)
// add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> injected ID (Commented out by default)
// add_filter('page_css_class', 'my_css_attributes_filter', 100, 1); // Remove Navigation <li> Page ID's (Commented out by default)
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'html5_blank_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('show_admin_bar', 'remove_admin_bar'); // Remove Admin bar
add_filter('style_loader_tag', 'html5_style_remove'); // Remove 'text/css' from enqueued stylesheet
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes
add_shortcode('html5_shortcode_demo', 'html5_shortcode_demo'); // You can place [html5_shortcode_demo] in Pages, Posts now.
add_shortcode('html5_shortcode_demo_2', 'html5_shortcode_demo_2'); // Place [html5_shortcode_demo_2] in Pages, Posts now.


/*------------------------------------*\
	ShortCode Functions
\*------------------------------------*/

// Shortcode Demo with Nested Capability
function html5_shortcode_demo($atts, $content = null)
{
    return '<div class="shortcode-demo">' . do_shortcode($content) . '</div>'; // do_shortcode allows for nested Shortcodes
}

// Shortcode Demo with simple <h2> tag
function html5_shortcode_demo_2($atts, $content = null) // Demo Heading H2 shortcode, allows for nesting within above element. Fully expandable.
{
    return '<h2>' . $content . '</h2>';
}

//Allow for SVG upload
function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

// Move JavaScript from the Head to the Footer
function remove_head_scripts() {
    remove_action('wp_head', 'wp_print_scripts');
    remove_action('wp_head', 'wp_print_head_scripts', 9);
    remove_action('wp_head', 'wp_enqueue_scripts', 1);

    add_action('wp_footer', 'wp_print_scripts', 5);
    add_action('wp_footer', 'wp_enqueue_scripts', 5);
    add_action('wp_footer', 'wp_print_head_scripts', 5);
}
add_action( 'wp_enqueue_scripts', 'remove_head_scripts' );

add_filter("gform_init_scripts_footer", "init_scripts");
function init_scripts() {
    return true;
}

add_filter( 'gform_cdata_open', 'wrap_gform_cdata_open' );
function wrap_gform_cdata_open( $content = '' ) {
    $content = 'document.addEventListener( "DOMContentLoaded", function() { ';
    return $content;
}
add_filter( 'gform_cdata_close', 'wrap_gform_cdata_close' );
function wrap_gform_cdata_close( $content = '' ) {
    $content = ' }, false );';
    return $content;
}

//CUSTOM POST TYPES


// Register Custom Post Type
function custom_post_type_plant() {

    $labels = array(
        'name'                  => _x( 'Plants', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Plant', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Plants', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Plant', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'            => array( 'month', ' plant_family' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'plant', $args );

}
add_action( 'init', 'custom_post_type_plant', 0 );

// Register Custom Taxonomy
function custom_taxonomy_month() {

    $labels = array(
        'name'                       => _x( 'Months', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Month', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Month', 'text_domain' ),
        'all_items'                  => __( 'All Items', 'text_domain' ),
        'parent_item'                => __( 'Parent Item', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
        'new_item_name'              => __( 'New Item Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Item', 'text_domain' ),
        'edit_item'                  => __( 'Edit Item', 'text_domain' ),
        'update_item'                => __( 'Update Item', 'text_domain' ),
        'view_item'                  => __( 'View Item', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Items', 'text_domain' ),
        'search_items'               => __( 'Search Items', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No items', 'text_domain' ),
        'items_list'                 => __( 'Items list', 'text_domain' ),
        'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );
    register_taxonomy( 'month', array( 'plant', ' task', 'custom_todo' ), $args );

}
add_action( 'init', 'custom_taxonomy_month', 0 );

// Register Custom Post Type
function custom_post_type_task() {

    $labels = array(
        'name'                  => _x( 'Tasks', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Task', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Tasks', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Task', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'            => array( 'month' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'task', $args );

}
add_action( 'init', 'custom_post_type_task', 0 );

// Register Custom Post Type
function custom_post_type_location() {

    $labels = array(
        'name'                  => _x( 'Locations', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Location', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Locations', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Location', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'author' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'location', $args );

}
add_action( 'init', 'custom_post_type_location', 0 );

// Register Custom Post Type
function custom_post_type_planted() {

    $labels = array(
        'name'                  => _x( 'Planted Plants', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Planted Plant', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Planted Plants', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Planted Plant', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'author' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'planted', $args );

}
add_action( 'init', 'custom_post_type_planted', 0 );

// Register Custom Post Type
function custom_post_type_journal() {

    $labels = array(
        'name'                  => _x( 'Journal Entries', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Journal Entry', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Journal Entries', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Journal Entry', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail','author' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'journal', $args );

}
add_action( 'init', 'custom_post_type_journal', 0 );

// Register Custom Post Type
function custom_post_type_header() {

    $labels = array(
        'name'                  => _x( 'Header Images', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Header Image', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Header Images', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Header Image', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'thumbnail' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'header', $args );

}
add_action( 'init', 'custom_post_type_header', 0 );

// Register Custom Post Type
function custom_post_type_custom_todo() {

    $labels = array(
        'name'                  => _x( 'Custom To Dos', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Custom To Do', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Custom Todos', 'text_domain' ),
        'name_admin_bar'        => __( 'Post Type', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Custom To Do', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'author' ),
        'taxonomies'            => array( 'month' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'show_in_rest'          => true,
    );
    register_post_type( 'custom_todo', $args );

}
add_action( 'init', 'custom_post_type_custom_todo', 0 );


//ACF Options Pages

if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title' 	=> 'Theme General Settings',
        'menu_title'	=> 'Theme Settings',
        'menu_slug' 	=> 'theme-general-settings',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));
}

function hide_editor() {
    if(isset($_GET['post']) || isset($_POST['post_ID'])) {
        $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
        if( !isset( $post_id ) ) return;

        $homepgname = get_the_title($post_id);

        if($homepgname == 'About'){
            remove_post_type_support('page', 'editor');
        }

        if($homepgname == 'Fleet'){
            remove_post_type_support('page', 'editor');
        }

        if($homepgname == 'Safety'){
            remove_post_type_support('page', 'editor');
        }

        if($homepgname == 'Home'){
            remove_post_type_support('page', 'editor');
        }
    }
}

//hide the editor for specific pages
//add_action( 'admin_init', 'hide_editor' );


//hide Yoast SEO box for sliders
function remove_yoast_metabox_reservations(){
    remove_meta_box('wpseo_meta', 'slider', 'normal');
    remove_meta_box('wpseo_meta', 'testimonial', 'normal');
}
add_action( 'add_meta_boxes', 'remove_yoast_metabox_reservations',11 );

function gimmeSVG($svg){
    if($svg == 'delete') {
        echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 315 315" enable-background="new 0 0 315 315">
                                        <g>
                                            <path d="m256.774,23.942h-64.836v-6.465c0-9.636-7.744-17.477-17.263-17.477h-34.348c-9.521,0-17.266,7.841-17.266,17.478v6.465h-64.835c-9.619,0-17.445,7.76-17.445,17.297v11.429c0,7.168 4.42,13.33 10.698,15.951 1.989,39.623 13.5,231.193 14.018,239.801 0.222,3.696 3.284,6.58 6.987,6.58h170.033c3.703,0 6.766-2.884 6.987-6.58 0.518-8.607 12.028-200.178 14.018-239.801 6.278-2.621 10.698-8.783 10.698-15.951v-11.43c5.68434e-14-9.537-7.826-17.297-17.446-17.297zm-119.713-6.464c0-1.918 1.465-3.478 3.266-3.478h34.348c1.8,0 3.264,1.56 3.264,3.478v6.465h-40.877v-6.465zm-82.282,23.761c0-1.818 1.546-3.297 3.445-3.297h198.549c1.899,0 3.445,1.478 3.445,3.297v11.429c0,1.819-1.546,3.299-3.445,3.299h-198.548c-1.899,0-3.445-1.479-3.445-3.299v-11.429zm181.143,259.761h-156.848c-2.055-34.247-11.479-191.674-13.51-231.033h183.867c-2.031,39.359-11.454,196.786-13.509,231.033z"/>
                                            <path d="m157.5,95.125c-3.866,0-7,3.134-7,7v176.109c0,3.866 3.134,7 7,7 3.866,0 7-3.134 7-7v-176.109c0-3.866-3.134-7-7-7z"/>
                                            <path d="m110.2,102.04c-0.202-3.86-3.507-6.837-7.355-6.625-3.86,0.201-6.827,3.494-6.625,7.355l9.182,175.829c0.195,3.736 3.285,6.635 6.984,6.635 0.123,0 0.247-0.003 0.371-0.01 3.86-0.201 6.827-3.494 6.625-7.355l-9.182-175.829z"/>
                                            <path d="m212.155,95.415c-3.899-0.223-7.153,2.764-7.355,6.625l-9.184,175.829c-0.202,3.861 2.765,7.154 6.625,7.355 0.125,0.007 0.248,0.01 0.371,0.01 3.698,0 6.789-2.898 6.984-6.635l9.184-175.829c0.202-3.861-2.764-7.154-6.625-7.355z"/>
                                        </g>
                                    </svg>';
    }
    if($svg == 'edit') {
        echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 612 612" style="enable-background:new 0 0 612 612;" xml:space="preserve">
<g>
	<g>
		<g>
			<path d="M239.486,368.979c3.389,3.388,7.828,5.081,12.267,5.081c4.442,0,8.88-1.695,12.27-5.081L447.32,185.683     c6.775-6.777,6.775-17.762,0-24.536c-6.774-6.775-17.762-6.775-24.538,0L239.486,344.441     C232.711,351.218,232.711,362.202,239.486,368.979z"/>
			<path d="M604.149,110.573c-6.702-20.057-21.54-41.957-42.922-63.333C539.68,25.69,506.868,0,472.925,0     c-15.226,0-29.255,5.354-40.679,15.501c-0.596,0.457-1.164,0.956-1.7,1.492l-65.823,65.821H20.838     c-9.582,0-17.35,7.769-17.35,17.35V594.65c0,9.582,7.769,17.35,17.35,17.35h494.485c9.582,0,17.35-7.769,17.35-17.35V240.19     c0-1.081-0.113-2.134-0.302-3.161l57.622-57.623c0.116-0.114,0.231-0.227,0.344-0.343c0.003-0.001,0.006-0.004,0.009-0.007     l1.129-1.131c0.534-0.534,1.022-1.09,1.47-1.673C608.724,158.554,612.602,135.88,604.149,110.573z M236.139,469.784     l-122.416,35.331l27.797-129.951L236.139,469.784z M267.877,452.447L156.023,340.592l260.97-260.974l111.856,111.856     L267.877,452.447z M425.445,512.469H213.384l56.919-16.428c2.818-0.814,5.383-2.328,7.458-4.401l220.211-220.211v305.871H38.188     V117.515h291.836L118.818,328.723c-2.367,2.367-3.999,5.367-4.699,8.64L73.73,526.188c-1.277,5.964,0.675,12.161,5.137,16.321     c3.256,3.033,7.498,4.659,11.833,4.659c0.927,0,334.745,0,334.745,0 M566.49,153.768c-0.189,0.202-0.373,0.409-0.551,0.619     l-0.124,0.123c-0.059,0.059-0.121,0.119-0.181,0.178l-12.248,12.246L441.531,55.08l12.53-12.53     c0.217-0.184,0.432-0.373,0.641-0.571c5.315-4.965,11.107-7.278,18.224-7.278c16.963,0,40.208,13.513,63.767,37.078     C549.597,84.681,589.886,128.729,566.49,153.768z"/>
		</g>
	</g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg>';
    }
    if($svg == 'journal') {
        echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 297 297" style="enable-background:new 0 0 297 297;" xml:space="preserve">
<g>
	<path d="M277.915,0H36.341c-5.775,0-10.458,4.683-10.458,10.458v24.053h-6.798c-5.775,0-10.457,4.683-10.457,10.458   c0,5.774,4.682,10.457,10.457,10.457h6.798v13.596h-6.798c-5.775,0-10.457,4.682-10.457,10.457s4.682,10.458,10.457,10.458h6.798   v13.595h-6.798c-5.775,0-10.457,4.683-10.457,10.458c0,5.775,4.682,10.458,10.457,10.458h6.798v13.595h-6.798   c-5.775,0-10.457,4.683-10.457,10.458s4.682,10.458,10.457,10.458h6.798v13.595h-6.798c-5.775,0-10.457,4.683-10.457,10.458   c0,5.775,4.682,10.458,10.457,10.458h6.798v13.595h-6.798c-5.775,0-10.457,4.683-10.457,10.458s4.682,10.457,10.457,10.457h6.798   v13.596h-6.798c-5.775,0-10.457,4.682-10.457,10.457c0,5.775,4.682,10.458,10.457,10.458h6.798v24.053   c0,5.775,4.683,10.458,10.458,10.458h241.574c5.775,0,10.457-4.683,10.457-10.458V10.458C288.372,4.683,283.69,0,277.915,0z    M267.457,276.084H46.798v-13.595h6.798c5.775,0,10.458-4.683,10.458-10.458c0-5.775-4.683-10.457-10.458-10.457h-6.798v-13.596   h6.798c5.775,0,10.458-4.682,10.458-10.457s-4.683-10.458-10.458-10.458h-6.798v-13.595h6.798c5.775,0,10.458-4.683,10.458-10.458   c0-5.775-4.683-10.458-10.458-10.458h-6.798v-13.595h6.798c5.775,0,10.458-4.683,10.458-10.458s-4.683-10.458-10.458-10.458h-6.798   v-13.595h6.798c5.775,0,10.458-4.683,10.458-10.458c0-5.775-4.683-10.458-10.458-10.458h-6.798V89.937h6.798   c5.775,0,10.458-4.683,10.458-10.458s-4.683-10.457-10.458-10.457h-6.798V55.426h6.798c5.775,0,10.458-4.683,10.458-10.457   c0-5.775-4.683-10.458-10.458-10.458h-6.798V20.916h220.659V276.084z"/>
	<path d="M105.362,141.702h120.786c5.775,0,10.458-4.683,10.458-10.458v-69.02c0-5.775-4.683-10.458-10.458-10.458H105.362   c-5.775,0-10.458,4.683-10.458,10.458v69.02C94.904,137.02,99.587,141.702,105.362,141.702z M115.819,72.682h99.872v48.105h-99.872   V72.682z"/>
	<path d="M105.362,210.724h120.786c5.775,0,10.458-4.683,10.458-10.458c0-5.775-4.683-10.457-10.458-10.457H105.362   c-5.775,0-10.458,4.682-10.458,10.457C94.904,206.041,99.587,210.724,105.362,210.724z"/>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg>';
    }

    if($svg == 'newplant') {
        echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 463.001 463.001" enable-background="new 0 0 463.001 463.001">
  <g>
    <g>
      <path d="m441.044,308.903c-3.285-8.994-10.277-15.956-19.184-19.102-8.9-3.145-18.637-2.163-26.706,2.69l-78.169,46.902-10.752,4.607h-56.404c-0.691,0-1.077-0.386-1.278-0.71-0.202-0.324-0.379-0.841-0.073-1.46 0.174-0.354 0.474-0.622 0.845-0.754l42.802-15.295c16.563-5.566 26.054-22.511 21.614-38.615-2.33-8.452-7.861-15.368-15.573-19.475-7.594-4.044-16.624-4.798-24.766-2.071l-44.166,14.719-115.97,7.733c-7.614,0.508-14.778,3.759-20.173,9.154l-63.084,63.083c-12.102,12.102-13.223,31.298-2.553,43.701 2.82,3.279 14.975,14.926 33.195,32.322 10.618,10.137 20.646,19.711 25.407,24.472 1.458,1.458 3.378,2.197 5.306,2.197 1.698,0 3.403-0.575 4.8-1.738l34.649-28.875c7.263-6.053 16.471-9.387 25.927-9.387h118.622c4.294,0 8.473-0.857 12.42-2.549l55.987-23.995c0.156-0.067 0.31-0.139 0.46-0.216l2.842-1.453c0.151-0.077 0.3-0.159 0.444-0.247l79.285-47.571c13.38-8.028 19.371-24.037 14.246-38.067zm-21.962,25.204l-79.067,47.44-2.39,1.222-55.753,23.895c-2.069,0.887-4.261,1.336-6.512,1.336h-118.622c-12.958,0-25.576,4.568-35.53,12.864l-29.436,24.53c-5.38-5.224-12.912-12.415-20.765-19.912-13.202-12.604-29.632-28.289-32.183-31.255-5.488-6.379-4.686-16.837 1.789-23.312l63.084-63.084c2.824-2.825 6.576-4.527 10.563-4.793l116.93-7.797c0.638-0.042 1.267-0.166 1.872-0.368l45.088-15.027c1.724-0.577 3.49-0.863 5.247-0.863 2.656,0 5.289,0.654 7.716,1.946 4.04,2.152 6.939,5.782 8.163,10.223 2.288,8.298-3.086,17.461-11.979,20.425-0.051,0.017-0.102,0.035-0.151,0.053l-42.872,15.32c-4.063,1.452-7.35,4.384-9.256,8.256-2.534,5.152-2.236,11.137 0.798,16.011 3.034,4.874 8.272,7.783 14.013,7.783h57.943c1.016,0 2.021-0.206 2.954-0.606l12.635-5.414c0.312-0.133 0.614-0.288 0.904-0.462l78.612-47.168c4.224-2.542 9.32-3.052 13.986-1.405 4.737,1.674 8.321,5.262 10.09,10.105 2.62,7.17-0.764,15.793-7.871,20.057z"/>
      <path d="m283.86,222.171v17.329c0,4.142 3.357,7.5 7.5,7.5s7.5-3.358 7.5-7.5v-23.144c0-0.003 0-0.007 0-0.01v-35.026c72.319-10.024 129.001-68.702 135.967-141.961 0.211-2.227-0.582-4.432-2.163-6.013-1.582-1.583-3.791-2.378-6.014-2.163-56.453,5.368-104.245,40.259-127.79,89.077v-88.76c0-9.098 7.401-16.5 16.5-16.5 4.143,0 7.5-3.358 7.5-7.5s-3.357-7.5-7.5-7.5c-17.369,0-31.5,14.131-31.5,31.5v28.212c-15.596-24.116-41.665-40.814-71.79-43.678-2.224-0.212-4.433,0.581-6.014,2.163-1.581,1.582-2.374,3.787-2.163,6.013 4.147,43.615 37.298,78.706 79.967,85.844v51.058c-23.546-48.818-71.337-83.711-127.79-89.078-2.227-0.214-4.432,0.581-6.014,2.163-1.581,1.582-2.374,3.787-2.163,6.013 6.966,73.259 63.648,131.936 135.967,141.961zm-63.519-189.691c30.938,6.768 55.271,31.101 62.039,62.04-30.94-6.768-55.273-31.101-62.039-62.04zm198.349,14.839c-9.04,55.857-50.24,100.852-104.009,115.466l61.982-61.982c2.929-2.929 2.929-7.678 0-10.606-2.93-2.929-7.678-2.929-10.607,0l-63.451,63.451c13.935-54.904 59.426-97.159 116.085-106.329zm-138.575,147.179l-63.451-63.451c-2.93-2.929-7.678-2.929-10.607,0s-2.929,7.678 0,10.606l61.982,61.982c-53.77-14.615-94.969-59.61-104.009-115.466 56.658,9.17 102.15,51.426 116.085,106.329z"/>
    </g>
  </g>
</svg>';
    }

}

?>
