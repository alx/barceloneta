<?php get_header(); ?>
		
	<div class="large">
	<?php if (have_posts()) : ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-title">
			<h2>Search Results for <i>'<?php the_search_query(); ?>'</i></h2>
			</div>
		</div>
		
		<?php while (have_posts()) : the_post(); ?>

		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="post-title">
			<h3 style="padding-bottom:3px"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
			<p style="padding-bottom:3px"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_permalink() ?></a></p>
			</div>
			
			<div class="post-content">
			<?php the_excerpt("&hellip;"); ?>
			</div>
		</div>
		
		<?php endwhile; ?>
		
		<div class="post-nav">
			<div class="previous"><?php previous_posts_link('&lsaquo; Previous Page') ?></div>
			<div class="next"><?php next_posts_link('Next Page &rsaquo;') ?></div>
		</div>
		
	<?php else : ?>
		
		<div class="post">
			<div class="post-title">
			<h2>Nothing Found</h2>
			</div>
			
			<div class="post-content">
			<p>Sorry, no posts matched your search for <i>'<?php the_search_query(); ?>'</i></p>
			</div>
		</div>

	<?php endif; ?>
	</div>

<?php get_sidebar(); ?>		

<?php get_footer(); ?>