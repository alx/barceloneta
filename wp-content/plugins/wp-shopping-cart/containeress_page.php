<?php
/*
Template Name: Containerless Page
*/
?>

<?php get_header(); ?>

	<div class="content">
<!-- 		<div class="container"> -->

					<?php if (have_posts()) : ?><?php while (have_posts()) : the_post(); ?>
<!-- 					<h2><?php the_title(); ?></h2> -->
					<?php the_content('Read the rest of this entry &raquo;'); ?>
					<?php endwhile; ?>
					<?php else : ?>
						<h2 class="center">Not Found</h2>
						<p class="center">Sorry, but you are looking for something that isn't here.</p>
						<?php include (TEMPLATEPATH . "/searchform.php"); ?>
					<?php endif; ?>

<!-- 			</div> -->
		</div>
	</div>
<!--
<?php get_sidebar(); ?>
-->
<?php get_footer(); ?>