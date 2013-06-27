<?php
/**
 * MC_Main
 */
class MC_Main
{

    function __construct()
    {
        require_once MC_PLUGIN_DIR . '/admin/class-admin-controller.php';
        require_once MC_PLUGIN_DIR . '/admin/class-admin-utilities.php';
        require_once MC_PLUGIN_DIR . '/admin/class-admin-action.php';
        require_once MC_PLUGIN_DIR . '/admin/class-appearance.php';
        require_once MC_PLUGIN_DIR . '/admin/class-custom-field.php';
        require_once MC_PLUGIN_DIR . '/admin/class-list-table.php';
        require_once MC_PLUGIN_DIR . '/admin/class-post-form.php';
        require_once MC_PLUGIN_DIR . '/admin/class-validation.php';
        require_once MC_PLUGIN_DIR . '/includes/class-capabilities.php';
        require_once MC_PLUGIN_DIR . '/includes/class-controller.php';
        require_once MC_PLUGIN_DIR . '/includes/class-date.php';
        require_once MC_PLUGIN_DIR . '/includes/class-draw-calendar.php';
        require_once MC_PLUGIN_DIR . '/includes/class-post-factory.php';
        require_once MC_PLUGIN_DIR . '/includes/class-post-wrapper.php';
        require_once MC_PLUGIN_DIR . '/includes/class-utilities.php';

        new MC_Capabilities();

        if ( is_admin() ) {
            new MC_Admin_Controller();
        } else {
            new MC_Controller();
        }

        add_action( 'admin_init', array( $this, 'upgrade' ) );
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'activate_' . MC_PLUGIN_BASENAME, array( &$this, 'activate' ) );
    }


    /**
     * Initialize Min Calendar plugin
     */
    public function init()
    {
        // L18N
        load_plugin_textdomain( 'mincalendar', false, 'min-calendar/languages' );
        // Custom Post Type
        $this->register_post_types();
    }


    /**
     * Min Calendar用カスタム投稿タイプ登録
     */
    private function register_post_types()
    {
        register_post_type(
            MC_Utilities::get_post_type(),
            array(
                'labels'    => array(
                    'name'          => 'Min Calendar',
                    'singular_name' => 'Min Calendar',
                ),
                'rewrite'   => false,
                'query_var' => false
            )
        );
    }


    /**
     *  activate and default settings
     */
    public function activate()
    {
        $opt = get_option( ( 'mincalendar') );
        if ( $opt ) {
            return;
        }

        load_plugin_textdomain( 'mincalendar', false, 'min-calendar/languages' );

        $this->register_post_types();
        $this->upgrade();

    }


    /**
     * Upgrading
     *
     * current version of option update
     */
    public function upgrade()
    {
        $opt = get_option( 'mincalendar' );

        if ( ! is_array( $opt ) ) {
            $opt = array();
        }

        $old_ver = isset( $opt[ 'version' ] ) ? (string) $opt[ 'version' ] : '0';
        $new_ver = MC_VERSION;

        if ( $old_ver === $new_ver ) {
            return;
        }

        $opt[ 'version' ] = $new_ver;
        update_option( 'mincalendar', $opt );
    }

}
