<?php
/**
 * MC_Draw_Calendar
 *
 * カレンダーの投稿IDから表示用マークアップ構築
 */
class MC_Draw_Calendar {

    /**
     * カレンダー表示用マークアップ作成
     *
     * @param $post_id カレンダーの投稿ID
     * @return string  カレンダー表示用マークアップ
     */
    public static function draw( $post_id )
    {
        $year        = (int) get_post_meta( $post_id, 'year' , true );
        $month       = (int) get_post_meta( $post_id, 'month', true );
        $res         = MC_Date::get_days( $year, $month , false );
        $days        = $res[ 'days' ];
        $day_of_week = $res[ 'day_of_week' ];
        $total = count( $days );

        for ( $i = 1; $i <= $total; $i++ ) {
            $key_date           = 'date-' . $i;
            $key_post           = 'post-' . $i;
            $key_text           = 'text-' . $i;
            $date[ $i ]         = get_post_meta( $post_id, $key_date, true );
            $relate_posts[ $i ] = get_post_meta( $post_id, $key_post, true );
            $texts[ $i ]        = get_post_meta( $post_id, $key_text, true );
        }

        $html = self::make(
            $year,
            $month,
            $date,
            $day_of_week,
            $relate_posts,
            $texts
        );
        return $html;
    }


    /**
     * 曜日出力用マークアップ作成
     *
     * @param string $y year yyyy
     * @param string $m month 1～13
     * @param array  $date 日付情報
     * @param array  $day_of_week 曜日のラベル
     * @param array  $relate_posts 曜日に紐づいた投稿
     * @param array  $texts テキスト
     * @return string 曜日のマークアップ
     */
    private static function make( $y, $m, $date, $day_of_week, $relate_posts, $texts )
    {
        // $y 年 $m 月
        $t     = mktime( 0, 0, 0, $m, 1, $y ); // $y年$m月1日のUNIXTIME
        $w     = date( 'w', $t );              // 1日の曜日（0:日～6:土）
        $n     = date( 't', $t );              // $y年$m月の日数
        if ( $m < 10 ) {
            $m = '0' . $m;
        }

        $sun = $day_of_week[0];
        $mon = $day_of_week[1];
        $tue = $day_of_week[2];
        $wed = $day_of_week[3];
        $thu = $day_of_week[4];
        $fri = $day_of_week[5];
        $sat = $day_of_week[6];

        $html = <<<HTML
    <table class="mincalendar">
    <caption>{$y} . {$m}</caption>
    <tr>
        <th class="mincalendar-th-sun">{$sun}</th>
        <th>{$mon}</th>
        <th>{$tue}</th>
        <th>{$wed}</th>
        <th>{$thu}</th>
        <th>{$fri}</th>
        <th class="mincalendar-th-sat">{$sat}</th>
    </tr>
HTML;

        // オプション取得
        $options = (array) json_decode( get_option( 'mincalendar-options' ) );
        for( $i = 1 - $w; $i <= $n + 7; $i++ ){
            if ( ( ( $i + $w) % 7) == 1) {
                $html .= "<tr>" . PHP_EOL;
            }
            // 日付が有効な場合の処理
            if ( ( 0 < $i ) && ( $i <= $n) ) {
                // 日付情報
                // $key = 'date-' . $i;
                $option = $date[ $i ];
                $context   = '';
                $html .= "<td";
                // 曜日の取得
                $hizuke = mktime( 0, 0, 0, $m, $i, $y ); //$y年$m月$i日のUNIXTIME
                $youbi  = date( 'w', $hizuke ); // 1日の曜日（0:日～6:土）

                // mc-valueの設定
<<<<<<< .mine
                if ( isset( $options[ 'mc-value-1st' ] ) && $option === 'mc-value-1st' ) {
                    $html .= self::set_holiday( '1st', $youbi);
=======
                if ( isset( $options[ 'mc-value-1st' ] ) && $option === 'mc-value-1st' ) {
                    $html .= self::set_holiday( '1st', $html, $youbi);
>>>>>>> .r798993
                    $context = ( "\x20" === $options[ 'mc-value-1st' ] ) ? '&nbsp;' : $options[ 'mc-value-1st' ];
<<<<<<< .mine
                } else if ( isset( $options[ 'mc-value-2nd' ] ) && $option === 'mc-value-2nd' ) {
                    $html .=  self::set_holiday( '2nd', $youbi);
=======
                } else if ( isset( $options[ 'mc-value-2nd' ] ) && $option === 'mc-value-2nd' ) {
                    $html .=  self::set_holiday( '2nd', $html, $youbi);
>>>>>>> .r798993
                    $context = ( "\x20" === $options[ 'mc-value-2nd' ] ) ? '&nbsp;' : $options[ 'mc-value-2nd' ];
<<<<<<< .mine
                } else if ( isset( $options[ 'mc-value-3rd' ] ) && $option === 'mc-value-3rd' ) {
                    $html .= self::set_holiday( '3rd', $youbi);
=======
                } else if ( isset( $options[ 'mc-value-3rd' ] ) && $option === 'mc-value-3rd' ) {
                    $html .= self::set_holiday( '3rd', $html, $youbi);
>>>>>>> .r798993
                    $context = ( "\x20" === $options[ 'mc-value-3rd' ] ) ? '&nbsp;' : $options[ 'mc-value-3rd' ];
                }

                // 紐づけた投稿
                if ( is_numeric( $relate_posts[ $i ] ) ) {
                    $relate = get_post( $relate_posts[ $i ] );
                    $link   = '<a target="_blank" href="' . get_permalink( $relate_posts[ $i ] ) .'">' . $relate->post_title . '</a>';
                } else {
                    $link = '';
                }

                // テキスト
                if ( '' !== $texts[ $i ] ) {
                    $text = nl2br( esc_html( $texts[ $i ] ) );
                } else {
                    $text = '';
                }

                // context
                $html .= '><div class="td-inner">';
                $html .= '<div class="mc-date">' . $i . '</div><div>' . esc_html( $context ) . '</div>';
                if ( false === empty( $link ) ) {
                    $html .= '<div class="mc-link">' . $link . '</div>' ;
                }
                if ( false === empty( $text ) ) {
                    $html .= '<div class="mc-text">' . $text . '</div>';
                }
                $html .= '</div></td>' . PHP_EOL;
            } else { // 日付が無効な場合
                $html .= '<td>&nbsp;</td>' . PHP_EOL;
            }
            if ( ( ( $i + $w ) % 7 ) == 0 ) {
                $html .= '</div></tr>' . PHP_EOL;
                if ( $i >= $n ) {
                    break;
                }
            }
        }
        $html .= '</table>' . PHP_EOL;

        // $htmlはエスケープ済み
        return $html;
    }


    /**
     * @param $index (1st, 2nd, 3rd)
     * @param $youbi 曜日 0:日, 6:土曜日
     * @return string マークアップ
     */
    private function set_holiday( $index, $youbi )
    {
        $html =' class="mc-bgcolor-' . $index;
        if ( 0 === (int) $youbi ) {
            $html .= ' mincalendar-td-sun';
        } else if ( 6 === (int) $youbi ) {
            $html .= ' mincalendar-td-sat';
        }
        $html .= '"';
        return $html;

    }

}
