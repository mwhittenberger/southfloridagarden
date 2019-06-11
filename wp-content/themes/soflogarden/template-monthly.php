<?php /* Template Name: Monthly Garden Task List */

require_once('login-redirect.php');
get_header();

if(isset($_GET["month"]))
    $this_month = $_GET["month"];
else
    $this_month = strtolower (date('F'));

$calendar = array('january' => '01', 'february' => '02', 'march' => '03', 'april' => '04', 'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08', 'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12');

$previous_month = strtolower(Date("F", strtotime("2019-".$calendar[$this_month]."-01 -1 Month")));
$next_month = strtolower(Date("F", strtotime("2019-".$calendar[$this_month]."-01 +1 Month")));
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <main role="main">
                <!-- section -->
                <section>

                    <h1><?php the_title(); echo " - ".Date("F", strtotime("2019-".$calendar[$this_month]."-01")); ?></h1>

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

                <div class="navigate-months">
                    <div class="previous-month"><a href="/monthly-south-florida-gardening-guide/?month=<?php echo $previous_month; ?>"><i class="fa fa-chevron-left" aria-hidden="true"></i>&nbsp;Previous Month</a></div>
                    <div class="next-month"><a href="monthly-south-florida-gardening-guide/?month=<?php echo $next_month; ?>">Next Month&nbsp; <i class="fa fa-chevron-right" aria-hidden="true"></i></a></div>
                </div>

                <section id="thetasks">
                    <h2>Tasks for this Month</h2>
                    <?php
                    $args = array(
                        'post_type' => 'task',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'month',
                                'field'    => 'slug',
                                'terms'    => $this_month,
                            ),
                        )
                    );

                    $loop = new WP_Query($args);
                    if ( $loop->have_posts() ) :
                        echo "<ul id='monthly-tasks'>";
                        while ( $loop->have_posts() ) : $loop->the_post(); ?>

                            <?php echo "<li><label>
    <input type=\"checkbox\" class=\"option-input checkbox complete\" />
    <span>".get_the_title()."</span>
  </label><br>".get_the_content()."</li>"; ?>

                    <?php

                        endwhile;
                        echo "</ul>"; ?>

                    <?php else: ?>
                    There are no tasks for this month.
                    <?php endif;
                    wp_reset_postdata();
                    ?>

                    <div class="add-custom-task"><i class="fa fa-plus" aria-hidden="true"></i> Add a Task</div>

                    <h2>Plant or Start by Seed</h2>
                    <?php
                    $args = array(
                        'post_type' => 'plant',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order'   => 'ASC',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'month',
                                'field'    => 'slug',
                                'terms'    => $this_month,
                            ),
                        )
                    );

                    $loop = new WP_Query($args);
                    if ( $loop->have_posts() ) :
                        echo "<ul id='what-to-plant'>";
                        while ( $loop->have_posts() ) : $loop->the_post(); ?>

                            <li>
                                <div><img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>" alt="<?php echo get_the_title(); ?>"></div>
                                <div><span><?php echo get_the_title(); ?></span>
                                <br><span>Add to My Garden Now</span>
                                <br><span>Add to My Garden Plan</span></div>
                            </li>


                            <?php

                        endwhile; echo "</ul>";?>

                    <?php else: ?>
                        There are no plants that are recommended to be planted this month.
                    <?php endif;
                    wp_reset_postdata();
                    ?>

                </section>

            </main>
        </div>
    </div>
</div>

<?php //get_sidebar(); ?>

<?php get_footer(); ?>
