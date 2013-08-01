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
        $year   = (int) get_post_meta( $post_id, 'year' , true );
        $month  = (int) get_post_meta( $post_id, 'month', true );
        $res   = MC_Date::get_days( $year, $month , false );
        $days = $res[ 'days' ];
        $day_of_week = $res[ 'day_of_week' ];
        $total = count( $days );

        for ( $i = 1; $i <= $total; $i++ ) {
            $key_date         = 'date-' . $i;
            $key_post         = 'post-' . $i;
            $date[ $i ]       = get_post_meta( $post_id, $key_date, true );
            $relatepost[ $i ] = get_post_meta( $post_id, $key_post, true );
        }

        $html = self::make(
            $year,
            $month,
            $date,
            $day_of_week,
            $relatepost
        );
        return $html;
    }


    /**
     * 曜日出力用マークアップ作成
     *
     * @param string $y year yyyy
     * @param string $m month 1～13
     * @param string $date 日付情報(配列)
     * @param array $day_of_week 曜日のラベル
     * @param array $relatepost 曜日に紐づいた投稿
     * @return string 曜日のマークアップ
     */
    private static function make( $y, $m, $date, $day_of_week, $relatepost )
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
                // $key = 'date-' . $i;
                $option = $date[ $i ];
                $context   = '';
                $html .= "<td";
                // 曜日の取得
                $hizuke = mktime( 0, 0, 0, $m, $i, $y ); //$y年$m月$i日のUNIXTIME
                $youbi  = date( 'w', $hizuke ); // 1日の曜日（0:日～6:土）
                if ( $option === 'mc-value-1st' ) {
                    $html .= ' class="mc-bgcolor-1st';
                    if ( 0 === (int) $youbi ) {
                        $html .= ' mincalendar-td-sun';
                    } else if ( 6 === (int) $youbi ) {
                        $html .= ' mincalendar-td-sat';
                    }
                    $html .= '"';
                    $context = ( "\x20" === $options[ 'mc-value-1st' ] ) ? '&nbsp;' : $options[ 'mc-value-1st' ];
                } else if ( $option === 'mc-value-2nd' ) {
                    $html .= ' class="mc-bgcolor-2nd';
                    if ( 0 === (int) $youbi ) {
                        $html .= ' mincalendar-td-sun';
                    } else if ( 6 === (int) $youbi ) {
                        $html .= ' mincalendar-td-sat';
                    }
                    $html .= '"';
                    $context = ( "\x20" === $options[ 'mc-value-2nd' ] ) ? '&nbsp;' : $options[ 'mc-value-2nd' ];
                } else if ( $option === 'mc-value-3rd' ) {
                    $html .= ' class="mc-bgcolor-3rd"';
                    if ( 0 === (int) $youbi ) {
                        $html .= ' mincalendar-td-sun';
                    } else if ( 6 === (int) $youbi ) {
                        $html .= ' mincalendar-td-sat';
                    }
                    $html .= '"';
                    $context = ( "\x20" === $options[ 'mc-value-3rd' ] ) ? '&nbsp;' : $options[ 'mc-value-3rd' ];
                } else if ( 0 === (int) $youbi ) {
                    $html .= ' class="mincalendar-td-sun"';
                } else if ( 6 === (int) $youbi ) {
                    $html .= ' class="mincalendar-td-sat"';
                }
                if ( is_numeric( $relatepost[ $i ] ) ) {
                    $relate = get_post( $relatepost[ $i ] );
                    $link   = '<a target="_blank" href="' . get_permalink( $relatepost[ $i ] ) .'">' . $relate->post_title . '</a>';
                } else {
                    $link = '';
                }
                $html .= '>' . $i . '<br>' . esc_html( $context ) . '<br>';
                if ( false === empty( $link ) ) {
                    $html .=  $link;
                }
                $html .= '</td>' . PHP_EOL;
            } else {
                $html .= '<td>&nbsp;</td>' . PHP_EOL;
            }
            if ( ( ( $i + $w ) % 7 ) == 0 ) {
                $html .= '</tr>' . PHP_EOL;
                if ( $i >= $n ) {
                    break;
                }
            }
        }
        $html .= '</table>' . PHP_EOL;

        // $htmlはエスケープ済み
        return $html;
    }

}
