<?php
/*
Plugin Name: Simple Deezer
Plugin URI: http://www.herewithme.fr/wordpress-plugins/simple-deezer
Description: This plugin add 2 shortcodes to easily put a Deezer Player into post content
Version: 1.0
Author: Amaury BALMER
Author URI: http://www.herewithme.fr

---
New Syntax : [dz id="123" alt="Text alternatif"]
Old Syntax : [dz | 123 | texte alternatif]
---

Copyright 2008 Amaury BALMER (balmer.amaury@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
*/

// HTML Code for Deezer
// Start edition
define("DEEZER_HTML", '
	<div class="deezer">
		###TEXT###
		<br />
		<object width="220" height="55" type="application/x-shockwave-flash" data="http://www.deezer.com/embedded/small-widget-v2.swf?idSong=###ID###&amp;colorBackground=0x999999&amp;colorButtons=0x666666&amp;textColor1=0x000000&amp;autoplay=0">
			<param name="movie" value="http://www.deezer.com/embedded/small-widget-v2.swf?idSong=###ID###&amp;colorBackground=0x999999&amp;colorButtons=0x666666&amp;textColor1=0x000000&amp;autoplay=0" />
			<param name="wmode" value="transparent" />
		</object>
		<br />
		###LINK###
	</div>');
// End edition

/* That's all, stop editing! Happy blogging. */
add_filter('the_content', 'sd_old_syntax', 1);
function sd_old_syntax( $content ) {
	if (strpos ( $content, '[dz |' ) != false) {
		$content = preg_replace_callback( '/\[dz(.*?)\]/s', 'sd_format_deezer', $content );
	}
	return $content;
}

add_shortcode('dz', 'sd_new_syntax');
function sd_new_syntax( $atts = '' ) {
	extract(shortcode_atts(array('id' => '','alt' => ''), $atts));
	return sd_build_html( $id, $alt );
}

function sd_build_html( $id, $alt ) {
	$id = (int) $id;
	if ( $id == 0 ) {
		return '';
	}

	$output = str_replace("###ID###", $id, DEEZER_HTML);
	$output = str_replace("###TEXT###", 'En &eacute;coute avec Deezer &raquo; <strong>' . $alt . '</strong>', $output);
	$output = str_replace("###LINK###", '<a href="http://www.deezer.com/track/'. $id .'" rel="nofollow">www.deezer.com/track/' . $id . '</a>', $output);

	return $output;
}

function sd_format_deezer( $result ) {
	$deezer = explode( '|', trim($result[1]) );
	return sd_build_html( $deezer[1], $deezer[2] );
}
?>