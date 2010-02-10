<?php

function postimage($size=medium) {
	if ( $images = get_children(array(
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',)))
	{
		foreach( $images as $image ) {
			$attachmenturl=wp_get_attachment_url($image->ID);
			$attachmentimage=wp_get_attachment_image( $image->ID, $size );
			$imagelink=get_permalink($image->post_parent);

			echo '<a href="'.$imagelink.'">'.$attachmentimage.apply_filters('the_title', $parent->post_title).'</a>';
		}
	} 
}

?>