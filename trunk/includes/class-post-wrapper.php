<?php
/**
 * MC_Post_Wrapper
 *
 * カレンダー投稿のラッパー
 *
 * 投稿が新規が既存かを判定するプロパティをもつ
 *
 * @property MC_Utilities $utilities
 */
class MC_Post_Wrapper {

    const post_type = 'mincalendar';

    public $initial = false;
    public $id;
    public $title;
    public $utilities;
    public $html;


    /**
     * カスタムフィールドのマークアップ設定
     *
     * @param string $html エスケープ処理済みマークアップ
     *
     */
    public function set_html( $html )
    {
        $this->html = $html;
    }


    /**
     * カスタムフィールドのマークアップ取得
     *
     * @return string $html エスケープ処理済みマークアップ
     */
    public function get_html()
    {
        return $this->html;
    }
    /**
     *
     */
    public function set_initial( $initial )
    {
        $this->initial = $initial;
    }

    /**
     *
     */
    public function set_id( $post_id )
    {
        $this->id = $post_id;
    }


    /**
     *
     */
    public function set_title( $title )
    {
        $this->title = $title;
    }


    /* Save */
    function save()
    {
        // get post id when post is new
        if ( true === $this->initial ) {
            $post_id = wp_insert_post( array(
                'post_type'   => self::post_type,
                'post_status' => 'publish',
                'post_title'  => $this->title
            ) );
        } else {
            $post_id = wp_update_post( array(
                'ID' => (int) $this->id,
                'post_status' => 'publish',
                'post_title' => $this->title
            ) );
        }

        if ( $post_id ) {
            if ( $this->initial ) {
                $this->initial = false;
                $this->id = $post_id;
            }
        }

        return $post_id;
    }


    /**
     * Copy
     *
     * Execute when MC_List_Table do copy
     */
    function copy()
    {
        $new = MC_Post_Factory::get_post_wrapper();
        $new->initial = true;
        $new->title = $this->title . '_copy';
        return $new;
    }


    /* Delete */
    function delete()
    {
        if ( true === $this->initial ) {
            return;
        }
        if ( wp_delete_post( $this->id, true ) ) {
            $this->initial = true;
            $this->id = null;
            return true;
        }
        return false;
    }

}
