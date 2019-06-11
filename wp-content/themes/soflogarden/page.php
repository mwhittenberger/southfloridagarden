<?php get_header(); ?>

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
			</main>
		</div>
	</div>
</div>

<?php //get_sidebar(); ?>

<?php get_footer(); ?>
