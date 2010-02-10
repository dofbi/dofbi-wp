<?php
	$values = get_post_custom_values("multimedia");
	if (isset($values[0])) {
	?>
<div class="multimedia">
	<?php $values = get_post_custom_values("multimedia"); echo $values[0]; ?>
</div>
<div class="clear"></div>
<?php } ?>