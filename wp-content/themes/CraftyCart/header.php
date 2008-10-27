<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<title><?php if (is_home()) { bloginfo('description'); ?> &mdash; <?php } else { wp_title('',true); ?> &mdash; <?php } ?><?php bloginfo('name'); ?></title>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/javascript/searchlabel.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/javascript/iehover.js"></script>
	<?php wp_head(); ?>
	<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style-ecommerce.css" type="text/css" media="screen" />
	<!-- WordPress Theme by Billion Studio www.billionstudio.com -->
</head>

<body id="top">
<div id="page">

	<div id="header">
		<div class="container">
			<h1><a href="<?php bloginfo('url'); ?>" title="Go to homepage"><?php bloginfo('name'); ?></a></h1>
			<?php if (get_bloginfo('description')) : ?><h4><?php bloginfo('description'); ?></h4><?php endif; ?>
		</div>
	</div>

	<div id="navigation">
		<div class="container">
			<?php $filename = TEMPLATEPATH . '/navigation.php'; if (file_exists($filename)) { include($filename); } ?>
		</div>
	</div>

<div id="content">
	<div class="container body">