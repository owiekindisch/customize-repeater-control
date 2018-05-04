# [WordPress] Customize Repeater Control
Repetitive control for the Theme Customization API of WordPress.

_Currently under development_

## Current status:

*  [x] Render content of applied controls
*  [x] Save and retrieve repeater field values
*  [ ] Fully compatible with all default controls
*  [ ] Add an option to remove fields
*  [ ] Support ordering

## Usage:
````
require_once 'customize-repeater-control.php';

add_action( 'customize_register', 'mytheme_customize_register' );
function mytheme_customize_register( $wp_customize ) {
	$wp_customize->add_setting( 'mytheme_value_xyz', array(
		'default'        => 'Hello World!',
		'capability'     => 'edit_theme_options',
		'type'           => 'option'
	) );

	$wp_customize->add_control( new Theme_Customize_Repeater_Control( $wp_customize, 'mytheme_value_xyz', array(
		'label'       => __( 'Item', 'mytheme' ),
		'section'     => 'title_tagline',
		'fields'      => array(
			array(
				'key'     => 'key',
				'control' => 'WP_Customize_Control',
				'args'    => array(
					'label' => __( 'Item key', 'mytheme' ),
				)
			),
			array(
				'key'     => 'value',
				'control' => 'WP_Customize_Control',
				'args'    => array(
					'label' => __( 'Item value', 'mytheme' ),
				)
			)
		)
	) ) );
}
````
