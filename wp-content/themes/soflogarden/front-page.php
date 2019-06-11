<?php get_header(); ?>

<div class="weather">
    <a class="weatherwidget-io" href="https://forecast7.com/en/26d12n80d14/fort-lauderdale/?unit=us" data-label_1="South Florida" data-label_2="WEATHER" data-theme="beige" >South Florida WEATHER</a>
    <script>
        !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='https://weatherwidget.io/js/widget.min.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','weatherwidget-io-js');
    </script>
</div>

<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <main role="main">
                <!-- section -->
                <section>



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
            </main>
        </div>
    </div>
</div>

<?php //get_sidebar(); ?>

<?php get_footer(); ?>
