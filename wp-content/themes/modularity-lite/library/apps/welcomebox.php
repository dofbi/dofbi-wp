<?php //Load Variables
  $welcomebox_state = get_option('T_welcomebox_state'); 
  $welcomebox_title = get_option('T_welcomebox_title');
  $welcomebox_content = get_option('T_welcomebox_content');
?> 

<?php
if ($welcomebox_state == 'On') {?>
  <div class="welcomebox entry">
    <h2><?php echo $welcomebox_title; ?></h2>
    <?php echo $welcomebox_content; ?>
  </div><!--end welcome-box-->
<?php } ?>