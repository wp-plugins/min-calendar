<?php

/**
 * MC_Admin_Controller
 *
 * @property MC_List_Table $list_table
 * @property MC_Post_Form $post_form
 * @property MC_Appearance $appearance
 * @property MC_Admin_Action $action
 */
class MC_Admin_Controller
{

	/** @var MC_Admin_Action */
	public $action;
	/** @var MC_Post_Form */
	public $porst_form;
	/** @var MC_Appearance */
	public $appearance;
	/** @var MC_List_Table */
	public $list_table;


	function __construct()
	{
		$this->porst_form = new MC_Post_Form();
		$this->action     = new MC_Admin_Action();
		$this->appearance = new MC_Appearance();
	}

	/**
	 * 管理画面処理
	 */
	public function setup() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * 管理画面へMin Calendarページ追加
	 */
	public function admin_menu()
	{
		if ( current_user_can( 'edit' ) ) {
			add_object_page(
				'Min Calendar',
				'Min Calendar',
				'read',
				'mincalendar',
				array( $this, 'admin_management_page' )
			);

			// カスタム投稿タイプmincalendarの一覧
			$post_list = add_submenu_page(
				'mincalendar',
				__( 'Edit Calendar', 'mincalendar' ),
				__( 'Edit', 'mincalendar' ),
				'read',
				'mincalendar',
				array( $this, 'admin_management_page' )
			);

			// MC_Admin_Action->manage_postをコールバックに設定
			add_action( 'load-' . $post_list, array( $this->action, 'manage_post' ) );

			// カレンダーオプション設定画面
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
	 * Min Calendarページ呼び出し
	 *
	 * 投稿の編集は編集画面表示しそれ他は投稿リストを表示する。
	 */
	public function admin_management_page()
	{
		$post_wrapper = $this->action->get_post_wrapper();

		// 編集処理
		if ( $post_wrapper ) {
			$post_form = new MC_Post_Form();
			$html      = $post_form->get_form( $post_wrapper );
			MC_Custom_Field::set_field( $post_wrapper, $html );
//            $this->add_meta_boxes();
//            do_meta_boxes( 'mincalendar', 'normal', array( $post_wrapper, $html ) );
			// get_htmlはエスケープ済みマークアップを返す
			echo $post_wrapper->get_html();
			return;
		}

		// リスト表示
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

		$html .= '</form>' . PHP_EOL
			. '</div>';

		echo $html;
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
			MC_Utilities::mc_plugin_url( 'admin/css/styles.css' ),
			array( 'thickbox' ),
			MC_VERSION,
			'all'
		);

		wp_enqueue_script(
			'mincalendar-admin-scripts',
			MC_Utilities::mc_plugin_url( 'admin/js/scripts.js' ),
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
			MC_Utilities::mc_plugin_url( 'admin/js/admin.js' ),
			array(),
			MC_VERSION,
			true
		);

		wp_enqueue_script(
			'mincalendar-admin-custom-fields',
			MC_Utilities::mc_plugin_url( 'admin/js/custom_fields.js' ),
			array(),
			MC_VERSION,
			true
		);

		wp_enqueue_script(
			'mincalendar-admin-custom-fields_handler',
			MC_Utilities::mc_plugin_url( 'admin/js/custom_fields_handler.js' ),
			array(),
			MC_VERSION,
			true
		);

	}

	/**
	 * 投稿フォームにカスタムフィールド挿入
	 *
	 * @param MC_POST_Wrapper $post_wrapper
	 * @param string $html カスタムフィールドのマークアップ
	 */
	private function add_meta_boxes()
	{
		add_meta_box(
			'mincalendar_meta_box_id',
			__( 'Min Calendar Meta Box', 'mincalendar' ),
			array( $this, 'set_field' ),
			'mincalendar',
			'normal'
		);
	}


	/**
	 * add_meta_boxのコールバック関数
	 *
	 * @param array $param do_meta_boxに指定した引数
	 */
	public function set_field( $params )
	{
		MC_Custom_Field::set_field( $params[0], $params[1] );
	}

}
