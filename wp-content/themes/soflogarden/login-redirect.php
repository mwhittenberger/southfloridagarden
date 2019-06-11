<?php
if(!is_user_logged_in()) :
    get_header();
?>

<div class="container">
    You must be logged in to access this part of the website. Please login or register an account.
</div>

<?php
//wp_redirect( $url );
get_footer();
exit;
endif;
?>