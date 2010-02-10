<?php 
/** 
 * @package WordPress 
 * @subpackage Default_Theme 
 */ 

get_header(); ?>

<div id="content" class="narrowcolumn" role="main">
	<?php if (have_posts()) : ?>
	
		<?php while (have_posts()) : the_post(); ?>
		
			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Lien permanent vers <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<small><?php the_time('j F Y') ?> <!-- par <?php the_author() ?> --></small>
				<div class="entry">
					<?php the_content('Lire le reste de cet article &raquo;'); ?>
				</div>
				<p class="postmetadata"><?php the_tags('Mots-clefs&nbsp;: ', ', ', '<br />'); ?> Publié dans <?php the_category(', ') ?> | <?php edit_post_link('Modifier', '', ' | '); ?>  <?php comments_popup_link('Aucun commentaire »', '1 commentaire »', '% commentaires »', 'comments-link', 'Les commentaires sont fermés'); ?></p>
			</div>
			
		<?php endwhile; ?>
		
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Articles plus anciens') ?></div>
			<div class="alignright"><?php previous_posts_link('Articles plus récents &raquo;') ?></div>
		</div>
		
	<?php else : ?>
	
		<h2 class="center">Introuvable</h2>
		<p class="center">Désolé, mais vous cherchez quelque chose qui ne se trouve pas ici.</p>
		<?php get_search_form(); ?>
		
	<?php endif; ?>
</div>
	
<?php get_sidebar(); ?>
<?php get_footer(); ?>