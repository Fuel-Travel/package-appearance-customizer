<?php
namespace FuelTravel;
class AppearanceCustomizer
{

    private $dependencies_met;
    private $active;
    private $stylesheet_handle;
    private $default_config;
    public  $enable_settings;

    function __construct( $stylesheet_handle = '', $default_config = '' ) {

        $this->dependencies_met = true;
        if ( !class_exists( 'ACF' ) ):
            $this->dependencies_met = false;
        endif;

        $this->active = true;

        $this->stylesheet_handle = $stylesheet_handle;

        if( $default_config ):
            $this->default_config = $default_config;
        else:
            $this->default_config = require( 'default_config.php' );
        endif;

        $this->enable_settings = true;

        add_action( 'init', array( $this, 'customizer_init' ), 4 );
        add_action( 'wp_enqueue_scripts', array( $this, 'print_customizer_styles' ), 99 );

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

        // Heading Color
        if( $this->show_it( 'heading_color' ) ):
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
                        'description' => __( 'Change the Default color for the headings in the Entry, and Archive, Titles.', 'ft_textdomain' ),
                        'label'       => __( 'Heading Color', 'ft_textdomain' ),
                        'section'     => 'colors',
                        'settings'    => 'ft_heading_color',
                    )
                )
            );
        endif;

        // Text Setting Section
        if( $this->show_it( 'text_section' ) ):
            $wp_customize->add_section( 'ft_text', array(
                'title'          => __( 'Text', 'ft_textdomain' ),
                'description'    => __( 'Choose the global settings for text. Color settings are located in the Color section.', 'ft_textdomain' ),
                'priority'       => 35,
            ) );
        endif;

        // Entry Title Size
        if( $this->show_it( 'text_section' ) && $this->show_it( 'entry_title_size' ) ):
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
        endif;

        // Entry Title Weight
        if( $this->show_it( 'text_section' ) && $this->show_it( 'entry_title_weight' ) ):
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
        endif;

        // Entry Content Line Clamp
        if( $this->show_it( 'text_section' ) && $this->show_it( 'entry_content_clamp' ) ):
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
        endif;

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
        return $this->default_config['heading_color']['default'];
    }

    function get_default_entry_title_size() {
        return $this->default_config['entry_title_size']['default'];
    }

    function get_default_entry_title_weight() {
        return $this->default_config['entry_title_weight']['default'];
    }

    function get_default_entry_content_line_clamp() {
        return $this->default_config['entry_content_line_clamp']['default'];
    }

    function select_option_sanitizer( $input, $setting ) {
        //get the list of possible select options
        $choices = $setting->manager->get_control( $setting->id )->choices;

        //return input if valid or return default option
        return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
    }

    function show_it( $setting_key ) {
        $enable = $this->enable_settings;
        if( $enable && !is_array( $enable ) )
            return true;

        if( $enable && is_array( $enable ) && in_array( $setting_key, $enable ) )
            return true;

        return false;
    }

    function print_customizer_styles() {
        $heading_color            = get_theme_mod( 'ft_heading_color' );
        $entry_title_size         = get_theme_mod( 'ft_entry_title_size' );
        $entry_title_weight       = get_theme_mod( 'ft_entry_title_weight' );
        $entry_content_line_clamp = get_theme_mod( 'ft_entry_content_line_clamp' );

        $css = '';

        // Heading Color Styles
        if( $this->show_it( 'heading_color' ) ):
            if( $heading_color ):
                $css .= make_css_targets( $this->default_config['heading_color']['targets'] ) ." {
                    color: " . $heading_color . ";
                }";
            endif;
        endif;

        // Entry Title Size
        if( $this->show_it( 'text_section' ) && $this->show_it( 'entry_title_size' ) ):
            if( $heading_color ):
                $css .= make_css_targets( $this->default_config['entry_title_size']['targets'] ) . " {
                    font-size: " . $entry_title_size . "px;
                }";
            endif;
        endif;

        // Entry Title Weight
        if( $this->show_it( 'text_section' ) && $this->show_it( 'entry_title_weight' ) ):
            if( $entry_title_weight ):
                $css .= make_css_targets( $this->default_config['entry_title_weight']['targets'] ) . " {
                    font-weight: " . $entry_title_weight . ";
                }";
            endif;
        endif;

        // Entry Content Line Clamp
        if( $this->show_it( 'text_section' ) && $this->show_it( 'entry_content_clamp' ) ):
            if( $entry_content_line_clamp ):
                $entry_content_font_size = $this->default_config['entry_content_line_clamp']['font_size'];
                $entry_content_line_height = $this->default_config['entry_content_line_clamp']['line_height'];
                $css .= make_css_targets( $this->default_config['entry_content_line_clamp']['targets'] ) . " {
                    -webkit-line-clamp: " . $entry_content_line_clamp . ";
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    font-size: " . $entry_content_font_size . "px;
                    line-height: " . $entry_content_line_height . "em;
                    height: " . $entry_content_line_clamp * $entry_content_line_height . "em;
                    overflow: hidden;
                }";
            endif;
        endif;

        if ( $css ) {

            $css = str_replace('; ',';',str_replace(' }','}',str_replace('{ ','{',str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),"",preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$css)))));
            $comment = "/* FT Customizer Style */\n";

            if( !$this->stylesheet_handle ):
                $this->stylesheet_handle = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';
            endif;
            wp_add_inline_style( $this->stylesheet_handle, $comment . $css );

        }

    }

}

function make_css_targets( $targets ) {
    $css = '';
    foreach( $targets as $target ):
        $css .= $target . ",";
    endforeach;
    return rtrim( $css, "," );
}