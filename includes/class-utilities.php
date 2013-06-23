<?php
class MC_Utilities
{
    public static $post_type = 'mincalendar';


    public static function get_post_type()
    {
        return self::$post_type;
    }

    public static function mc_plugin_url( $path = '' )
    {
        $url = untrailingslashit( MC_PLUGIN_URL );
        if ( ! empty( $path )
            && is_string( $path )
            && false === strpos( $path, '..' )
        ) {
            $url .= '/' . ltrim( $path, '/' );
        }
        return $url;
    }


    public static function mc_sys_msg()
    {
        $sys_msgs = array(
            'validation_error' => array(
                'description' => __( "Validation errors occurred", 'mincalendar' ),
                'default'     => __( 'Validation errors occurred. Please confirm the fields and submit it again.', 'mincalendar' )
            ),

            'accept_terms' => array(
                'description' => __( "There are terms that the sender must accept", 'mincalendar' ),
                'default'     => __( 'Please accept the terms to proceed.', 'mincalendar' )
            )

        );

        return apply_filters( 'mc_sys_msg', $sys_msgs );
    }


    public static function mc_is_rtl()
    {
        if ( function_exists( 'is_rtl' ) )
            return is_rtl();

        return false;
    }


    public static function mc_ajax_loader()
    {
        $url = self::mc_plugin_url( 'images/ajax-loader.gif' );

        return apply_filters( 'mc_ajax_loader', $url );
    }


    public static function mc_verify_nonce( $nonce, $action = -1 )
    {
        if ( substr( wp_hash( $action, 'nonce' ), -12, 10 ) == $nonce )
            return true;

        return false;
    }


    public static function mc_array_flatten( $input )
    {
        if ( ! is_array( $input ) )
            return array( $input );

        $output = array();

        foreach ( $input as $value )
            $output = array_merge( $output, self::mc_array_flatten( $value ) );

        return $output;
    }

}
