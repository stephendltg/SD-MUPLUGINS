<?php
/*
Plugin Name: Aragorn - Rodeur SOE
Description: Améliore le référencement et le partage sur les réseaux sociaux..
Version: 1.0
Author: Stephen DELETANG
Copyright 2014 Stephen DELETANG
*/

defined('ABSPATH') or die('Vous avez bien fait de venir. Vous entendrez aujourd\' hui tout ce qu\'il vous est n&eacute;cessaire de savoir pour comprendre les desseins de l\'ennemi.'); 
	


/*** Ajouter extrait au post de type page , utilisé pour meta description----------------------------------------*/
add_action( 'admin_init', create_function('', "return add_post_type_support( 'page', 'excerpt' );") );


/*** Ajouter mot clé sur page et produits , utilisé pour meta keyword----------------------------------------*/
add_action('init', 'tags_support_all');
function tags_support_all() {  
	register_taxonomy_for_object_type('post_tag', 'page');
    register_taxonomy_for_object_type('post_tag', 'wpshop_product');
} 
add_action('pre_get_posts', 'tags_support_query'); // assurer tout les mots clés dans la base de donnée
function tags_support_query($wp_query) {
	if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
}


/*** Ajouter code après chargement du thème----------------------------------------*/
 add_action('after_setup_theme','_aragorn_say');
 function _aragorn_say() {
		
	/* Ajouter Code google analytics */
	// add_action('wp_head', 'async_google_analytics');
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
		
		
	/* Ajouter balise meta description et meta keyword */
	add_action('wp_head', 'meta_data');
	function meta_data(){
		$blog_description = get_bloginfo( 'description', 'display' );
		$excerpt=str_replace ('Lire la suite', '', get_the_excerpt( ) ) ;

		if ( $excerpt &&  !is_home() && !is_archive() ) {
			echo '<meta name="description" content="' . wp_strip_all_tags( $excerpt ) . '" />'. "\n" ;
		}
		elseif ( is_archive() ) { // On utilise la description de la categorie pour la meta description.
			echo '<meta name="description" content="' . wp_strip_all_tags( category_description() ). '" />'. "\n";
		}
		else {
			echo '<meta name="description" content="' . $blog_description . '" />'. "\n" ;
		}
		if ( get_the_tags() && ( is_page() || is_single() ) ) { // On ajoute les mots clés des articles, pages
			 	$keywords ='';
		 		foreach (get_the_tags() as $tag) { $keywords.=",".$tag->name; }
				echo '<meta name="keywords" content="'.substr ($keywords,1).'" />'. "\n" ;
		 }

		
	}


	/* Ajouter Opengraph pour améliorer lisibilité reseaux sociaux*/
	 add_action( 'wp_head', 'wptuts_opengraph' );
	function wptuts_opengraph() { 
		/*
		if (is_archive() && $wp_query->queried_object->name ) {
			$output = '<meta property="og:locale" content="fr_FR" />' . "\n"; 
			$output .= '<meta property="og:type" content="page" />' . "\n";  
			$output .= '<meta property="og:title" content="' . wp_strip_all_tags( $wp_query->queried_object->name ). '" />' . "\n"; 
			$output .= '<meta property="og:description" content="'. wp_strip_all_tags( $wp_query->queried_object->description ). '" />' . "\n";
			echo $output;
		}
		*/
		
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

?>
