<?php

// Page/Post Title

function ilovewp_helper_display_breadcrumbs() {

	// CONDITIONAL FOR "Breadcrumb NavXT" plugin OR Yoast SEO Breadcrumbs
	// https://wordpress.org/plugins/breadcrumb-navxt/

	if ( function_exists('bcn_display') ) { ?>
	<div class="site-breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
		<p class="site-breadcrumbs-p"><?php bcn_display(); ?></p>
	</div><!-- .site-breadcrumbs--><?php }

	// CONDITIONAL FOR "Yoast SEO" plugin, Breadcrumbs feature
	// https://wordpress.org/plugins/wordpress-seo/
	if ( function_exists('yoast_breadcrumb') ) {
		yoast_breadcrumb('<div class="site-breadcrumbs"><p class="site-breadcrumbs-p">','</p></div>');
	}

}

add_filter( 'wpseo_breadcrumb_links', 'custom_breadcrumb_links' );

function custom_breadcrumb_links( $links ) {
    // Loop through the breadcrumb links array
    foreach ( $links as $index => $link ) {
        // Check if this is the CATEGORY
        // print_r($link);
    }

    return $links;
}

add_filter( "paginate_links_output", 'htmx_ify_pagination', 10, 2 );
//add_filter( 'get_pagenum_link', 'htmx_ify_pagination', 10, 2 );
// Weirdly this only applies to links created via the_posts_pagination
function htmx_ify_pagination($html)
{
	// This is the only place where class comes before href in forming the link.
	// var_dump($html);
	// finds all links in your content
	return htmx_ify_content($html);
}

add_filter( 'the_tags', 'htmx_ify_tags');
add_filter( 'the_category', 'htmx_ify_tags');
add_filter( 'widget_custom_html_content', 'htmx_ify_tags');
add_filter( 'widget_text', 'htmx_ify_tags');
function htmx_ify_tags($html)
{
	// finds all links in your content
	return htmx_ify_content($html);
}
add_filter( 'nav_menu_link_attributes', 'htmx_ify_link_attrs', 10, 4 );
function htmx_ify_link_attrs( $atts, $item, $args, $depth ) {

    //var_dump( $atts, $item ); // a lot of stuff we can use

    // var_dump( $atts['href'] ); // string(36) "http://dev.rarst.net/our-philosophy/"

    // var_dump( get_the_title( $item->object_id ) ); // string(14) "Our Philosophy", note $item itself is NOT a page

	$baseUrl = parse_url(home_url(), PHP_URL_HOST);

    if ( str_contains($atts['href'], $baseUrl) ) { // for example

        // $atts['test'] = 'foobar';
		// hx-boost="true" hx-swap="outerHTML show:top" hx-target="#container" hx-push-url="true" hx-select="#container"$2$3
		$atts['hx-boost'] = 'true';
		$atts['hx-swap'] = "outerHTML show:top";
		$atts['hx-target'] = "#container";
		$atts['hx-push-url'] = "true";
		$atts['hx-select'] = "#container";
    }

    return $atts;
}


add_filter( 'next_posts_link_attributes', 'htmx_ify_link_attrs_string', 10, 1 );
add_filter( 'previous_posts_link_attributes', 'htmx_ify_link_attrs_string', 10, 1 );
//weirdly this doesn't apply to links created via the_posts_pagination
function htmx_ify_link_attrs_string( $atts ) {

	//var_dump( $atts, $item ); // a lot of stuff we can use

	// var_dump( $atts['href'] ); // string(36) "http://dev.rarst.net/our-philosophy/"

	// var_dump( get_the_title( $item->object_id ) ); // string(14) "Our Philosophy", note $item itself is NOT a page
	//var_dump('htmx_ify_link_attrs_string');
	// var_dump($atts);
	return $atts += get_htmx_link_props();
}

function podcast_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		
		// We don't use the default the_custom_logo() function because of its automatic addition of itemprop attributes (they fail the ARIA tests)
		
		$site = get_bloginfo('name');
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
		$html = sprintf( '<a '.get_htmx_link_props().' href="%1$s" class="custom-logo-link" rel="home">%2$s</a>', 
			esc_url( home_url( '/' ) ),
			wp_get_attachment_image( $custom_logo_id, 'full', false, array(
				'class'    => 'custom-logo',
				'alt' => __('Logo for ','podcast') . esc_attr($site),
				) )
			);
		}

		echo $html;

	}

}

function ilovewp_helper_display_title($post) {

	if( ! is_object( $post ) ) return;
	the_title( '<h1 class="page-title a-title">', '</h1>' );
}