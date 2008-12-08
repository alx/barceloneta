<div class="small" id="sidebar-1">
	
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