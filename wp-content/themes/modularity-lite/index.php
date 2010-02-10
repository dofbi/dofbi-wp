<?php get_header(); ?>

<!-- Show the welcome box and slideshow only on first page.  Makes for better pagination. -->
<?php if ( $paged < 1 ) { ?>

<!-- Begin Welcome Box -->
<?php if (is_home()) include (THEMELIB . '/apps/welcomebox.php'); ?>

<!-- Begin Slideshow -->
<?php include (THEMELIB . '/apps/slideshow-static.php'); ?>

<!-- End Better Pagination -->
<?php } ?>

<!-- Begin Blog -->
<?php include (THEMELIB . '/apps/blog.php'); ?>

<!-- Begin Footer -->
<?php get_footer(); ?>