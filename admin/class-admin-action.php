<?php
/**
 * MC_Admin_Action
 *
 * Manage post form action.
 *
 * @property MC_Post_Wrapper $post_wrapper
 */
class MC_Admin_Action
{

	/** @var MC_Post_Wrapper */
	public $post_wrapper;

	function __construct()
	{
		$this->post_wrapper = null;
	}

	/**
	 * get $post_wrapper
	 */
	public function get_post_wrapper()
	{
		return $this->post_wrapper;
	}

	/**
	 * 管理画面のmincalendarページを表示するときの処理
	 *
	 * mincalendarページをロードしたときの処理順序
	 * 1. 当メソッド(manage_post)<br>
	 * 2. MC_Admin_Controller->admin_management_page
	 *
	 * MC_Admin_Controller->admin_management_pageより先に処理される。
	 */
	public function manage_post()
	{

		$action = MC_Admin_Utility::get_current_action();
		// save
		if ( 'save' === $action ) {
			$this->save();
		}
		// copy
		if ( 'copy' === $action ) {
			$this->copy();
		}
		// delete
		if ( 'delete' == $action ) {
			$this->delete();
		}
		$post_id = isset( $_GET['postid'] ) ? (int) $_GET['postid'] : '';
		$post_wrapper = null;

		// new
		if ( 'new' === $action && current_user_can( 'edit' ) ) {
			$post_wrapper = MC_Post_Factory::get_post_wrapper();
		}

		// edit
		if ( 'edit' === $action ) {
			$post_wrapper = MC_Post_Factory::get_post_wrapper( $post_id );
		}

		if ( $post_wrapper && current_user_can( 'edit', $post_wrapper->id ) ) {
			$this->post_wrapper = $post_wrapper;
		} else {
			// initial load
			$current_screen = get_current_screen();
			add_filter(
				'manage_' . $current_screen->id . '_columns',
				array( 'MC_List_Table', 'define_columns' )
			);
		}

	}


	/**
	 * 投稿の保存
	 *
	 * 投稿の識別ID{$id}は新規投稿は-1, 既存投稿はpost->IDになる
	 */
	private function save()
	{
		// new post id that is not saved is -1
		$id = $_POST['post_id'];

		check_admin_referer( 'save_' . $id );

		if ( false === current_user_can( 'edit', $id ) ) {
			wp_die( __( 'You are not allowed to edit this item.', 'mincalendar' ) );
		}

		$this->post_wrapper = MC_Post_Factory::get_post_wrapper( $id );
		if ( false === $this->post_wrapper ) {
			$this->post_wrapper          = MC_Post_Factory::get_post_wrapper();
			$this->post_wrapper->initial = true;
		}

		$this->post_wrapper->title = trim( $_POST['mincalendar-title'] );

		$query           = array();
		$query['action'] = 'edit';

		// new post data insert wp_posts so that ID number of wp_posts is defined
		$this->post_wrapper->save();

		// update custom field
		MC_Custom_Field::update_field( $this->post_wrapper );

		$query['postid'] = $this->post_wrapper->id;
		$redirect_to     = add_query_arg( $query, menu_page_url( 'mincalendar', false ) );
		wp_safe_redirect( $redirect_to );

		exit();
	}


	/**
	 * 投稿をコピー
	 */
	private function copy()
	{
		$id = empty( $_POST['post_id'] )
			? absint( $_REQUEST['postid'] )
			: absint( $_POST['post_id'] );

		check_admin_referer( 'copy_' . $id );

		if ( ! current_user_can( 'edit', $id ) ) {
			wp_die( __( 'You are not allowed to edit this item.', 'mincalendar' ) );
		}

		$query = array();

		if ( $this->post_wrapper = MC_Post_Factory::get_post_wrapper( $id ) ) {
			$new_post_wrapper = $this->post_wrapper->copy();
			$new_post_wrapper->save();
			$query['postid'] = $new_post_wrapper->id;
		} else {
			$query['postid'] = $this->post_wrapper->id;
		}

		$redirect_to = add_query_arg( $query, menu_page_url( 'mincalendar', false ) );

		wp_safe_redirect( $redirect_to );

		exit();

	}

	/**
	 * 投稿の削除
	 */
	private function delete()
	{
		if ( false === empty( $_POST['post_id'] ) ) {
			check_admin_referer( 'delete_' . $_POST['post_id'] );
		} elseif ( false === is_array( $_REQUEST['postid'] ) ) {
			check_admin_referer( 'delete_' . $_REQUEST['postid'] );
		} else {
			// bulk-postidsのpostidsはMC_List_Tableでpluralに指定した値
			check_admin_referer( 'bulk-postids' );
		}

		$post_ids = empty( $_POST['post_id'] )
			? (array) $_REQUEST['postid']
			: (array) $_POST['post_id'];

		$deleted = 0;

		foreach ( $post_ids as $post_id ) {
			$post_wrapper = MC_Post_Factory::get_post_wrapper( $post_id );

			if ( empty( $post_wrapper ) ) {
				continue;
			}

			if ( ! current_user_can( 'delete', $post_wrapper->id ) ) {
				wp_die( __( 'You are not allowed to delete this item.', 'mincalendar' ) );
			}

			if ( ! $post_wrapper->delete() ) {
				wp_die( __( 'Error in deleting.', 'mincalendar' ) );
			}

			$deleted += 1;
		}
		$query = array();
		$redirect_to = add_query_arg( $query, menu_page_url( 'mincalendar', false ) );
		wp_safe_redirect( $redirect_to );
		exit();
	}
}
