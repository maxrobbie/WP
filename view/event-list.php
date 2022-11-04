<?php get_header(); ?>
<main class="has-global-padding is-layout-constrained wp-block-group" style="margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--70)" id="wp--skip-link--target">
<div class="wp-block-query alignwide">
<?php if ( have_posts() ) : ?>
	<ul class="event_list">
	<?php while ( have_posts() ) : the_post(); 
		$eTime = get_post_meta(get_the_ID(),"_timestamp",true);
		$eOrganizer = get_post_meta(get_the_ID(),"_organizer",true);
		$eEmail = get_post_meta(get_the_ID(),"_email",true);
		$eAddress = get_post_meta(get_the_ID(),"_address",true);
		$eLatitude = get_post_meta(get_the_ID(),"_latitude",true);
		$eLongitude = get_post_meta(get_the_ID(),"_longitude",true);
		$eTags = get_the_terms(get_the_ID(), 'event-tag');
		if(count($eTags)>0){
			$t = array();
			foreach($eTags as $tg){
				$t[] = $tg->name;
			}
			$show = join(", ",$t);	
		} 
	?>
		<li class="event_data" id="event-<?php echo get_the_ID(); ?>">
			<h4><?php echo get_the_title(get_the_ID()); ?></h4>
			<?php the_content(); ?>
			<span><b><?php _e('Time','wp-event-plugin'); ?> : </b><?php echo eventago($eTime); ?></span><br
			<span><b><?php _e('Organizer','wp-event-plugin'); ?> : </b><?php echo $eOrganizer; ?></span>
			<span><b><?php _e('Email','wp-event-plugin'); ?> : </b><?php echo $eEmail; ?></span>
			<span><b><?php _e('Address','wp-event-plugin'); ?> : </b><?php echo $eAddress; ?></span>
			<span><b><?php _e('Latitude','wp-event-plugin'); ?> : </b><?php echo $eLatitude; ?></span>
			<span><b><?php _e('Longitude','wp-event-plugin'); ?> : </b><?php echo $eLongitude; ?></span>
			<span><b><?php _e('Tags','wp-event-plugin'); ?> : </b><?php echo $show; ?></span>
		</li>
	<?php endwhile; ?>
<?php else :  echo "No events available";  endif; ?>
<div class="paginations">
                  <?php  the_posts_pagination( array(
                     'prev_text'          => __( '<'),
                     'next_text'          => __( '>'),
                     'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Pagina', 'wp-event-plugin' ) . ' </span>',
                     ) ); ?>
               </div>
</div>
</main>
<?php get_footer(); ?>