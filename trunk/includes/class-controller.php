<?php
/**
 * MC_Controller
 */
class MC_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        add_action( 'plugins_loaded', array( &$this, 'add_shortcodes' ), 1 );
        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles_base' ) );
    }


    /**
     * Set 'mincalendar' shortcode used wordpress builtin add_shortcode function
     */
    function add_shortcodes()
    {
        add_shortcode( 'mincalendar', array( &$this, 'display' ) );
    }


    /**
     * display calendar html markup that is created by shortcode tag
     *
     *
     * @param $atts
     * @param null $content
     * @param string $code mincalendar
     * @return string calendar html markup
     */
    public function display( $atts, $content = null, $code = '' )
    {

        if ( is_feed() ) {
            return '[mincalendar]';
        }

        if ( 'mincalendar' === $code ) {
            $atts = shortcode_atts(
                array(
                    'id'    => 0
                ),
                $atts
            );
            $id    = (int) $atts[ 'id' ];
            // num カレンダーの表示数を追加予定
        }

        // make calendar html markup
        $unit_tag = 'mincalendar-' . $id;
        $html = '<div id="' . $unit_tag . '" class="mincalendar">';
        $html .= MC_Draw_Calendar::draw( $id );
        $html .= '</div>';

        return $html;
    }


    /**
     * Add style to wordpress style enqueue
     */
    function enqueue_styles()
    {
        wp_enqueue_style(
            'mincalendar',
            MC_Utilities::mc_plugin_url( 'includes/css/mincalendar.css' ),
            array(),
            MC_VERSION,
            'all'
        );

        do_action( 'enqueue_styles' );
    }
    /**
     * Add style to wordpress style enqueue
     */
    function enqueue_styles_base()
    {
        wp_enqueue_style(
            'mincalendar-base',
            MC_Utilities::mc_plugin_url( 'includes/css/mincalendar-base.css' ),
            array(),
            MC_VERSION,
            'all'
        );

        do_action( 'enqueue_styles_base' );
    }

}
