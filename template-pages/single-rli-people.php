<?php
/**
 * The Template for displaying single rli-people posts. 
 *
 * Based on theme Twenty Twelve
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			
			<?php while ( have_posts() ) : the_post(); 
				if ( function_exists( 'rli_people_person' ) ){
					$person = rli_people_person();
				}
				?>
				
				<div class='vcard'>
					<div class='person-photo'>
						<?php 
						$size = 'post-thumbnail';
						the_post_thumbnail( $size, array( 'class' => "attachment-$size photo" ) ); ?>
					</div>
					<h2><span class='fn'><?php echo $person['name'] ?></span></h2>
					<p class='person-meta'><span class='person-title title'><?php echo $person['title']?></span></p>
					<p class='person-contact'><a href="mailto:<?php echo $person['email']; ?>" class='email'><?php echo $person['email'];?></a></p>
				</div>
				<div class='person-long-bio'>
					<?php echo $person['full_bio']; ?>
				</div>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
