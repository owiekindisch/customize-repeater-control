# [WordPress] Customize Repeater Control
Repetitive control for the Theme Customization API of WordPress.

## Back-end Usage:
````
require_once 'customize-repeater-control.php';

add_action( 'customize_register', 'mytheme_customize_register' );
function mytheme_customize_register( $wp_customize ) {
    $wp_customize->register_control_type( 'Theme_Customize_Repeater_Control' );

	$wp_customize->add_setting( 'mytheme_frontend_slideshow', array(
		'capability' => 'edit_theme_options',
		'type'       => 'theme_mod' // or can be 'option'
		'default'    => json_encode( array(
			array(
				'image' => 'http://plugins.local/wp-content/uploads/2020/10/hoodie-with-logo.jpg',
				'url'   => 'http://plugins.local/hoodie',
			),
			array(
				'image' => 'http://plugins.local/wp-content/uploads/2020/10/beanie.jpg',
				'url'   => 'http://plugins.local/beanie',
			),
		), JSON_UNESCAPED_SLASHES ),
	) );

	$wp_customize->add_control( new Theme_Customize_Repeater_Control( $wp_customize, 'mytheme_frontend_slideshow', array(
		'label'   => __( 'Item', 'mytheme' ),
		'section' => 'title_tagline',
		'fields'  => array(
			array(
				'key'     => 'image',
				'control' => 'WP_Customize_Image_Control',
				'args'    => array(
					'label' => __( 'Image', 'mytheme' ),
				)
			),
			array(
				'key'     => 'url',
				'control' => 'WP_Customize_Control',
				'args'    => array(
					'label' => __( 'URL', 'mytheme' ),
				)
			),			
		)
	) ) );
}
````

## Front-end Usage:
````
// get data from theme mod
$slideshow = get_theme_mod( "mytheme_frontend_slideshow", json_encode( array(
	array(
		'image' => 'http://plugins.local/wp-content/uploads/2020/10/hoodie-with-logo.jpg',
		'url'   => 'http://plugins.local/hoodie',
	),
	array(
		'image' => 'http://plugins.local/wp-content/uploads/2020/10/beanie.jpg',
		'url'   => 'http://plugins.local/beanie',
	),
), JSON_UNESCAPED_SLASHES ) );

// the repeater data is a json data, so it's need to be encoded to array
$slideshow = json_decode( $slideshow, JSON_OBJECT_AS_ARRAY );

// check the data
var_dump( $slideshow );
````