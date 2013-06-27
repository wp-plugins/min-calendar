<?php
/**
 * MC_List_Table
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class MC_List_Table extends WP_List_Table {

    private $total_items;


    function __construct()
    {
        parent::__construct(
            array(
                'singular' => 'postid',
                'plural'   => 'postids',
                'ajax'     => false
            )
        );
    }


    public static function define_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'title'     => __( 'Title', 'mincalendar' ),
            'shortcode' => __( 'Shortcode', 'mincalendar' ),
            'author'    => __( 'Author', 'mincalendar' ),
            'date'      => __( 'Date', 'mincalendar' )
        );
        return $columns;
    }


    function prepare_items()
    {
        $this->total_items = 0;
        $current_screen = get_current_screen();
        $per_page       = $this->get_items_per_page( 'mc_per_page' );

        $this->_column_headers = $this->get_column_info();

        $args = array(
            'posts_per_page' => $per_page,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'offset'         => ( $this->get_pagenum() - 1 ) * $per_page
        );

        if ( false === empty( $_REQUEST[ 's' ] ) ) {
            $args[ 's' ] = $_REQUEST[ 's' ];
        }

        if ( false === empty( $_REQUEST[ 'orderby' ] ) ) {
            if ( 'title' === $_REQUEST[ 'orderby' ] ) {
                $args[ 'orderby' ] = 'title';
            } elseif ( 'author' === $_REQUEST[ 'orderby' ] ) {
                $args[ 'orderby' ] = 'author';
            } elseif ( 'date' === $_REQUEST[ 'orderby' ] ) {
                $args[ 'orderby' ] = 'date';
            }
        }

        if ( false === empty( $_REQUEST[ 'order' ] ) ) {
            if ( 'asc' === strtolower( $_REQUEST[ 'order' ] ) ) {
                $args[ 'order' ] = 'ASC';
            } elseif ( 'desc' === strtolower( $_REQUEST[ 'order' ] ) ) {
                $args[ 'order' ] = 'DESC';
            }
        }

        $this->items = $this->find( $args );
        $total_pages = ceil( $this->total_items / $per_page );

        $this->set_pagination_args(
            array(
                'total_items' => $this->total_items,
                'total_pages' => $total_pages,
                'per_page'    => $per_page
            )
        );
    }

    function get_columns()
    {
        return get_column_headers( get_current_screen() );
    }

    function get_sortable_columns()
    {
        $columns = array(
            'title'  => array( 'title', true ),
            'author' => array( 'author', false ),
            'date'   => array( 'date', false )
        );
        return $columns;
    }


    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function column_default( $item, $column_name )
    {
        return '';
    }


    function column_cb( $item )
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args[ 'singular' ],
            $item->id );
    }

    function column_title( $item )
    {
        $url = admin_url( 'admin.php?page=mincalendar&postid=' . absint( $item->id ) );
        $edit_link = add_query_arg( array( 'action' => 'edit' ), $url );

        $actions = array(
            'edit' => '<a href="' . $edit_link . '">' . __( 'Edit', 'mincalendar' ) . '</a>'
        );

        if ( current_user_can( 'edit', $item->id ) ) {
            $copy_link = wp_nonce_url(
                add_query_arg( array( 'action' => 'copy' ), $url ),
                'copy_' . absint( $item->id )
            );

            $actions = array_merge(
                $actions,
                array( 'copy' => '<a href="' . $copy_link . '">' . __( 'Copy', 'mincalendar' ) . '</a>' )
            );
        }

        $a = sprintf(
            '<a class="row-title" href="%1$s" title="%2$s">%3$s</a>',
            $edit_link,
            esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'mincalendar' ), $item->title ) ),
            esc_html( $item->title )
        );

        return '<strong>' . $a . '</strong> ' . $this->row_actions( $actions );
    }

    function column_author( $item ) {
        $post = get_post( $item->id );
        if ( false === $post ) {
            return;
        }
        $author = get_userdata( $post->post_author );
        return esc_html( $author->display_name );
    }


    function column_shortcode( $item )
    {
        $shortcodes = array(
            sprintf( '[mincalendar id="%1$d" title="%2$s"]', $item->id, $item->title ) );

        $output = '';

        foreach ( $shortcodes as $shortcode ) {
            $output .= "\n" . '<input type="text" onfocus="this.select();" readonly="readonly" value="'
                . esc_attr( $shortcode ) . '" class="shortcode-in-list-table" />';
        }

        return trim( $output );
    }


    function column_date( $item )
    {
        $post = get_post( $item->id );

        if ( false === $post ) {
            return;
        }

        $t_time = mysql2date( __( 'Y/m/d g:i:s A', 'mincalendar' ), $post->post_date, true );
        $m_time = $post->post_date;
        $time = mysql2date( 'G', $post->post_date ) - get_option( 'gmt_offset' ) * 3600;

        $time_diff = time() - $time;

        if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 )
            $h_time = sprintf( __( '%s ago', 'mincalendar' ), human_time_diff( $time ) );
        else
            $h_time = mysql2date( __( 'Y/m/d', 'mincalendar' ), $m_time );

        return '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
    }


    /**
     * @param string $args
     * @return array
     */
    public function find( $args = '' )
    {
        $defaults = array(
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'offset'         => 0,
            'orderby'        => 'ID',
            'order'          => 'ASC'
        );

        $args                = wp_parse_args( $args, $defaults );
        $args[ 'post_type' ] = 'mincalendar';
        $q                   = new WP_Query();
        $posts               = $q->query( $args );
        $this->total_items   = $q->found_posts;
        $objs                = array();
        foreach ( (array) $posts as $post ) {
            $objs[] = MC_Post_Factory::get_post_wrapper( $post->ID );
        }
        return $objs;
    }
}
