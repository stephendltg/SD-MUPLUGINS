<?php
/*
Plugin Name: Eleanor - Noblesse
Description: Adapte votre site à votre utilisation.
Version: 1.0
Author: Stephen DELETANG
Copyright 2014 Stephen DELETANG
*/

defined('ABSPATH') or die('Vous avez bien fait de venir. Vous entendrez aujourd\' hui tout ce qu\'il vous est n&eacute;cessaire de savoir pour comprendre les desseins de l\'ennemi.'); 
	

/*** Parametrage des comptes utilisateurs----------------------------------------*/
add_filter('user_contactmethods','modify_user_contact_methods',10,1);	// add facebook and twitter account to user profil
function modify_user_contact_methods($user_contact) {
	// $user_contact['skype'] = __('Skype'); 
	// $user_contact['twitter'] = __('Twitter');
	// $user_contact['facebook'] = __('Facebook');
	unset($user_contact['yim']);
	unset($user_contact['jabber']);
	unset($user_contact['aim']);
	return $user_contact;
}


/*** Supprimer champ personnalisé sur post de type page et article ----------------------------------------*/
function baw_remove_custom_field_meta_boxes() {
    remove_post_type_support( 'post','custom-fields' );
    remove_post_type_support( 'page','custom-fields' );
}
add_action('init','baw_remove_custom_field_meta_boxes');


/*** Modifier liens page wp-login ----------------------------------------*/
add_filter( 'login_headerurl', 'custom_login_url' );// Make admin link point to the home of the site
function custom_login_url() {
	return home_url( '/' );
}
add_filter( 'login_headertitle', 'custom_login_title' );// Change alt title of admin logo to use blog name
function custom_login_title() {
	return get_option( 'blogname' );
}

/*** Affiche options de la BDD ( evites d'utiliser PHP Admin , mais attention !) ----------------------------------------*/
// add_action('admin_menu', 'add_all_general_settings_link'); 
function add_all_general_settings_link() {  
    add_options_page(__('All Settings'), __('All Settings'), 'administrator', 'options.php');  
}  


/*** NETTOYAGE INTERFACE ADMIN ----------------------------------------*/
		
// add_action('admin_menu','remove_dashboard_widgets');// nettoyer dashboard widgets	
// add_action('admin_menu', 'delete_menu_items');// deleting menu items from admin area
// add_action( 'admin_menu', 'delete_submenu_page', 999 ); // deleting submenu page from admin aera
// add_filter('manage_posts_columns', 'custom_post_columns');// remove column entries from list of posts
// add_filter('manage_pages_columns', 'custom_pages_columns');// remove column entries from list of page
// add_action('wp_before_admin_bar_render', 'remove_item_admin_bar' );// Nettoyer the admin bar
// add_action('widgets_init', 'unregister_default_widgets', 11);// remove widgets from the widget page
// add_filter( 'contextual_help', 'remove_help_tabs', 999, 3 ); // Enlever les options d'écrans d'aide
// add_filter('screen_options_show_screen', 'remove_screen_options', 10, 2); // // Enlever les options d'écrans "option d'écran"
// add_action('wp_dashboard_setup', 'add_dashboard_widgets' ); // Ajouter un widget sur Dashboard
// add_action('admin_init','remove_dolly');// remove the hello dolly plugin

// add_filter('show_admin_bar', '__return_false'); // Cacher la barre d'outils a tous les utilisateurs meme admin
// add_action('init','_Eleanor_remove_toolsbar'); // Cacher la barre d'outils sauf administrateur
function _Eleanor_remove_toolsbar(){
	if (!current_user_can('administrator')) { add_filter('show_admin_bar', '__return_false'); }
}


/* LISTES DES APPELS FONCTIONS NETTOYAGE INTERFACE ADMIN */

	
/*** cleaning up the dashboard- ----------------------------------------*/
if( !function_exists('remove_dashboard_widgets'))  {
	function remove_dashboard_widgets(){
		remove_meta_box('dashboard_right_now','dashboard','core');// right now overview box
		remove_meta_box('dashboard_incoming_links','dashboard','core');// incoming links box
		remove_meta_box('dashboard_quick_press','dashboard','core');// quick press box
		remove_meta_box('dashboard_plugins','dashboard','core');// new plugins box
		remove_meta_box('dashboard_recent_drafts','dashboard','core');// recent drafts box
		remove_meta_box('dashboard_recent_comments','dashboard','core');// recent comments box
		remove_meta_box('dashboard_primary','dashboard','core');// wordpress development blog box
		remove_meta_box('dashboard_secondary','dashboard','core');// other wordpress news box
	}
}
/*----------------------------------------------------------------------*/


/* Remove some menus froms the admin area*/
if( !function_exists('delete_menu_items'))  {
	function delete_menu_items() {
	
	/*** Remove menu http://codex.wordpress.org/Function_Reference/remove_menu_page 
	syntaxe : remove_menu_page( $menu_slug )	**/
	remove_menu_page('index.php');// Dashboard
	remove_menu_page('edit.php');// Posts
	remove_menu_page('upload.php');// Media
	remove_menu_page('link-manager.php');// Links
	remove_menu_page('edit.php?post_type=page');// Pages
	remove_menu_page('edit-comments.php');// Comments
	remove_menu_page('themes.php');// Appearance
	remove_menu_page('plugins.php');// Plugins
	remove_menu_page('users.php');// Users
	remove_menu_page('tools.php');// Tools
	remove_menu_page('options-general.php');// Settings
	}
}

if( !function_exists('delete_submenu_page'))  {
function delete_submenu_page() {
	remove_submenu_page( 'edit.php', 'edit.php' ); //Menu Tous les articles
	remove_submenu_page( 'edit.php', 'post-new.php' ); //Menu Ajouter article
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' ); //Menu Catégorie
	remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' ); //Menu Mots-clefs
	remove_submenu_page( 'upload.php', 'media-new.php' ); //Menu Ajouter media
	remove_submenu_page( 'upload.php', 'upload.php' ); //Menu bibliotheque
	remove_submenu_page( 'edit.php?post_type=page', 'edit.php?post_type=page' ); //Menu Toutes les pages
	remove_submenu_page( 'edit.php?post_type=page', 'post-new.php?post_type=page' ); //Menu Ajouter une page
	remove_submenu_page( 'themes.php', 'themes.php' ); //Menu Themes (choisir)
	remove_submenu_page( 'themes.php', 'customize.php' ); //Menu Personnaliser theme
	remove_submenu_page( 'themes.php', 'widgets.php' ); //Menu Gestiond des widgets
	remove_submenu_page( 'themes.php', 'nav-menus.php' ); //Menu Gestion des menus
	remove_submenu_page( 'themes.php', 'theme-editor.php' ); //Menu Edition de theme
	remove_submenu_page( 'plugins.php', 'plugins.php' ); //Menu Extensions installées
	remove_submenu_page( 'plugins.php', 'plugin-install.php' ); //Menu Installer plugin
	remove_submenu_page( 'plugins.php', 'plugin-editor.php' ); //Menu Edition de plugin
	remove_submenu_page( 'users.php', 'users.php' ); //Menu Tous les utilisateurs
	remove_submenu_page( 'users.php', 'user-new.php' ); //Menu Ajouter un utilisateur
	remove_submenu_page( 'users.php', 'profile.php' ); //Menu Votre profil
	remove_submenu_page( 'tools.php', 'tools.php' ); //Menu Outils disponniles
	remove_submenu_page( 'tools.php', 'import.php' ); //Menu Outils importer
	remove_submenu_page( 'tools.php', 'export.php' ); //Menu Outils exporter
	remove_submenu_page( 'options-general.php', 'options-general.php' ); //Menu Reglages general
	remove_submenu_page( 'options-general.php', 'options-writing.php' ); //Menu Reglages ecriture
	remove_submenu_page( 'options-general.php', 'options-reading.php' ); //Menu Reglages lecture
	remove_submenu_page( 'options-general.php', 'options-discussion.php' ); //Menu Reglages discussion
	remove_submenu_page( 'options-general.php', 'options-media.php' ); //Menu Reglages medias
	remove_submenu_page( 'options-general.php', 'options-permalink.php' ); //Menu Reglages permaliens
	}
}

/*----------------------------------------------------------------------*/

/** removing parts from column ------------------------------------------*/
/* use the column id, if you need to hide more of them
syntaxe : unset($defaults['columnID']);	*/

/** remove column entries from posts **/
if( !function_exists('custom_post_columns'))  {
	function custom_post_columns($defaults) {
		unset($defaults['comments']);// comments 
		unset($defaults['author']);// authors
		unset($defaults['tags']);// tag 
		//unset($defaults['date']);// date
		//unset($defaults['categories']);// categories	
		return $defaults;
	}
}

/** remove column entries from pages **/
if( !function_exists('custom_pages_columns'))  {
	function custom_pages_columns($defaults) {
		unset($defaults['comments']);// comments 
		unset($defaults['author']);// authors
		unset($defaults['date']);	// date 
		return $defaults;
	}
}
/*-----------------------------------------------------------------------**/


/** remove widgets from the widget page ------------------------------------*/
/* Credits : http://wpmu.org/how-to-remove-default-wordpress-widgets-and-clean-up-your-widgets-page/ 
uncomment what you want to remove	*/
if( !function_exists('unregister_default_widgets'))  {
	 function unregister_default_widgets() {
		unregister_widget('WP_Widget_Pages');
		unregister_widget('WP_Widget_Calendar');
		unregister_widget('WP_Widget_Archives');
		unregister_widget('WP_Widget_Links');
		unregister_widget('WP_Widget_Meta');
		unregister_widget('WP_Widget_Search');
		unregister_widget('WP_Widget_Text');
		unregister_widget('WP_Widget_Categories');
		unregister_widget('WP_Widget_Recent_Posts');
		unregister_widget('WP_Widget_Recent_Comments');
		unregister_widget('WP_Widget_RSS');
		unregister_widget('WP_Widget_Tag_Cloud');
		unregister_widget('WP_Nav_Menu_Widget');
		unregister_widget('Twenty_Eleven_Ephemera_Widget');
	 }
}

/****** removings items froms admin bars 
use the last part of the ID after "wp-admin-bar-" to add some menu to the list	exemple for comments : id="wp-admin-bar-comments" so the id to use is "comments"	***********/
if( !function_exists('remove_item_admin_bar'))  {
	function remove_item_admin_bar() {
	global $wp_admin_bar;
		$wp_admin_bar->remove_menu('comments'); //remove comments
		$wp_admin_bar->remove_menu('wp-logo'); //remove the whole wordpress logo, help etc part
    	$wp_admin_bar->remove_menu('about'); // A propos de WordPress
    	$wp_admin_bar->remove_menu('wporg'); // WordPress.org
    	$wp_admin_bar->remove_menu('documentation'); // Documentation
    	$wp_admin_bar->remove_menu('support-forums');  // Forum de support
    	$wp_admin_bar->remove_menu('feedback'); // Remarque
   		$wp_admin_bar->remove_menu('site-name'); // Nom du site
    	$wp_admin_bar->remove_menu('updates'); // Icone mise à jour
		$wp_admin_bar->remove_menu('new-content'); // bouton créer		
	}
}

/*-----------------------------------------------------------------------**/

/** WordPress user profil cleanups	------------------------------------*/
	
/* remove the color scheme options */
if( !function_exists('admin_color_scheme'))  {
		function admin_color_scheme() {
		global $_wp_admin_css_colors;
		$_wp_admin_css_colors = 0;
	}
}

add_action('admin_head', 'admin_color_scheme');


/*----------------------------------------------------------------------- **/



/** Enlever les options d'écrans	------------------------------------*/
if( !function_exists('remove_help_tabs'))  {
	function remove_help_tabs($old_help, $screen_id, $screen){
    	$screen->remove_help_tabs();
    	return $old_help;
	}
}
if( !function_exists('remove_screen_options'))  {
	function remove_screen_options($display_boolean, $wp_screen_object){
  		$blacklist = array('post.php', 'post-new.php', 'index.php', 'edit.php','upload.php','edit-comments.php','nav-menus.php','edit-tags.php','users.php','admin.php');
  		if (in_array($GLOBALS['pagenow'], $blacklist)) {
    		$wp_screen_object->render_screen_layout();
    		$wp_screen_object->render_per_page_options();
    		return false;
  		} else {
    	return true;
  		}
	}
}

/*----------------------------------------------------------------------- **/


/** Ajouter un widget sur dashboard	------------------------------------*/
function dashboard_widget_function() {
		echo "
		<ul>
		<li>Date de realisation : Avril 2014</li>
		<li>Auteurs : Stéphen DELETANG</li>
		<li>Web developper : <a href='mailto:s.deletang@laposte.net'>Stephen DELETANG</a></li>
		</ul>
		";
}
if( !function_exists('add_dashboard_widgets'))  {
	function add_dashboard_widgets() {
		wp_add_dashboard_widget('wp_dashboard_widget', 'Informations techniques', 'dashboard_widget_function');
	}
}

/*----------------------------------------------------------------------- **/


?>
