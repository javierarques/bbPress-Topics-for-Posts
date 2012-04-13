<?php
/**
 * The template for displaying a comment topic when the user has selected one.
 *
 */
?>
<div id="comments">
	
	<?php do_action( 'bbp_template_before_single_topic' ); ?>
	<?php 
		global $bbp;
		
		// Set passed attribute to $forum_id for clarity
		$topic_id = $bbp->topic_query->post->ID;
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		// Setup the meta_query
		$replies_query['meta_query'] = array( array(
			'key'     => '_bbp_topic_id',
			'value'   => $topic_id,
			'compare' => '='
		) );

		// Reset the queries if not in theme compat
		if ( !bbp_is_theme_compat_active() ) {

			// Reset necessary forum_query attributes for topics loop to function
			$bbp->forum_query->query_vars['post_type'] = bbp_get_forum_post_type();
			$bbp->forum_query->in_the_loop             = true;
			$bbp->forum_query->post                    = get_post( $forum_id );

			// Reset necessary topic_query attributes for topics loop to function
			$bbp->topic_query->query_vars['post_type'] = bbp_get_topic_post_type();
			$bbp->topic_query->in_the_loop             = true;
			$bbp->topic_query->post                    = get_post( $topic_id );
		}


		// Check forum caps
		if ( bbp_user_can_view_forum( array( 'forum_id' => $forum_id ) ) ) {

			// Before single topic
			do_action( 'bbp_template_before_single_topic' );

			// Password protected
			if ( post_password_required() ) {

				// Output the password form
				bbp_get_template_part( 'bbpress/form', 'protected' );

			// Not password protected, or password is already approved
			} else {

				// Tags
				bbp_topic_tag_list( $topic_id );

				// Topic description
				bbp_single_topic_description( array( 'topic_id' => $topic_id ) );

				// Template files
				if ( bbp_show_lead_topic() )
					bbp_get_template_part( 'bbpress/content', 'single-topic-lead' );

				// Load the topic
				if ( bbp_has_replies( $replies_query ) ) {
					bbp_get_template_part( 'bbpress/pagination', 'replies' );
					bbp_get_template_part( 'bbpress/loop',       'replies' );
					bbp_get_template_part( 'bbpress/pagination', 'replies' );
				}

				// Reply form
				bbp_get_template_part( 'bbpress/form', 'reply' );
			}

			// After single topic
			do_action( 'bbp_template_after_single_topic' );

		// Forum is private and user does not have caps
		} elseif ( bbp_is_forum_private( $forum_id, false ) ) {
			bbp_get_template_part( 'bbpress/feedback', 'no-access' );
		}

		
	?>
	<?php do_action( 'bbp_template_after_single_topic' ); ?>

</div><!-- #comments -->
