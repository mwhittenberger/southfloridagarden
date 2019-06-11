<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

		<link href="//www.google-analytics.com" rel="dns-prefetch">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?php bloginfo('description'); ?>">

		<?php wp_head(); ?>
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,700&display=swap" rel="stylesheet">

        <?php if(is_user_logged_in()) : ?>
        <style>
            #menu-item-446 {
                display: none;
            }
        </style>
        <?php endif; ?>

	</head>
	<body <?php body_class(); ?>>

		<!-- wrapper -->
		<div class="ger_the_wrapper">

			<!-- header -->
			<header class="header clear" role="banner">

                        <?php
                        global $post;
                        $tmp_post = $post;
                        $myposts = get_posts( 'post_type=header&numberposts=1&orderby=rand' );
                        foreach( $myposts as $post ) : setup_postdata($post);
                            $header_image = get_the_post_thumbnail_url(get_the_ID(), "full");
                        endforeach;
                        wp_reset_postdata();
                        ?>
						<div class="header-image" style="background-image:url(<?php echo $header_image; ?>);background-repeat: no-repeat; background-size: cover; background-position: center;">
                            <div class="darkener"></div>
                            <?php if(!is_user_logged_in()) : ?>
                            <div class="myaccount">
                                <a href="#">Register <i class="fa fa-user-circle-o" aria-hidden="true"></i></a>
                            </div>
                            <?php endif ?>
                            <div class="login">
                                <?php if(!is_user_logged_in()) : ?>
                                <a href="/login/">Login <i class="fa fa-user-circle-o" aria-hidden="true"></i></a>
                                <?php else : ?>
                                    <a href="../wp-login.php?action=logout">Logout <i class="fa fa-user-circle-o" aria-hidden="true"></i></a>

                                <?php endif ?>
                            </div>

							<!-- logo -->
							<div class="logo">
								<a href="<?php echo home_url(); ?>">
									<!-- svg logo - toddmotto.com/mastering-svg-use-for-a-retina-web-fallbacks-with-png-script -->
									<img src="/wp-content/uploads/2019/05/header-logo.png" alt="Logo" class="logo-img">
								</a>
							</div>
							<!-- /logo -->
                            <div id="user-id" style="display: none;" logged-user="<?php echo get_current_user_id(); ?>"></div>


			</header>
            <div class="outerstrip">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="thestrip">
                                <div class="social">
                                    <div class="facebook"><a href="#" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></div>
                                    <div class="instagram"><a href="#" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></div>
                                    <div class="pinterest"><a href="#" target="_blank"><i class="fa fa-pinterest-square" aria-hidden="true"></i></a></div>
                                    <div class="youtube"><a href="#" target="_blank"><i class="fa fa-youtube" aria-hidden="true"></i></a></div>
                                    <div class="iosstore"><a href="#" target="_blank"><img src="/wp-content/uploads/2019/05/download-on-the-app-store-apple.png"></a></div>
                                    <div class="googleplay"><a href="#" target="_blank"><img src="/wp-content/uploads/2019/05/google-play-badge.png"></a></div>
                                </div>
                                <div class="thenav">
                                    <div id="menuToggle">

                                        <input type="checkbox">

                                        <span></span>
                                        <span></span>
                                        <span></span>

                                    </div>
                                </div>
                            </div>
                            <div class="fancy-nav">
                                <?php
                                wp_nav_menu( array(
                                    'menu'   => 'Primary'
                                ) );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


			<!-- /header -->
