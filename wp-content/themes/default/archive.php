<?php 
/** 
 * @package WordPress 
 * @subpackage Default_Theme 
 */ 

get_header(); 
?>

<div id="content" class="narrowcolumn" role="main">
	<?php if (have_posts()) : ?>		
		<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>		
		<?php /* If this is a category archive */ if (is_category()) { ?>
			<h2 class="pagetitle">Archive pour la catégorie &#8216;<?php single_cat_title(); ?>&#8217;</h2>
		<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?> 
			<h2 class="pagetitle">Archive pour le mot-clef &#8216;<?php single_tag_title(); ?>&#8217;</h2> 
		<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			<h2 class="pagetitle">Archive pour <?php the_time('j F Y'); ?></h2>
		<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<h2 class="pagetitle">Archive pour <?php the_time('F Y'); ?></h2>
		<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<h2 class="pagetitle">Archive pour <?php the_time('Y'); ?></h2>
		<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			<h2 class="pagetitle">Archive par auteur </h2>
		<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<h2 class="pagetitle">Archives du blog </h2>
		<?php } ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Articles plus anciens') ?></div>
			<div class="alignright"><?php previous_posts_link('Articles plus récents &raquo;') ?></div>
		</div>

		<?php while (have_posts()) : the_post(); ?>
		
			<div <?php post_class() ?>>
				<h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Lien permanent vers <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<small><?php the_time('l j F Y') ?></small>
				<div class="entry">
					<?php the_content('Lire le reste de cet article &raquo;'); ?>
				</div>
				<p class="postmetadata"><?php the_tags('Mots-clefs&nbsp;: ', ', ', '<br />'); ?>Publié dans <?php the_category(', ') ?> | <?php edit_post_link('Modifier', '', ' | '); ?>  <?php comments_popup_link('Aucun commentaire »', '1 commentaire »', '% commentaires »', 'comments-link', 'Les commentaires sont fermés'); ?></p>
			</div>
		
		<?php endwhile; ?>
		
		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Articles plus anciens') ?></div>
			<div class="alignright"><?php previous_posts_link('Articles plus récents &raquo;') ?></div>
		</div>
	<?php else :

		if ( is_category() ) { // If this is a category archive
			printf("<h2 class='center'>Désolé, mais il n'y a pas encore d'article dans la catégorie %s.</h2>", single_cat_title('',false));
		} else if ( is_date() ) { // If this is a date archive
			echo("<h2>Désolé, mais aucun article ne correspond à cette date.</h2>");
		} else if ( is_author() ) { // If this is a category archive
			$userdata = get_userdatabylogin(get_query_var('author_name'));
			printf("<h2 class='center'>Désolé, mais %s n'a pas encore écrit d'article.</h2>", $userdata->display_name);
		} else {
			echo("<h2 class='center'>Aucun article trouvé.</h2>");
		}
		get_search_form();

	endif;
?>

	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>