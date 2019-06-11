<?php get_header(); ?>

	<div class="ger_the_wrapper">
		<div class="container">
			<div class="row">
				<div class="col-sm-9">
					<main role="main">
						<!-- section -->
						<section>

							<h1 class="inner_page_h1"><?php single_cat_title(); ?> Related Posts</h1>

							<?php get_template_part('loop'); ?>

							<?php get_template_part('pagination'); ?>

						</section>
						<!-- /section -->
					</main>
				</div>
				<div class="col-sm-3">
					<?php get_sidebar(); ?>
				</div>
			</div>
		</div>
	</div>
<?php get_footer(); ?>