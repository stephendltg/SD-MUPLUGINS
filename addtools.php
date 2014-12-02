<?php
/*
Plugin Name: Overload css et google analytic liŽe au thme
Description: Surcharge css et ajout de google analytic
Version: 1.0
Author: Stephen DELETANG
Copyright 2014 Stephen DELETANG

*/
add_action('wp', 'delete_cache_scheduled');
function delete_cache_scheduled() {
	if ( !wp_next_scheduled( 'delete_cache_daily_event' ) ) {
		wp_schedule_event( time(), 'daily', 'delete_cache_daily_event' );
	}
}

add_action( 'delete_cache_daily_event', 'do_delete_cache' );

function do_delete_cache() {
	if( function_exists('wpmc_clean_home_cache'))  {
		wpmc_clean_home_cache() ;
		$url_page = home_url().'/boutique/inscription/';
		wpmc_clean_cache_file($url_page);
		
		global $wpdb;
		$link_category = $wpdb->get_results( 
				"
					SELECT slug
					FROM $wpdb->terms
				"
			);
		foreach ( $link_category as $link_category ) { 
			$url_category = home_url().'/boutique/'.$link_category->slug.'/' ;
			wpmc_clean_cache_file ( $url_category );
		}
	
	}
}


 add_action('after_setup_theme','wpce_setup');
 if( !function_exists('wpce_setup'))  {
 function wpce_setup() {
	
	/*** calling clean up fonctions, comment to disable a function----------------------------------------*/ 

   	/* afficher toutes les options wordpress eviter d'utiliser php admin pour modifier table*/
  	//add_action('admin_menu', 'add_all_general_settings_link');    
		
	/* Code google analytics */
	add_action('wp_head', 'async_google_analytics');
	
	/* AJouter opengraph pour amŽliorer lisibilitŽ reseaux sociaux*/
	add_action( 'wp_head', 'wptuts_opengraph' );
	
	/* admin part cleanups */
	add_action('admin_enqueue_scripts', 'css_admin'); // Surcharge css sur admin

 	/* Modifier page wp-login */
 	add_action('login_head', 'css_login'); // Surcharge CSS wp-login
	 
	 /* Nettoyage differents style css de wpshop*/
	 add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
	 
	 /* Supprimer menu plugins et sub menu*/
	//add_action('admin_menu', 'delete_menu_plugins');
	//add_action('wp_before_admin_bar_render', 'remove_plugins_admin_bar' );
	
	 /* Forcer l'impossibilitŽ de dŽsactiver un plugins*/
	add_filter( 'plugin_action_links_wpshop/wpshop.php', 'disable_plugin_link_delete' );
	add_filter( 'plugin_action_links_backwpup/backwpup.php', 'disable_plugin_link_delete' );
	add_filter( 'plugin_action_links_simple-image-widget/simple-image-widget.php', 'disable_plugin_link_delete' );
	add_filter( 'plugin_action_links_wp-smushit/wp-smushit.php', 'disable_plugin_link_delete' );
	add_filter( 'plugin_action_links_google-sitemap-generator/sitemap.php', 'disable_plugin_link_delete' );
	
	/* Ajouter extrait aux page */
	add_action( 'admin_init', create_function('', "return add_post_type_support( 'page', 'excerpt' );") );
	
	/* Ajouter mot clŽ sur page et produits */
	add_action('init', 'tags_support_all');
	add_action('pre_get_posts', 'tags_support_query');
	
	/* Limiter la durŽe de vie cookie des commentateurs ( option cache)  72h*/
	add_filter('comment_cookie_lifetime', 'my_comment_cookie_lifetime');
	function my_comment_cookie_lifetime($lifetime) { return 259200; } 
	
	/* supprimer le mot inscription sur page login */
	function enleve_mdp_textfr ($text) {if ($text == 'Inscription') {$text = '';} return $text;}
	function enleve_mdp_fr() {add_filter('gettext','enleve_mdp_textfr');}
	add_action ('login_head','enleve_mdp_fr');
	
	// La fonctionnalitŽ de renouvellement de mot de passe ne fonctionnera pas
	// function supprimer_rest_mdp() {return false;}
	// add_filter ('allow_password_reset','supprimer_rest_mdp');

	
	/**---------------------------------------------------------------------------------------------------*/	
	}
}



 /* Here come my different fonctions 
	* 
	* 
	* */
	
/*** Insertion code analytics en mode asynchrone- ----------------------------------------*/
if( !function_exists('async_google_analytics'))  {
	function async_google_analytics() { ?>
		<script>
		var _gaq = [['_setAccount', 'UA-38243504-1'], ['_trackPageview']];
			(function(d, t) {
				var g = d.createElement(t),
					s = d.getElementsByTagName(t)[0];
				g.async = true;
				g.src = ('https:' == location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				s.parentNode.insertBefore(g, s);
			})(document, 'script');
		</script>
	<?php }
}

/*** Insertion code opengraph- ----------------------------------------*/
if( !function_exists('wptuts_opengraph'))  {
	function wptuts_opengraph() { 
		if ( is_singular() ) { 
			global $post; 
			setup_postdata( $post );
			$description=str_replace ('Lire la suite', '', get_the_excerpt( ) ) ;
			$output = '<meta property="og:locale" content="fr_FR" />' . "\n"; 
			if (is_single() ) { 
				$output .= '<meta property="og:type" content="article" />' . "\n";
				$output .= '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />' . "\n"; 
				$output .= '<meta property="og:description" content="' . wp_strip_all_tags( $description ) . '" />' . "\n"; 
			 }
			if (is_home() ) { 
				$output .= '<meta property="og:type" content="website" />' . "\n";
				$output .= '<meta property="og:title" content="' . get_bloginfo('name') . '" />' . "\n"; 
				$output .= '<meta property="og:description" content="' . wp_strip_all_tags( get_bloginfo( 'description', 'display' ) ) . '" />' . "\n"; 
  			}
 			if (is_front_page() ) { 
				$output .= '<meta property="og:type" content="website" />' . "\n";
				$output .= '<meta property="og:title" content="' . get_bloginfo('name') . '" />' . "\n"; 
				$output .= '<meta property="og:description" content="' . wp_strip_all_tags( $description ) . '" />' . "\n"; 
  			} 			
  			if (is_page() && !(is_front_page() ||is_home() ) ) { 
				$output .= '<meta property="og:type" content="page" />' . "\n";  
				$output .= '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />' . "\n"; 
				$output .= '<meta property="og:description" content="' . wp_strip_all_tags( $description ) . '" />' . "\n"; 
			}
			$output .= '<meta property="og:url" content="' . get_permalink() . '" />' . "\n"; 
			$output .= '<meta property="og:site_name" content="'.get_bloginfo('name').'" />'."\n"; 
			if (is_single() ) { 
				$output .= '<meta property="article:published_time" content="' . get_the_time(c) . '" />' . "\n";
				$output .= '<meta property="article:modified_time" content="' . get_the_modified_time(c) . '" />' . "\n";
				$output .= '<meta property="article:author" content="' . get_the_author() . '" />' . "\n";
				//$output .= '<meta property="article:section" content="' . get_the_category() . '" />' . "\n";
				//$output .= '<meta property="article:tag" content="' . get_the_tags() . '" />' . "\n";
				$output .= '<meta property="og:updated_time" content="'. get_the_modified_time(c) .'" />'."\n"; 
				$output .= '<meta name="twitter:card" content="product" />'."\n"; 
				$output .= '<meta name="twitter:site" content="@'. get_bloginfo('name') .'">'."\n"; 
				$output .= '<meta name="twitter:creator" content="@'. get_the_author() .'" />'."\n"; 
				$output .= '<meta name="twitter:title" content="'. esc_attr( get_the_title() ) .'" />'."\n"; 
				$output .= '<meta name="twitter:description" content="'. wp_strip_all_tags( $description ).'" />'."\n"; 
				$price = do_shortcode('[wpshop_att_val type="decimal" attid="6" pid="'. get_the_ID() .'"]'); 
				$output .= '<meta name="twitter:data1" content="'. $price .'" />'."\n"; 
				$output .= '<meta name="twitter:label1" content="Price" />'."\n"; 
			 }
			if ( has_post_thumbnail() ) { 
				$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' ); 
				$output .= '<meta property="og:image" content="' . $imgsrc[0] . '" />' . "\n"; 
				$output .= '<meta name="twitter:image" content="' . $imgsrc[0] . '" />' . "\n"; 
			} 
			echo $output; 
		} 
	}
}

/*** Surcharge CSS Admin- ----------------------------------------*/
if( !function_exists('css_admin'))  {
	function css_admin() {
		wp_enqueue_style( 'admin', get_bloginfo('template_directory') . '/css/custom_admin.css');
	}
}


/** Custom admin login header css	------------------------------------*/
if( !function_exists('css_login'))  {
	function css_login() {
		echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory') . '/css/custom_login.css"/>';
	}
}

/** Nettoyage Style CSS WP shop -----------------------------------------*/
if( !function_exists('my_deregister_styles'))  {
	function my_deregister_styles() {
		wp_deregister_style( 'wpshop_default_frontend_main_css' );
		wp_deregister_style( 'wpshop_default_wps_style_css' );
		wp_deregister_style( 'wpshop_frontend_main_css' );
		wp_deregister_style( 'wpshop_dialog_box_css');
		//wp_deregister_style( 'wpshop_jquery_fancybox');
		wp_deregister_style( 'wpshop_jquery_ui');
		wp_deregister_style( 'wpshop_jquery_ui_menu');
		wp_deregister_style( 'wpshop_jquery_ui_menu_2');
		wp_deregister_style( 'wpshop_jquery_jqzoom_css');
		wp_deregister_style( 'wps_shipping_mode_css');
		wp_deregister_style( 'wpshop_breadcrumb_css');
		wp_deregister_style( 'wpshop_filter_search_css');		
		wp_deregister_style( 'wpshop_filter_search_chosen_css');		
		if (!current_user_can('edit_post')) {wp_deregister_style( 'dashicons');}
	}
}

/** Supprimer menu plugins	------------------------------------*/
if( !function_exists('delete_menu_plugins'))  {
	function delete_menu_plugins() {
		remove_menu_page('backwpup');// Pages backwpup
	}
}

/** Supprimer menu plugins admin bar	------------------------------------*/
if( !function_exists('remove_plugins_admin_bar'))  {
	function remove_plugins_admin_bar() {
		$wp_admin_bar->remove_menu('backwpup'); // bouton backwpup plugins
	}
}

/** plugins pour lesquels les liens seront supprimŽs -----------------*/
					   			   
function disable_plugin_link_delete( $actions )
{
    // Enleve les lien 'Modifier' et 'Supprimer" des plugins "prŽservŽs"
	foreach( array( 'edit', 'deactivate' ) as $act )
		if( isset( $actions[$act] ) )
			unset( $actions[$act] );
 
    return $actions;
}

/** Affichage table SQL option wordpress -----------------*/
if( !function_exists('add_all_general_settings_link'))  {
	function add_all_general_settings_link() {  
      	add_options_page(__('All Settings'), __('All Settings'), 'administrator', 'options.php');  
   	}  
}

/** Ajouter mot clŽs sur page et produits -----------------*/

if( !function_exists('tags_support_all'))  {
	function tags_support_all() {  
      	register_taxonomy_for_object_type('post_tag', 'page');
      	register_taxonomy_for_object_type('post_tag', 'wpshop_product');
   	}  
}
// assurer tout les mots clŽs dans la base de donnŽe
if( !function_exists('tags_support_query'))  {
	function tags_support_query($wp_query) {
		if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
	}
}
/*----------------------------------------------------------------------- **/
?>
