<?php /* Template Name: Plants List */ get_header(); ?>

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

                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

                    <?php
                    $loop = new WP_Query( array( 'post_type' => 'plant', 'posts_per_page' => -1, 'orderby' => 'title',
                        'order'   => 'ASC' ) );
                    if ( $loop->have_posts() ) :
                        while ( $loop->have_posts() ) : $loop->the_post(); ?>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <div class="plant-title-wrap" data-bg="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" data-toggle="collapse" data-parent="#accordion" href="#<?php echo get_post_field( 'post_name', get_post() ); ?>" aria-expanded="true" aria-controls="<?php echo get_post_field( 'post_name', get_post() ); ?>">
                                            <div>
                                                <a role="button" >
                                                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>" alt="<?php echo get_the_title(); ?>">
                                                </a>
                                            </div>
                                            <div>
                                                <a role="button" >
                                                    <?php echo get_the_title(); ?>
                                                </a>
                                            </div>
                                        </div>

                                    </h4>
                                </div>
                                <div id="<?php echo get_post_field( 'post_name', get_post() ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        <?php the_content(); ?>
                                        <div class="recommended">
                                            Recommended Varieties for South Florida: <span style="font-weight: bold"><?php echo get_post_meta( get_the_ID(), 'recommended_varieties', true ); ?></span>
                                        </div>
                                        <div class="plant-stats">
                                            <div>Seed Planting Depth: <span style="font-weight: bold"><?php echo get_post_meta( get_the_ID(), 'seed_depth', true ); ?> inches</span></div>
                                            <div>Germination Time : <span style="font-weight: bold"><?php echo get_post_meta( get_the_ID(), 'germination', true ); ?> days</span></div>
                                            <div>Time to Harvest : <span style="font-weight: bold"><?php echo get_post_meta( get_the_ID(), 'harvest_min', true ); ?> -  <?php echo get_post_meta( get_the_ID(), 'harvest_max', true ); ?> days</span></div>
                                        </div>
                                        <?php if(is_user_logged_in()) : ?>
                                        <div class="best-time">
                                            Best Months for Planting:
                                            <?php
                                            $month_string = '';

                                            $categories=get_the_terms(get_the_ID(), 'month');

                                            foreach($categories as $category) {

                                                $month_string .= $category->term_id .",";
                                            }
                                            $month_string = substr($month_string, 0, -1);

                                            $cat_args= array(
                                                'orderby' => 'id',
                                                'order' => 'ASC',
                                                'taxonomy' => 'month',
                                                'include' => $month_string
                                            );
                                            $categories=get_terms($cat_args);
                                            $month_string = '';

                                            foreach($categories as $category) {
                                                $month_string .= $category->name .", ";
                                            }
                                            echo '<span style="font-weight: bold;">'.substr($month_string, 0, -2).'</span>';

                                            ?>
                                        </div>
                                        <?php endif; ?>
                                        <img class="plant-image">
                                    </div>
                                </div>
                            </div>
                    <?php endwhile;

                    endif;
                    wp_reset_postdata();
                    ?>


                </div>

            </main>
        </div>
    </div>
</div>



<?php get_footer(); ?>

