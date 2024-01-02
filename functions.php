<?php
/**
 * Set up My Child Theme's textdomain.
 *
 * Declare textdomain for this child theme.
 * Translations can be added to the /languages/ directory.
 */
function podcastchild_theme_setup() {
	load_child_theme_textdomain( 'podcastchild', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'podcastchild_theme_setup' );

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
	$parenthandle = 'podcast-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
	$theme        = wp_get_theme();
	wp_enqueue_style( $parenthandle,
		get_template_directory_uri() . '/style.css',
		array(),  // If the parent theme code has a dependency, copy it to here.
		$theme->parent()->get( 'Version' )
	);
	wp_enqueue_style( 'child-style',
		get_stylesheet_uri(),
		array( $parenthandle ),
		$theme->get( 'Version' ) // This only works if you have Version defined in the style header.
	);
}

add_action( 'wp_enqueue_scripts', 'htmx_scripts' );
function htmx_scripts() {
    wp_enqueue_script(
        'htmx',
        get_stylesheet_directory_uri() . '/js/htmx.min.js',
        array( 'jquery' )
    );
	wp_enqueue_script(
        'htmx-head-support',
        get_stylesheet_directory_uri() . '/js/head-support.js',
        array( 'htmx' )
    );
	wp_enqueue_script(
        'xplayer',
        get_stylesheet_directory_uri() . '/js/xplayer.js',
        array( 'htmx' )
    );
	wp_enqueue_script(
        'xplayer-basic-native',
        get_stylesheet_directory_uri() . '/js/xplayer-basic-native.js',
        array( 'htmx' )
    );
}


// Get Color Palette from Theme Options
if( ! function_exists( 'ilovewp_helper_get_color_palette' ) ) {
	function ilovewp_helper_get_color_palette() {

		global $post;

		$valid_palettes = array('black','blue','green','orange','purple','red','teal','yellow', 'localswitchboard');
		$themeoptions_color_palette = esc_attr(get_theme_mod( 'theme-color-palette', 'teal' ));
		$class_string = 'theme-color-';

		if ( in_array($themeoptions_color_palette, $valid_palettes) ) {
			$class_string = $class_string . $themeoptions_color_palette;
		} else {
			$class_string = $class_string . 'blue';
		}

		return $class_string;
	}
}


function ilovewp_customizer_define_general_sections_child( $sections ) {
    $panel           = 'ilovewp' . '_general';
    $general_sections = array();

    $theme_sidebar_positions = array(
        'left'      => esc_html__('Left', 'podcast'),
        'right'     => esc_html__('Right', 'podcast')
    );

    $theme_color_palettes = array(
        'black'         => esc_html__('Black', 'podcast'),
        'blue'          => esc_html__('Blue', 'podcast'),
        'green'         => esc_html__('Green', 'podcast'),
        'orange'        => esc_html__('Orange', 'podcast'),
        'purple'        => esc_html__('Purple', 'podcast'),
        'red'           => esc_html__('Red', 'podcast'),
        'teal'          => esc_html__('Teal', 'podcast'),
        'yellow'        => esc_html__('Yellow', 'podcast'),
		'localswitchboard'        => esc_html__('Local Switchboard', 'podcast')
    );

    $general_sections['general'] = array(
        'title'     => esc_html__( 'Theme Settings', 'podcast' ),
        'priority'  => 4900,
        'options'   => array(

            'theme-color-palette'    => array(
                'setting'               => array(
                    'default'           => 'black',
                    'sanitize_callback' => 'ilovewp_sanitize_text'
                ),
                'control'           => array(
                    'label'         => esc_html__( 'Theme Color Palette', 'podcast' ),
                    'type'          => 'select',
                    'choices'       => $theme_color_palettes
                ),
            ),

            'theme-sidebar-position'    => array(
                'setting'               => array(
                    'default'           => 'right',
                    'sanitize_callback' => 'ilovewp_sanitize_text'
                ),
                'control'           => array(
                    'label'         => esc_html__( 'Default Sidebar Position', 'podcast' ),
                    'type'          => 'select',
                    'choices'       => $theme_sidebar_positions
                ),
            ),

            'podcast-display-latest-episode' => array(
                'setting'               => array(
                    'sanitize_callback' => 'absint',
                    'default'           => 0
                ),
                'control'               => array(
                    'label'             => __( 'Display Newest Episode Block on Homepage', 'podcast' ),
                    'type'              => 'checkbox'
                )
            ),

            'podcast-episodes-category'  => array(
                'setting'               => array(
                    'default'           => '1',
                    'sanitize_callback' => 'podcast_sanitize_categories'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Category containing Episodes', 'podcast' ),
                    'description'       => /* translators: link to categories */ sprintf( wp_kses( __( 'This list is populated with <a href="%1$s">Categories</a>.', 'podcast' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'edit-tags.php?taxonomy=category' ) ) ),
                    'type'              => 'select',
                    'choices'           => podcast_get_categories()
                ),
            ),

            'podcast-episodes-label' => array(
                'setting'               => array(
                    'sanitize_callback' => 'ilovewp_sanitize_text',
                    'default'           => esc_html__( 'Newest Episode', 'podcast' ),
                ),
                'control'               => array(
                    'label'             => __( 'Post Block Heading', 'podcast' ),
                    'type'              => 'text'
                )
            ),

            'podcast-display-pages'    => array(
                'setting'               => array(
                    'sanitize_callback' => 'absint',
                    'default'           => 0
                ),
                'control'               => array(
                    'label'             => __( 'Display Featured Pages on Homepage', 'podcast' ),
                    'type'              => 'checkbox'
                )
            ),

            'podcast-featured-page-1'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'podcast_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Featured Page #1', 'podcast' ),
                    'description'       => /* translators: link to pages */ sprintf( wp_kses( __( 'This list is populated with <a href="%1$s">Pages</a>.', 'podcast' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'edit.php?post_type=page' ) ) ),
                    'type'              => 'dropdown-pages',
                    'choices'           => podcast_get_pages()
                ),
            ),

            'podcast-featured-page-2'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'podcast_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Featured Page #2', 'podcast' ),
                    'type'              => 'dropdown-pages',
                    'choices'           => podcast_get_pages()
                ),
            ),

            'podcast-featured-page-3'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'podcast_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Featured Page #3', 'podcast' ),
                    'type'              => 'dropdown-pages',
                    'choices'           => podcast_get_pages()
                ),
            ),

            'podcast-featured-page-4'  => array(
                'setting'               => array(
                    'default'           => 'none',
                    'sanitize_callback' => 'podcast_sanitize_pages'
                ),
                'control'               => array(
                    'label'             => esc_html__( 'Featured Page #4', 'podcast' ),
                    'type'              => 'dropdown-pages',
                    'choices'           => podcast_get_pages()
                ),
            ),

        ),
    );

    return array_merge( $sections, $general_sections );
}

function get_htmx_link_props(){
	return ' hx-boost="true" hx-swap="outerHTML show:top" hx-target="#container" hx-push-url="true" hx-select="#container" ';
}

function the_htmx_link_props(){
	echo get_htmx_link_props();
}

function alter_link_to_htmx($toReplace, $content){
	$baseUrl = parse_url(home_url(), PHP_URL_HOST);
	//$urlParts = explode('.', $baseUrl);

	if (!preg_match('/hx-target\s*\=/',$toReplace) && preg_match('/'.preg_quote($baseUrl).'/',$toReplace)){
		$replacement = preg_replace('/(\<a.*?)(\>)(.*?\/a\>.*?)/','$1  hx-boost="true" hx-swap="outerHTML show:top" hx-target="#container" hx-push-url="true" hx-select="#container"$2$3',$toReplace);
		// replaces the current link with the $replacement string
		$content = str_ireplace($toReplace,$replacement,$content);
	}
	return $content;
}

function htmx_ify_content($html){
	preg_match_all('/(\<a.*href=.*?a>)/',$html,$matches, PREG_SET_ORDER);
	//	print_r('<pre><code>');
	//	print_r($matches);
	//	print_r('</pre></code>');
		// loop through all matches
		foreach($matches as $m){
			// potential link to be replaced...
			$toReplace = $m[0];
			// Why is the link there twice in each array?
			$html = alter_link_to_htmx($toReplace, $html);
			//$toReplace = $m[1];
			//alter_link_to_htmx($toReplace, $html);
	
		}
	//	print_r('<pre><code>');
	//	print_r($html);
	//	print_r('</code></pre>');
		return $html;
}

add_filter( 'ilovewp_customizer_sections', 'ilovewp_customizer_define_general_sections_child', 11 );

require_once( get_theme_file_path('/supporting-functions/post-treatments.php') );
require_once( get_theme_file_path('/supporting-functions/link-treatments.php') );