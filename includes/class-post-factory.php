<?php
/**
 * MC_Post_Factory
 */
class MC_Post_Factory
{

    /**
     * @param $post_id post id of custom post type 'mincalendar'
     * @return bool|MC_Post_Wrapper
     */
    public static function get_post_wrapper( $post_id = null )
    {
        $post_wrapper         = new MC_Post_Wrapper();
        $post_wrapper->set_initial( true );
        $post_wrapper->set_id( $post_id );
        $post                 = get_post( $post_id );

        if ( ! empty( $post )
            || MC_Utilities::$post_type === get_post_type( $post ) ) {
            $post_wrapper->set_initial( false );
            $post_wrapper->set_id( $post->ID );
            $post_wrapper->set_title( $post->post_title );
        } else {
            $post_wrapper->set_initial( true );
            $post_wrapper->set_title( __( 'Untitled', 'mincalendar' ) );
        }
        return $post_wrapper;
    }

}
