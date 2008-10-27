<div class="small" id="sidebar-1">
	<?php if ( is_404() || is_category() || is_day() || is_month() || is_year() || is_tag() || is_paged() ) { ?>
	<div class="sidebar first-child">
		<?php /* If this is a 404 page */ if (is_404()) { ?>
		<?php /* If this is a category archive */ } elseif (is_category()) { ?>
		<h4>You are currently browsing the archives for <?php single_cat_title(''); ?>.</h4>

		<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h4>You are currently browsing the archives for the day <?php the_time('l, F jS, Y'); ?>.</h4>

		<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h4>You are currently browsing the archives for <?php the_time('F, Y'); ?>.</h4>

		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h4>You are currently browsing the archives for the year <?php the_time('Y'); ?>.</h4>

		<?php /* If this is a yearly archive */ } elseif (is_tag()) { ?>
		<h4>You are currently browsing the archives for the tag <i>'<?php single_tag_title(); ?>'</i>.</h4>

		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h4>You are currently browsing the archives.</h4>
		<?php } ?>
	</div>
	<?php } ?>
	
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar')) : ?>
	<!-- Default Sidebar content -->
	<?php if (!is_page()) :?>
	<div class="sidebar first-child">
		<h3>Bookmarks</h3>
		<ul><?php wp_list_bookmarks('title_li=&categorize=0'); ?></ul>
	</div>
	<?php else : ?>
	<div class="sidebar first-child">
		<?php
		if($post->post_parent)
		$children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0"); else
		$children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0"); ?>
		<h3>Navigation</h3>
		<ul>
		<?php if ($children) { ?>
		<?php echo $children; ?>
		<?php } else { ?>
		<?php wp_list_pages('title_li=&depth=2'); ?>
		<?php } ?>
		</ul>
	</div>
	<?php endif; ?>
	
	<div class="sidebar">
		<h3>Categories</h3>
		<ul><?php wp_list_categories('title_li=&show_count=0&category_before=&category_after='); ?></ul>
	</div>
	
	<div class="sidebar">
		<h3>Archives</h3>
		<ul><?php wp_get_archives('type=monthly'); ?></ul>
	</div>
	
	<!-- Search Form-->
	<?php $filename = TEMPLATEPATH . '/searchform.php'; if (file_exists($filename)) { include($filename); } ?>
	
	<?php if(is_home() AND get_the_author_description() != '') : ?>
	<div class="tabbertab sidebar">
		<h3>About</h3>
		<p><?php the_author_description(); ?></p>
	</div>
	<?php endif; ?>
	
<?php endif; ?>

</div>