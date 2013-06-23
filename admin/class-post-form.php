<?php
/**
 * MC_Post_Form
 *
 * カレンダー投稿フォーム
 */
class MC_Post_Form
{
    /**
     * @param MC_Post_Wrapper $post_wrapper
     * @return string エスケープ済処済み投稿フォームのマークアップ
     */
    public function get_form( $post_wrapper )
    {
        return $this->form( $post_wrapper );
    }


    /**
     * @param MC_Post_Wrapper $post_wrapper
     * @return string 投稿フォームマークアップ
     */
    private function form( $post_wrapper )
    {
        // 新規投稿のpost_idは-1。その他は投稿ID。
        $post_id = ( is_null( $post_wrapper->id ) ) ? -1 : $post_wrapper->id;

        $html = '<div class="wrap">' . PHP_EOL
            . '<h2>' . esc_html( __( 'Min Calendar Post', 'mincalendar' ) ) . '</h2>';

        if ( false === empty( $post_wrapper ) ) {
            if ( current_user_can( 'edit', $post_id ) ) {
                $disabled = '';
            } else {
                $disabled = ' disabled="disabled"';
            }
        }
        $action = esc_url( add_query_arg(
            array( 'postid' => $post_wrapper->id ),
            menu_page_url( 'mincalendar', false )
        ) );
        $html .= '<form method="post" action="' . $action . '" id="mincalendar-admin-form-element">' . PHP_EOL;

        if ( current_user_can( 'edit', $post_id ) ) {
            $nonce = wp_nonce_field( 'save_' . $post_id, '_wpnonce', true, false  );
            $html .= $nonce . PHP_EOL;
        }

        $html .= '<input type="hidden" id="post_id" name="post_id" value="' . (int) $post_id .'" />'
            . '<input type="hidden" id="mincalendar-id" name="mincalendar-id" value="'
            . (int) get_post_meta( $post_wrapper->id, '_old_MC_unit_id', true )
            . '" />' . PHP_EOL
            . '<input type="hidden" id="hiddenaction" name="action" value="save" />' . PHP_EOL
            . '<div id="poststuff" class="metabox-holder">' . PHP_EOL
            . '<div id="titlediv">' . PHP_EOL
            . '<input type="text" id="mincalendar-title" name="mincalendar-title" size="40" value="'
            . esc_attr( $post_wrapper->title ) . '"' . $disabled . ' />';

        if ( false === $post_wrapper->initial ) {
            $html .= '<p class="tagcode">' . PHP_EOL
                . esc_html( __( "Copy this code and paste it into your post, page or text widget content.", 'mincalendar' ) )
                . '<br />' . PHP_EOL
                . '<input type="text" id="mincalendar-anchor-text" onfocus="this.select();" readonly="readonly" />' . PHP_EOL
                . '</p>' . PHP_EOL
                .  '<p class="tagcode" style="display: none;">' . PHP_EOL
                .  esc_html( __( "Old code is also available.", 'mincalendar' ) ) . '<br>' . PHP_EOL
                .  '<input type="text" id="mincalendar-anchor-text-old" onfocus="this.select();" readonly="readonly" />' . PHP_EOL
                .  '</p>' . PHP_EOL;
        }
        if ( current_user_can( 'edit', $post_id ) ) {
            $html .= '<div class="save-mincalendar">' . PHP_EOL
                . '<input type="submit" class="button-primary" name="mincalendar-save" value="' . esc_attr( __( 'Save', 'mincalendar' ) ) . '" />' . PHP_EOL
                . '</div>';
        }

        // copy and delete link
        if ( current_user_can( 'edit', $post_id ) && false === $post_wrapper->initial )  {
            $copy_nonce   = wp_create_nonce( 'copy_' . $post_wrapper->id );
            $delete_nonce = wp_create_nonce( 'delete_' . $post_wrapper->id );

            $html .= '<div class="actions-link">' . PHP_EOL
                . '<input type="submit" name="mincalendar-copy" class="copy" value="' . esc_attr( __( 'Copy', 'mincalendar' ) ) . '"'
                . " onclick=\"this.form._wpnonce.value = '" . $copy_nonce . "'; this.form.action.value = 'copy'; return true;\"" ." />"
                . '&nbsp;|&nbsp;'
                // delete
                . '<input type="submit" name="delete" class="delete" value="' . esc_attr( __( 'Delete', 'mincalendar' ) ) . '"'
                . " onclick=\"if (confirm('" . esc_js( __( "You are about to delete this contact form.\n  'Cancel' to stop, 'OK' to delete.", 'mincalendar' ) )
                . "')) {this.form._wpnonce.value = '" . $delete_nonce . "'; this.form.action.value = 'delete'; return true;} return false;\"" . " />"
                . '</div>';
        }
        $html .= '</div><!-- titlediv -->';
        $html .= '</form>';

        return $html;

    }
}
