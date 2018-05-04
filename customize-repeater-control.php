<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) exit;

// Abort if Customizer API not available
if ( ! class_exists( 'WP_Customize_Control' ) ) return;

class Theme_Customize_Repeater_Control extends WP_Customize_Control {

	/*
	** Storage for repeater fields
	*/
	protected $fields = array();

	/*
	** Storage for settings argument
	*/
	protected $_settings;

	/*
	** Constructor
	*/
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );

		$this->_settings = isset( $this->args['settings'] ) ? $this->args['settings'] : $this->id;

		if ( is_array( $this->fields ) && ! empty( $this->fields ) ) {
			$this->prepare_fields( $this->fields );
		}

		// Force control type to 'repeater'
		$this->type = 'repeater';
	}

	/*
	** Enqueue control related scripts/styles
	*/
	public function enqueue() {
		wp_enqueue_style('customize-repeater-control', THEME_URI . 'inc/customize-repeater/customize-repeater-control.css', array('customize-controls'));
		wp_enqueue_script('customize-repeater-control', THEME_URI . 'inc/customize-repeater/customize-repeater-control.js', array('jquery', 'customize-controls'), false, true);
	}

	/*
	** Refresh the parameters passed to the JavaScript via JSON
	*/
	public function to_json() {
		parent::to_json();

		$fields = $this->fields;
		foreach ( $fields as $key => &$field ) {
			$Control = new $field['control']( $this->manager, $this->_settings, $field['args'] );

			$field['control'] = str_replace(array('WP_Customize', '_'), '', $field['control']);
			$field['args'] = $Control->json();
		}

		$this->json['fields'] = $fields;
	}

	/*
	** Prepare fields and add them to local storage
	*/
	protected function prepare_fields( $fields ) {
		// Reset fields
		$this->fields = array();

		foreach ( $fields as $field ) {
			$field = $this->normalize_field( $field );

			if ( class_exists( $field['control'] ) ) {
				// Force section id
				$field['args']['section'] = $this->section;

				$this->fields[ $field['key'] ] = $field;
			}
		}
	}

	/*
	** Normalize field arguments
	*/
	protected function normalize_field( $args ) {
		$defaults = array(
			'key' => '',
			'control' => 'WP_Customize_Control',
			'args' => array()
		);

		$args = wp_parse_args($args, $defaults);

		return $args;
	}

	/*
	** Don't render the control's content - it uses a JS template instead.
	*/
	protected function render_content() {}

	/*
	** An Underscore (JS) template for this control's content (but not its container)
	*/
	public function content_template() {
		?>
		<# if ( data.label ) { #>
			<span class="customize-control-title">{{{ data.label }}}</span>
		<# } #>
		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>
		<div class="customize-control-content">
			<div class="customize-control-repeater-fields control-subsection">
				<div class="customize-control-repeater-field accordion-section-title prototype">
					<ul class="wp-clearfix">
					</ul>
				</div>
			</div>

			<div class="customize-control-repeater-buttons">
				<button type="button" class="button customize-add-repeater-field" aria-label="<?php esc_attr_e( 'Add new item' ); ?>" aria-expanded="false" aria-controls="available-repeater-items">
					<?php _e( 'Add Items' ) ?>
				</button>
			</div>
		</div>
		<?php
	}
}

add_action( 'customize_register', 'themeRegisterCustomizeRepeaterControl', 11 );
function themeRegisterCustomizeRepeaterControl( $wp_customize ) {
	// Require JS-rendered control types.
	$wp_customize->register_control_type( 'Theme_Customize_Repeater_Control' );
}