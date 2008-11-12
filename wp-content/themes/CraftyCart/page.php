<?php get_header(); ?>
		
	<div class="large">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-title">
			<p class="post-edit" style="float:right;"><?php edit_post_link('Edit','',''); ?></p>
			<h2>
				<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent link to <?php the_title(); ?>">
				<?php the_title(); ?></a>
			</h2>
			</div>
			
			<div class="post-content">
				<?php the_content("Continue reading&hellip;"); ?>
				<?php link_pages('<p class="pagenav">Page: ', '</p>'); ?>
			</div>
		</div>
		
		<?php comments_template(); ?>

		<?php endwhile; ?>
	<?php else : ?>
	
		<?php /* Error 404 */ ?>	
		<?php $filename = TEMPLATEPATH . '/404.php'; if (file_exists($filename)) { include($filename); } ?>

	<?php endif; ?>
	</div>

<?php get_sidebar(); ?>		

<?php get_footer(); ?>