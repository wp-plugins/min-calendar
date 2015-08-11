<?php

/**
 * MC_Main
 *
 * min calendar
 */
class MC_Main
{

	function __construct()
	{
		require_once MC_PLUGIN_DIR . '/admin/class-admin-controller.php';
		require_once MC_PLUGIN_DIR . '/admin/class-admin-utilities.php';
		require_once MC_PLUGIN_DIR . '/admin/class-manage-form-action.php';
		require_once MC_PLUGIN_DIR . '/admin/class-appearance.php';
		require_once MC_PLUGIN_DIR . '/admin/class-custom-field.php';
		require_once MC_PLUGIN_DIR . '/admin/class-list-table.php';
		require_once MC_PLUGIN_DIR . '/admin/class-post-form.php';
		require_once MC_PLUGIN_DIR . '/admin/class-validation.php';
		require_once MC_PLUGIN_DIR . '/includes/class-capabilities.php';
		require_once MC_PLUGIN_DIR . '/common/class-day.php';
		require_once MC_PLUGIN_DIR . '/view/class-calendar-drawing.php';
		require_once MC_PLUGIN_DIR . '/view/class-calendar-maker.php';
		require_once MC_PLUGIN_DIR . '/includes/class-post-factory.php';
		require_once MC_PLUGIN_DIR . '/includes/class-post-wrapper.php';
		require_once MC_PLUGIN_DIR . '/includes/class-utilities.php';
		// get_currentuserinfo function is defined by pluggable.php. not automatically load.
		require_once ABSPATH . WPINC . '/pluggable.php';

		// user level administrator can execute.
		global $user_level;
		get_currentuserinfo();

		if ( 10 === (int) $user_level ) {
			new MC_Capabilities();
		}

		if ( is_admin() && 10 === (int) $user_level ) {
			// 管理者に対する画面処理
			$controller = new MC_Admin_Controller();
			$controller->setup();
			// アクティベートまたはアップレード時処理
			add_action( 'activate_' . MC_PLUGIN_BASENAME, array( &$this, 'activate' ) );
			add_action( 'admin_init', array( $this, 'upgrade' ) );
		} else {
			// 一般閲覧者への表示処理
			$drawing = new MC_Calendar_Drawing();
			$drawing->run();
		}
	}

	/**
	 * カスタム投稿タイプ(mincalendar)登録
	 */
	private function register_post_types()
	{
		register_post_type(
			'mincalendar',
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
	 * 有効化処理
	 */
	public function activate()
	{
		$option = get_option( ( 'mincalendar' ) );
		if ( $option ) {
			return;
		}
		load_plugin_textdomain( 'mincalendar', false, 'min-calendar/languages' );
		$this->register_post_types();
		$this->upgrade();
	}

	/**
	 * アップグレード処理
	 */
	public function upgrade()
	{
		$option = get_option( 'mincalendar' );
		if ( ! is_array( $option ) ) {
			$option = array();
		}
		$old_ver = isset( $option['version'] ) ? (string) $option['version'] : '0';
		$new_ver = MC_VERSION;
		if ( $old_ver === $new_ver ) {
			return;
		}
		$option['version'] = $new_ver;
		update_option( 'mincalendar', $option );
	}

}
