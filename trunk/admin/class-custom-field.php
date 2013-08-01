<?php
/**
 * MC_Custom_Field
 *
 * カレンダー投稿のカスタムフィールド
 */
class MC_Custom_Field
{

    /**
     * 日付入力などフィールド処理
     *
     * @param MC_Post_Wrapper $post_wrapper
     * @param string $html markup
     */
    public static function set_field( $post_wrapper, $html )
    {
        /*
         *  カスタムフィールドの値取得
         */
        // 年, 月既存値取得
        $year  = (int) get_post_meta( $post_wrapper->id, 'year', true );
        $month = (int) get_post_meta( $post_wrapper->id, 'month', true );

        // 既存値がないときは今日の日付を設定
        $today = getdate();
        $year  = ( false === empty( $year ) ) ? $year : (int) $today[ 'year' ];
        $month = ( false === empty( $month ) ) ? $month : (int) $today[ 'mon' ];

        // 曜日既存値取得
        $days = array_shift( MC_Date::get_days( $year, $month, true ) );
        $total = count( $days );

        // 日付既存値取得
        $date  = array();
        for ( $i = 1; $i <= $total; $i++ ) {
            $key = 'date-' . $i;
            $date[ $i ] = get_post_meta( $post_wrapper->id, $key, true );
        }

        // 記事既存値取得
        $related_posts  = array();
        for ( $i = 1; $i <= $total; $i++ ) {
            $key = 'post-' . $i;
            $related_posts[ $i ] = get_post_meta( $post_wrapper->id, $key, true );
        }


        /*
         * カスタムフィールド表示
         */
        $html .= '<div id="fields">';
        // 年
        $html .= '<div id="fields_year_month">' . PHP_EOL
            . __( 'Year', 'mincalendar' )
            . ' : <select name="year"><option value="-">--</option>';
        for ( $y = 2000; $y < 2050; $y++ ){
            if ( $y ===  $year ) {
                $html .= '<option value="' . $year . '" selected="selected">' . $year. '</option>';
            } else {
                $html .= '<option value="' . $y . '">' . $y . '</option>';
            }
        }
        $html .= '</select>&nbsp;';

        // 月
        $html .= 'Month : <select name="month"><option value="-">--</option>';
        for ( $m = 1; $m < 13; $m++ ){
            if ( $m ===  $month ) {
                $html .= '<option value="' . $month . '" selected = "selected">' . $month. '</option>';
            } else {
                $html .= '<option value="' . $m . '">' . $m . '</option>';
            }
        }
        $html .= '</select></div><!-- mincalendar_fileds_yearandmonth -->' . PHP_EOL;


        /*
         * 各日付処理
         */
        $html .= '<div id="fields_date">';

        // wp_option取得
        $options = (array) json_decode( get_option( 'mincalendar-options' ) );
        $context1 = ( true === isset( $options[ 'mc-value-1st' ] ) ) ? $options[ 'mc-value-1st' ] : '';
        $context2 = ( true === isset( $options[ 'mc-value-2nd' ] ) ) ? $options[ 'mc-value-2nd' ] : 'o';
        $context3 = ( true === isset( $options[ 'mc-value-3rd' ] ) ) ? $options[ 'mc-value-3rd' ] : 'x';
        $tag      = ( true === isset( $options[ 'mc-tag' ] ) ) ? $options[ 'mc-tag' ] : false;
        for ( $i = 1; $i <= $total; $i++ ) {
            $html .= '<div class="field">' . PHP_EOL;
            $html .= '<div class="cell cell-date">';
            if ( 'mc-value-1st' === $date[ $i ] ) {
                $html .=  '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
                    . ' : <select name="date-' . $i . '">'
                    . '<option value="mc-value-1st" selected="selected">' . esc_html( $context1 ) . '</option>'
                    . '<option value="mc-value-2nd">' . esc_html( $context2 ) . '</option>'
                    . '<option value="mc-value-3rd">' . esc_html( $context3 ) . '</option>'
                    . '</select>';
            } else if ( 'mc-value-2nd' === $date[ $i ] ) {
                $html .=  '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
                    . ' : <select name="date-' . $i . '">'
                    . '<option value="mc-value-1st">' . esc_html( $context1 ) . '</option>'
                    . '<option value="mc-value-2nd" selected="selected">' . esc_html( $context2 ) . '</option>'
                    . '<option value="mc-value-3rd">' . esc_html( $context3 ) . '</option>'
                    . '</select>';
            } else if ( 'mc-value-3rd' === $date[ $i ] ) {
                $html .=  '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
                    . ' : <select name="date-' . $i . '">'
                    . '<option value="mc-value-1st">' . esc_html( $context1 ) . '</option>'
                    . '<option value="mc-value-2nd">' . esc_html( $context2 ) . '</option>'
                    . '<option value="mc-value-3rd" selected="selected">' . esc_html( $context3 ) . '</option>'
                    . '</select>';
            }
            else {
                $html .=  '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
                    . ' : <select name="date-' . $i . '">'
                    . '<option value="mc-value-1st" selected="selected">' . esc_html( $context1 ) . '</option>'
                    . '<option value="mc-value-2nd">' . esc_html( $context2 ) . '</option>'
                    . '<option value="mc-value-3rd">' . esc_html( $context3 ) . '</option>'
                    . '</select>';
            }
            $html .= '</div><!-- cell-date -->';

            // 関連記事
            if ( false === empty( $tag ) ) {
                $myposts = get_posts( array(
                    'numberposts' => 100,
                    'tag'    => $tag
                ) );
                $html .= '<div class="cell cell-post">' . PHP_EOL;
                $html .= 'post: <select name="post-' . $i . '">'. PHP_EOL;
                $html .= '<option value="-">--</option>';
                foreach( $myposts as $mypost ) {
                    if ( $related_posts[ $i ] == $mypost->ID ) {
                        $html .= '<option value="' . $mypost->ID . '" selected="selected">' . $mypost->post_title . '</option>';
                    } else {
                        $html .= '<option value="' . $mypost->ID . '">' . $mypost->post_title . '</option>';
                    }
                }
                $html .= '</select>' . PHP_EOL;
                $html .= '</div><!-- cell-post -->' . PHP_EOL;
            }

            $html .= '</div><!-- field -->';
        }
        $html .= '</div><!-- fields-date -->' . PHP_EOL
            . '</div><!-- fields -->' . PHP_EOL
            . '</div><!-- poststuff -->' . PHP_EOL
            . '</form>' . PHP_EOL
            . '</div>';
        $post_wrapper->set_html( $html );
    }


    /**
     * 保存処理
     */
    public static function update_field( $post_wrapper )
    {
        $post_id = $post_wrapper->id;
        // セキュリティチェック
        $nonce = isset( $_POST[ '_wpnonce' ] ) ? $_POST[ '_wpnonce' ] : null;
        if ( false === wp_verify_nonce( $nonce, 'save_' . $post_id )
            && false === wp_verify_nonce( $nonce, 'save_' . -1 )
        ) {
            return $post_id;
        }

        /*
         * 値取得
         */
        $year  = (int) $_POST[ 'year' ];
        $month = (int) $_POST[ 'month' ];

        $days = MC_Date::get_days( $year, $month, true );
        $total = count( $days[ 'days' ] );

        $date  = array();
        for ( $i = 1; $i <= $total; $i++ ) {
            $key = 'date-' . $i;
            if ( isset( $_POST[ $key ] ) ) {
                $date[ $i ] = $_POST[ $key ];
            } else {
                $date[ $i ] = '';
            }
        }

        $related_posts = array();
        for ( $i = 1; $i <= $total; $i++ ) {
            $key = 'post-' . $i;
            if ( isset( $_POST[ $key ] ) ) {
                $related_posts[ $i ] = $_POST[ $key ];
            } else {
                $related_posts[ $i ] = '';
            }
        }

        /*
         * 更新
         */
        if( '' === $year ) {
            delete_post_meta( $post_id, 'year' );
        } else {
            update_post_meta( $post_id, 'year', $year );
        }
        if( '' === $month ) {
            delete_post_meta( $post_id, 'month' );
        } else {
            update_post_meta( $post_id, 'month', $month );
        }
        for ( $i = 1; $i <= $total; $i++ ) {
            $key = 'date-' . $i;
            if ( '' === $date[ $i ] ) {
                delete_post_meta( $post_id, $key );
            } else {
                update_post_meta( $post_id, $key, $date[ $i ]);
            }
        }
        for ( $i = 1; $i <= $total; $i++ ) {
            $key = 'post-' . $i;
            if ( '' === $related_posts[ $i ] ) {
                delete_post_meta( $post_id, $key );
            } else {
                update_post_meta( $post_id, $key, $related_posts[ $i ]);
            }
        }
    }
}