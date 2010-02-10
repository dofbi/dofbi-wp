<?php 
/** 
 * @package WordPress 
 * @subpackage Default_Theme 
 */ 

get_header(); ?>

<div id="content" class="widecolumn" role="main">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
		<div class="navigation">
			<div class="alignleft"><?php previous_post_link('&laquo; %link') ?></div>
			<div class="alignright"><?php next_post_link('%link &raquo;') ?></div>
		</div>
		
		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">Lire la suite de l\'article &raquo;</p>'); ?>
				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php the_tags( '<p>Mots-clefs&nbsp;: ', ', ', '</p>'); ?> 
				<p class="postmetadata alt">
					<small>
						Cet article  a été publié 
						<?php 
						/*
							Cette partie est commentée parce qu'elle demande parfois un petit ajustement .
							Vous aurez besoin de télécharger ce plugin, et de suivre les instructions contenues dans la page :
							http://binarybonsai.com/wordpress/time-since/ 
						*/
						/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */
						?>
						le <?php the_time('l j F Y') ?> à <?php the_time() ?>
						et est classé dans <?php the_category(', ') ?>.
						Vous pouvez en suivre les commentaires par le biais du flux  
						<?php post_comments_feed_link('RSS 2.0'); ?>. 
						<?php if ( comments_open() && pings_open() ) {
							// Both Comments and Pings are open ?>
							Vous pouvez  <a href="#respond">laisser un commentaire</a>, ou <a href="<?php trackback_url(); ?>" rel="trackback">faire un trackback</a> depuis votre propre site.
						<?php } elseif ( !comments_open() && pings_open() ) {
							// Only Pings are Open ?>
							Les commentaires sont fermés, mais vous pouvez  <a href="<?php trackback_url(); ?> " rel="trackback">faire un trackback</a> depuis votre propre site.
						<?php } elseif ( comments_open() && !pings_open() ) {
							// Comments are open, Pings are not ?>
							Vous pouvez aller directement à la fin et laisser un commentaire. Les pings ne sont pas autorisés.
						<?php } elseif ( !comments_open() && !pings_open() ) {
							// Neither Comments, nor Pings are open ?>
							Les commentaires et pings sont fermés.
						<?php } edit_post_link('Modifier cet article','','.'); ?>
					</small>				
				</p>
			</div>
		</div>
		<?php comments_template(); ?>
	
	<?php endwhile; else: ?>
	
		<p>Désolé, aucun article ne correspond à vos critères.</p>
		
	<?php endif; ?>
</div>

<?php get_footer(); ?>