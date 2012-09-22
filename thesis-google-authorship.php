<?php
/*
Plugin Name: Google Authorship for Thesis
Plugin URI: http://andrewsfreeman.com
Description: Adds Google authorship support for Thesis.
Version: 1.0
Author: Andrew S Freeman
Author URI: http://andrewsfreeman.com
License: GPL2
*/

/* 
* References and Credits: 
* http://designpx.com/tutorials/rel-author-rel-me/ 
* http://yoast.com/wordpress-rel-author-rel-me/
* 
* Setting up rel=author and rel=me entails three main things
* 
* 1) Link each blog post to the author archive with a rel="author"
* 2) On the author archive, a link to the G+ profile with rel="me"
* 3) Adding a custom link on the Google+ profile
* 
*/

/* First, we add the Google+ link to the author archive pages. The G+ profile is set in the WordPress user profile. */
function asfrl_add_google_profile( $contactmethods ) {
	$contactmethods['google_profile'] = 'Google Profile URL';
	return $contactmethods;
}
add_filter( 'user_contactmethods', 'asfrl_add_google_profile', 10, 1 );

function asfrl_archive_intro_headline( $output ) {
	global $wp_query;
	if ( is_author() ) {
		$author = $wp_query->query_vars['author'];
		$author_name = get_author_name( $author );
		$profile_url = get_the_author_meta( 'google_profile', $author );
		$output = str_replace( $author_name, '<a rel="me" href="' . $profile_url . '">' . $author_name . '</a>',  $output );
	}
	return $output;
}
add_filter( 'thesis_archive_intro_headline', 'asfrl_archive_intro_headline' );

/**
 * Next, we link the author byline for each post the the author's archive with a rel="me"
 * We use output buffering because there's no real filter for this.
 */
function asfrl_headline_catcher() {
	ob_start();
}

function asfrl_headline_catcher_end() {
	global $thesis_design;
	if ( $thesis_design->display['byline']['author']['nofollow'] )
		$output = str_replace( 'rel="nofollow"', 'rel="nofollow author"', ob_get_contents() );
	else
		$output = str_replace( 'class="url fn"', 'class="url fn" rel="author"', ob_get_contents() );
	ob_end_clean();
	echo $output;
}
add_action( 'thesis_hook_post_box_top', 'asfrl_headline_catcher' );
add_action( 'thesis_hook_before_post' , 'asfrl_headline_catcher_end' );