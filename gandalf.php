<?php
/*
Plugin Name: Gandalf - Vous ne passerez pas !
Description: Améliore la securité wordpress.
Version: 1.0
Author: Stephen DELETANG
Copyright 2014 Stephen DELETANG
*/

defined('ABSPATH') or die('Vous avez bien fait de venir. Vous entendrez aujourd\' hui tout ce qu\'il vous est n&eacute;cessaire de savoir pour comprendre les desseins de l\'ennemi.'); 

// Forcer l'adresse email admin 
add_filter( 'option_admin_email', '_gandalf_admin_email' ); 
function _gandalf_admin_email( $value ) { return 's.deletang@laposte.net';} 
	
// Forcer l'impossibilité de s'inscrire 
add_filter( 'pre_option_users_can_register', '_gandalf_users_can_register' ); 
function _gandalf_users_can_register( $value ) { return '0'; } 

// Forcer le rôle par défaut sur "Abonné" 
add_filter( 'pre_option_default_role', '_gandalf_default_role' ); 
function _gandalf_default_role( $value ) { return 'subscriber'; }

// Enlever notification pour tout le monde sauf adminisrateur
add_action('admin_notices','_gandalf_update_notification_nonadmins',1);
function _gandalf_update_notification_nonadmins() {
	if (!current_user_can('administrator')) 
	remove_action('admin_notices','update_nag',3);
}

// Désactive self-trackbacking
add_action('pre_ping','_gandalf_disable_self_pings');
function _gandalf_disable_self_pings( &$links ) {
	foreach ( $links as $l => $link )
		if ( 0 === strpos( $link, home_url() ) )
			unset($links[$l]);
}
	
// Empêcher les accents dans les URLs lors de l'upload d'un média ainsi eviter erreur 404
add_filter('sanitize_file_name', 'remove_accents' );

// Retire numéro version du flux rss
add_filter('the_generator', '_gandalf_delete_rss_version');
function _gandalf_delete_rss_version() { return ''; }
	
// On réoriente les flux RSS de WordPress */
add_action('do_feed', '_gandalf_disable_all_feeds', 1);
add_action('do_feed_rdf', '_gandalf_disable_all_feeds', 1);
add_action('do_feed_rss', '_gandalf_disable_all_feeds', 1);
add_action('do_feed_rss2', '_gandalf_disable_all_feeds', 1);
add_action('do_feed_atom', '_gandalf_disable_all_feeds', 1);
function _gandalf_disable_all_feeds() { wp_redirect('/');} // -> On renvoi vers la page d'acceuil

/* Localisation */
add_action( 'init', '_gandalf_remove_l1on' ); //remove the l10n.js script http://eligrey.com/blog/post/passive-localization-in-javascript
function _gandalf_remove_l1on() {
	if ( !is_admin() ) {
		wp_deregister_script('l10n');
	}
}
	 
 // Autoriser shortcode sur widget
if ( !is_admin() ) { add_filter ('widget_text','do_shortcode'); }
	 
// Bloquer usurpation d'identité
add_filter('preprocess_comment', '_gandalf_preprocess_comment' );
function _gandalf_preprocess_comment( $commentdata ) {
    	if( is_user_logged_in() || $commentdata['comment_type']!='' )
			return $commentdata; 
			
    	$user = '';
    	
    	if( $commentdata['comment_author']!='' ):	
        	$user = get_user_by( 'slug', $commentdata['comment_author'] );
        	$info = 'ce pseudo';
    	endif;
    	
    	if( !$user && $commentdata['comment_author_email']!='' ):
        	$user = get_user_by( 'email', $commentdata['comment_author_email'] );
        	$info = 'cette adresse email';
    	endif;
    	
    	if( $user )
			wp_die( '<p>Impossible de continuer car ' . $info . ' correspond &agrave; un membre sur ce site. </p><p>S\'il s\'agit de vous, merci de vous connecter</a>.</p><p align="right"> <a href="' . esc_url( wp_get_referer() ) . '">Retour &raquo;</a></p>' );
    		
    	return $commentdata;
	}

// Enlever message d'erreur sur login pour securitée
add_filter('login_errors',create_function('$a', "return null;")); 
	 	
// On nettoie le head	
remove_action('wp_head', 'wp_generator'); // Effacer la version de wordpress dans le head
remove_action('wp_head', 'feed_links', 2); // Affiche les liens des flux RSS pour les Articles et les commentaires.
remove_action('wp_head', 'feed_links_extra', 3); // Affiche les liens des flux RSS supplémentaires comme les catégories de vos articles.
remove_action('wp_head', 'rsd_link'); // Affiche le lien RSD (Really Simple Discovery). Je ne l'ai jamais utilisé mais si vous êtes certain d'en avoir besoin, laissez-le.
remove_action('wp_head', 'wlwmanifest_link'); // Affiche le lien xml dont a besoin Windows Live Writer pour accéder à votre blog. 
remove_action( 'wp_head', 'index_rel_link' );// index link
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );// prev link
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );// start link
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );// Display relational links for the posts adjacent to the current post.
remove_action('wp_head','start_post_rel_link');
remove_action('wp_head','adjacent_posts_rel_link_wp_head'); // Affiche les liens relatifs vers les articles suivants et précédents.
remove_action('wp_head','wp_shortlink_wp_head'); // Affiche l'url raccourcie de la page ou vous vous situez.
remove_filter( 'the_content', 'capital_P_dangit' ); // Désactive l'effet que wordpress corrige les lettres capitales incorrect dans le contenu
remove_filter( 'the_title', 'capital_P_dangit' ); // Désactive l'effet que wordpress corrige les lettres capitales incorrect dans le titre
remove_filter( 'comment_text', 'capital_P_dangit' ); // Désactive l'effet que wordpress corrige les lettres capitales incorrect dans les commentaires

/* Limiter la durée de vie cookie des commentateurs ( option cache)  72h*/
add_filter('comment_cookie_lifetime', 'my_comment_cookie_lifetime');
function my_comment_cookie_lifetime($lifetime) { return 259200; } 

?>
