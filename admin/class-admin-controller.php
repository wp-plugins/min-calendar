<?php

/**
 * MC_Admin_Controller
 *
 * @property MC_List_Table $list_table
 * @property MC_Post_Form $form
 * @property MC_Appearance $appearance
 * @property MC_Manage_Form_Action $form_action
 */
class MC_Admin_Controller
{
	/** @var MC_Post_Form */
	public $form;
	/** @var MC_Manage_Form_Action */
	public $form_action;
	/** @var MC_Appearance */
	public $appearance;
	/** @var MC_List_Table */
	public $list_table;

	function __construct()
	{
		$this->form        = new MC_Post_Form();
		$this->form_action = new MC_Manage_Form_Action();
		$this->appearance  = new MC_Appearance();
	}

	/**
	 * 管理画面作成
	 */
	public function setup()
	{
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Min Calendar関連ページ作成
	 */
	public function admin_menu()
	{
		if ( current_user_can( 'edit' ) ) {
			// メニュー追加
			add_object_page(
				'Min Calendar',
				'Min Calendar',
				'read',
				'mincalendar',
				array( $this, 'admin_management_page' )
			);
			// 一覧ページ追加
			$list_page = add_submenu_page(
				'mincalendar',
				__( 'Edit Calendar', 'mincalendar' ),
				__( 'Edit', 'mincalendar' ),
				'read',
				'mincalendar',
				array( $this, 'admin_management_page' )
			);
			// MC_Manage_Form_Action->manage_postをコールバックに設定
			add_action( 'load-' . $list_page, array( $this->form_action, 'manage_post' ) );
			// 外観ページ(カレンダーオプション)
			add_submenu_page(
				'mincalendar',
				__( 'Edit Appearance', 'mincalendar' ),
				__( 'Appearance', 'mincalendar' ),
				'read',
				'mincalenar-appearance',
				array( $this->appearance, 'admin_appearance_page' )
			);
		}
	}

	/**
	 * Min Calendarページ管理
	 *
	 * 既存の投稿は編集画面表示しその他はリストを表示します。
	 */
	public function admin_management_page()
	{
		$post_wrapper = $this->form_action->get_post_wrapper();
		if ( $post_wrapper ) {
			// 編集画面
			echo $this->get_edit_page( $post_wrapper );
		} else {
			// 一覧画面
			echo $this->get_list_page();
		}
	}

	/**
	 * 編集画面マークアップ取得
	 *
	 * @return string マークアップ
	 */
	public function get_edit_page( $post_wrapper )
	{
		$form = new MC_Post_Form();
		$html = $form->get_form( $post_wrapper );
		MC_Custom_Field::set_field( $post_wrapper, $html );
		// get_htmlはエスケープ済みマークアップを返す
		return $post_wrapper->get_html();
	}

	/**
	 * 一覧画面マークアップ取得
	 *
	 * @return string マークアップ
	 */
	public function get_list_page()
	{
		$this->list_table = new MC_List_Table();
		$this->list_table->prepare_items();

		$html = '<div class="wrap">' . PHP_EOL
			. '<h2>' . PHP_EOL
			. 'Min Calendar' . ' <a href="admin.php?page=mincalendar&action=new">'
			. esc_html( __( 'Add New', 'mincalendar' ) ) . '</a>';

		if ( ! empty( $_REQUEST['s'] ) ) {
			$html .= sprintf(
				'<span class="subtitle">'
				. __( 'Search results for &#8220;%s&#8221;', 'mincalendar' )
				. '</span>',
				esc_html( $_REQUEST['s'] )
			);
		}
		$html .= '</h2>' . PHP_EOL
			. '<form method="get" action="">' . PHP_EOL
			. '<input type="hidden" name="page" value="' . esc_attr( $_REQUEST['page'] ) . '" />' . PHP_EOL
			. $this->list_table->search_box( __( 'Search Calendar', 'mincalendar' ), 'mincalendar' );

		ob_start();
		$this->list_table->display();
		$html .= ob_get_contents();
		ob_clean();

		$html .= '</form>' . PHP_EOL . '</div>';

		return $html;
	}

	/**
	 * 安全にJavaScriptを呼び出す処理
	 */
	public function admin_enqueue_scripts( $hook_suffix )
	{
		if ( false === strpos( $hook_suffix, 'mincalendar' ) ) {
			return;
		}

		wp_enqueue_style(
			'mincalendar-admin',
			MC_PLUGIN_URL . '/admin/css/styles.css',
			array( 'thickbox' ),
			MC_VERSION,
			'all'
		);

		wp_enqueue_script(
			'mincalendar-admin-scripts',
			MC_PLUGIN_URL . '/admin/js/scripts.js',
			array(
				'jquery',
				'thickbox',
				'postbox'
			),
			MC_VERSION,
			true
		);
		wp_enqueue_script(
			'mincalendar-admin',
			MC_PLUGIN_URL . '/admin/js/admin.js',
			array(),
			MC_VERSION,
			true
		);

		wp_enqueue_script(
			'mincalendar-admin-custom-fields',
			MC_PLUGIN_URL . '/admin/js/custom_fields.js',
			array(),
			MC_VERSION,
			true
		);

		wp_enqueue_script(
			'mincalendar-admin-custom-fields_handler',
			MC_PLUGIN_URL . '/admin/js/custom_fields_handler.js',
			array(),
			MC_VERSION,
			true
		);
	}
}
