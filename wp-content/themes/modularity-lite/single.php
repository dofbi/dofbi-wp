<?php get_header(); ?>
<div class="span-<?php
		$sidebar_state = get_option('T_sidebar_state');

		if($sidebar_state == "On") {
			echo "15 colborder home";
		}
		else {
			echo "24 last";
		}
		?>">
<div class="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<h2><?php the_title(); ?></h2>
<?php include (THEMELIB . '/apps/multimedia.php'); ?>
<?php the_content(); ?>
</div>
<div class="clear"></div>
<?php the_meta(); ?>
<p class="postmetadata alt">
					<small>
						Cet article a &eacute;t&eacute; post&eacute;
						<?php /* This is commented, because it requires a little adjusting sometimes.
							You'll need to download this plugin, and follow the instructions:
							http://binarybonsai.com/archives/2004/08/17/time-since-plugin/ */
							/* $entry_datetime = abs(strtotime($post->post_date) - (60*120)); echo time_since($entry_datetime); echo ' ago'; */ ?>
						le <?php the_time('l, F jS, Y') ?> &agrave; <?php the_time() ?>
						et est class&eacute; dans <?php the_category(', ') ?><?php if (get_the_tags()) the_tags(' et avec les tags '); ?>.
							Vous pouvez suivre les r&eacute;ponses &agrave; ce bulletin avec le fil <?php post_comments_feed_link('RSS 2.0'); ?>.

						<?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Both Comments and Pings are open ?>
							Vous pouvez <a href="#respond">laisser une r&eacute;ponse</a>, ou faire un <a href="<?php trackback_url(); ?>" rel="trackback">trackback</a> depuis votre propre site.

						<?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
							// Only Pings are Open ?>
							Les r&eacute;ponses sont actuellement ferm&eacute;es, mais vous pouvez <a href="<?php trackback_url(); ?> " rel="trackback">trackback</a> depuis votre propre site.

						<?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Comments are open, Pings are not ?>
							Vous pouvez aller directement &agrave; la fin et laisser un commentaire. Envoyer un ping n'est pas actuellement autoris&agrave;.

						<?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
							// Neither Comments, nor Pings are open ?>
							Les commentaires et les pings sont actuellement ferm&eacute;s.

						<?php } edit_post_link('Modifier cette entr&eacute;e','','.'); ?>

					</small>
				</p>


<div class="nav prev left"><?php next_post_link('%link', '&larr;', TRUE); ?></div>
<div class="nav next right"><?php previous_post_link('%link', '&rarr;', TRUE); ?></div>
<div class="clear"></div>
			<?php endwhile; else : ?>

				<h2 class="center">Not Found</h2>
				<p class="center">D&eacute;sol&eacute;, mais vous cherchez quelque chose qui n'est pas ici.</p>
				<?php include (TEMPLATEPATH . "/searchform.php"); ?>

			<?php endif; ?>
<?php comments_template(); ?>
</div>
</div>

<?php
		$sidebar_state = get_option('T_sidebar_state');

		if($sidebar_state == "On") {
			get_sidebar() ;
		}
		else {
			echo "";
		}
		?>

<!-- Begin Footer -->
<?php get_footer(); ?>