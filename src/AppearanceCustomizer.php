<?php
namespace FuelTravel;
class AppearanceCustomizer
{

    private $dependencies_met;
    private $active;
    public  $default_config;

    function __construct( $default_config = '' ) {

        $this->dependencies_met = true;
        if ( !class_exists( 'ACF' ) ):
            $this->dependencies_met = false;
        endif;

        $this->active = true;

        if( $default_config ):
            $this->default_config = $default_config;
        else:
            $this->default_config = require( 'default_config.php' );
        endif;

        add_action( 'init', array( $this, 'customizer_init' ), 4 );

    }

    public function on_start() {
        require $this->getPackagePath() . '/vendor/autoload.php';
    }

    function customizer_init() {

        if( $this->active && $this->dependencies_met ):

            add_action( 'customize_register', array( $this, 'load_settings'), 10 );

        else:

            return false;

        endif;

    }

    function load_settings() {

        global $wp_customize;

        // Text Setting Section
        $wp_customize->add_section( 'ft_text', array(
            'title'          => __( 'Text', 'ft_textdomain' ),
            'description'    => __( 'Choose the global settings for text. Color settings are located in the Color section.', 'ft_textdomain' ),
            'priority'       => 35,
        ) );

        // Entry Title Size
        $wp_customize->add_setting(
            'ft_entry_title_size',
            array(
                'default'           => $this->get_default_entry_title_size(),
                'sanitize_callback' => 'absint',
            )
        );
        $wp_customize->add_control(
            'ft_entry_title_size',
            array(
                'description' => __( 'Change the size of the text for all the Entry Title headings.', 'ft_textdomain' ),
                'label'       => __( 'Entry Title Size', 'ft_textdomain' ),
                'section'     => 'ft_text',
                'settings'    => 'ft_entry_title_size',
                'type'        => 'number',
            )
        );

        // Entry Title Weight
        $wp_customize->add_setting(
            'ft_entry_title_weight',
            array(
                'default'           => $this->get_default_entry_title_weight(),
                'sanitize_callback' => array( $this, select_option_sanitizer ),
            )
        );
        $wp_customize->add_control(
            'ft_entry_title_weight',
            array(
                'description' => __( 'Change the weight of the text for all the Entry Title headings.', 'ft_textdomain' ),
                'label'       => __( 'Entry Title Weight', 'ft_textdomain' ),
                'section'     => 'ft_text',
                'settings'    => 'ft_entry_title_weight',
                'type'        => 'select',
                'choices' => array(
                    100 => 100,
                    200 => 200,
                    300 => 300,
                    400 => 400,
                    500 => 500,
                    600 => 600,
                    700 => 700,
                    800 => 800,
                    900 => 900,
                )
            )
        );

        // Entry Content Line Clamp
        $wp_customize->add_setting(
            'ft_entry_content_line_clamp',
            array(
                'default'           => $this->get_default_entry_content_line_clamp(),
                'sanitize_callback' => 'absint',
            )
        );
        $wp_customize->add_control(
            'ft_entry_content_line_clamp',
            array(
                'description' => __( 'Sets the maximum number of lines of Entry Content to show on archive pages and widgets.', 'ft_textdomain' ),
                'label'       => __( 'Entry Content Line Clamp', 'ft_textdomain' ),
                'section'     => 'ft_text',
                'settings'    => 'ft_entry_content_line_clamp',
                'type'        => 'number',
            )
        );

        // Heading Color
        $wp_customize->add_setting(
            'ft_heading_color',
            array(
                'default'           => $this->get_default_heading_color(),
                'sanitize_callback' => 'sanitize_hex_color',
            )
        );
        $wp_customize->add_control(
            new \WP_Customize_Color_Control(
                $wp_customize,
                'ft_heading_color',
                array(
                    'description' => __( 'Change the Default color for the headings in the Entry, Archive, and Widget Titles.', 'ft_textdomain' ),
                    'label'       => __( 'Heading Color', 'ft_textdomain' ),
                    'section'     => 'colors',
                    'settings'    => 'ft_heading_color',
                )
            )
        );

    }

    function active() {
        return $this->active;
    }

    function disable() {
        $this->active = false;
    }

    function enable() {
        $this->active = true;
    }

    function get_default_heading_color() {
        return $this->default_config['heading_color'];
    }

    function get_default_entry_title_size() {
        return $this->default_config['entry_title_size'];
    }

    function get_default_entry_title_weight() {
        return $this->default_config['entry_title_weight'];
    }

    function get_default_entry_content_line_clamp() {
        return $this->default_config['entry_content_line_clamp'];
    }

    function select_option_sanitizer( $input, $setting ) {
        //get the list of possible select options
        $choices = $setting->manager->get_control( $setting->id )->choices;

        //return input if valid or return default option
        return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
    }

}