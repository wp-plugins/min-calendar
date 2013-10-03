<?php
/**
 * MC_Appearance
 *
 * カレンダーのAppearance設定
 *
 * appearanceは全カレンダーで共通。
 * 個別に設定することはできない。
 *
 */
class MC_Appearance
{
    /**
     * @var array $options mincalendar-optionsをキーとしてwp_optionsへ設定する配列
     */
    private $options = array();
    /**
     * @var array $errors 正しくない入力が行われたキーの配列
     */
    private $errors  = array();
    /**
     * @var string $css CSS文字列
     */
    private $css = '';


    /**
     * options配列アップデート
     *
     * wp_optionsのmincalendar-optionにoptions配列をjson形式に変換して値を設定
     *
     * @param string $key mincalendar-option
     * @param string $referer update-appearance
     * @return bool
     */
    private function update( $key = 'mincalendar-options', $referer = 'update-appearance' )
    {
        if ( true === empty( $this->options ) ) {
            return false;
        }
        check_admin_referer( $referer );
        update_option( $key, json_encode( $this->options ) );
    }


    /**
     * options連想配列にkey/value pairを追加
     *
     * @param $key   optionsのキー
     * @param $value 設定値
     */
    private function add_options( $key, $value )
    {
        if ( true === array_key_exists( $key, $this->options )  ) {
            $this->options[ $key ] = $value;
        } else {
            $this->options += array(
                $key =>$value
            );
        }

    }


    /**
     * フォームの入力値を検査しadd_option関数へ渡す
     *
     * @param string $key   更新対象のキー
     * @param string $value 更新する値
     * @return mix          キーがあれば検査済み値、無いときはfalse
     */
    private function prepare( $key , $value = null )
    {
        if ( false === isset( $_POST[ $key ] ) ) {
            return false;
        }
        if ( true === is_null( $value ) ) {
            $value = $_POST[ $key ];
            $this->add_options( $key, $value );
        } else {
            $this->add_options( $key, $value );
        }
        return $value;
    }


    /**
     * CSS幅・高さの検査・設定
     *
     * @param string $key       options配列キー
     * @param string $selector  CSSセレクタ名
     * @param string $property  CSS設定値
     * @return bool 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
     */
    private function prepare_size( $key, $selector, $property )
    {
        if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
            return false;
        }

        $value = $_POST[ $key ];

        if ( true === MC_Validation::is_size( $value ) ) {
            $value = MC_Validation::normalize_size( $value );
            $value = $this->prepare( $key, $value );
            $this->css .=  $selector . ' { ' . $property . ': ' . $value . '; }' . PHP_EOL;
            return true;
        }

        $this->errors[ $key ] = __( 'The inputted value is not right.', 'mincalendar' );
        return false;
    }


    /**
     * CSSボーダーの検査・設定
     *
     * @param string $key options配列キー
     * @param string $selector CSSセレクタ
     * @return boolean 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
     */
    private function prepare_border( $key, $selector )
    {
        if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
            return false;
        }

        $value = $_POST[ $key ];

        if ( true === MC_Validation::is_color( $value ) ) {
            $value = MC_Validation::normalize_color( $value );
            $value = $this->prepare( $key, $value );
            $this->css .=  $selector . '{ ' . 'border: 1px solid ' . $value . '; }' . PHP_EOL;
            return true;
        }

        $this->errors[ $key ] = __( 'The inputted value is not right.', 'mincalendar' );
        return false;
    }


    /**
     * CSSカラーの検査・設定
     *
     * @param string $key      options配列キー
     * @param string $selector CSSセレクタ名
     * @param string $property CSS設定値
     * @return boolean 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
     */
    private function prepare_color( $key, $selector, $property )
    {
        if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
            return false;
        }

        $value = $_POST[ $key ];
        if ( true === MC_Validation::is_color( $value ) ) {
            $value = MC_Validation::normalize_color( $value );
            $value = $this->prepare( $key , $value );
            $this->css .=  $selector . '{ ' . $property . ': '. $value . '; }' . PHP_EOL;
            return true;
        }
        $this->errors[ $key ] = __( 'The inputted value is not right.', 'mincalendar' );
        return false;
    }


    /**
     * 配置を設定
     *
     * @param string $key      options配列キー
     * @param string $selector CSSセレクタ名
     * @param string $property CSS設定値
     * @return boolean 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
     */
    private function prepare_align( $key, $selector, $property )
    {
        if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
            return false;
        }

        $value = $_POST[ $key ];

        if ( true === MC_Validation::is_align( $value ) ) {
            $value = $this->prepare( $key , $value );
            $this->css .=  $selector . '{ ' . $property . ': '. $value . '; }' . PHP_EOL;
            return true;
        }

        $this->errors[ $key ] = __( 'The inputted value is not right.', 'mincalendar' );
        return false;
    }


    /**
     * 入力用コントロール作成
     *
     * @param string $name    表示名
     * @param string $key     name属性値
     * @param string $example 入力例
     * @return string 作成コントロールのマークアップ
     */
    private function create_field( $name, $key, $example = '' )
    {
        $value   = esc_attr( isset( $this->options[ $key ] ) ? $this->options[ $key ] : '' );
        $error   = esc_attr( isset( $this->errors[ $key ] ) ? $this->errors[ $key ] : '' );
        $markup  = '<tr>' . PHP_EOL;
        $markup .= '<th>' . $name . '</th>' . PHP_EOL;
        $markup .= '<td><input type="text" name="' . $key . '" value="' . $value . '" size="8">'
            . '&nbsp;<span class="mc-example">' . $example . '</span>'
            . '&nbsp;<span class="mc-error">' . $error . '</span></td>' . PHP_EOL;
        $markup .= '</tr>' . PHP_EOL;
        return $markup;
    }


    /**
     * カレンダーのappearance設定画面
     */
    public function admin_appearance_page()
    {
        wp_enqueue_style(
            'mincalendar-appearance',
            MC_Utilities::mc_plugin_url( 'admin/css/appearance.css' )
        );

        if ( ! current_user_can( 'edit' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page' ) );
            return false;
        }


        /*
         * 更新
         */

        // 曜日見出し
        $this->prepare( 'mc-sun' );
        $this->prepare( 'mc-mon' );
        $this->prepare( 'mc-tue' );
        $this->prepare( 'mc-wed' );
        $this->prepare( 'mc-thu' );
        $this->prepare( 'mc-fri' );
        $this->prepare( 'mc-sat' );
        $this->prepare( 'mc-value-1st' );
        $this->prepare( 'mc-value-2nd' );
        $this->prepare( 'mc-value-3rd' );

        // 日曜・土曜の見出しカラー
        $this->prepare_color( 'mincalendar-th-sun', '.mincalendar-th-sun', 'color');
        $this->prepare_color( 'mincalendar-th-sat', '.mincalendar-th-sat', 'color');

        // 幅・高さ
        $this->prepare_size( 'mc-width-th', '.mincalendar th', 'width' );
        $this->prepare_size( 'mc-height-th', '.mincalendar th', 'height' );
        $this->prepare_size( 'mc-width-td', '.mincalendar td', 'width' );
        $this->prepare_size( 'mc-height-td', '.mincalendar td', 'height' );

        // table th border color
        $this->prepare_border( 'mc-border-th', '.mincalendar th ' );
        $this->prepare_border( 'mc-border-td', '.mincalendar td ' );

        // table td background color
        $this->prepare_color( 'mc-bgcolor-1st', '.mc-bgcolor-1st', 'background' );
        $this->prepare_color( 'mc-bgcolor-2nd', '.mc-bgcolor-2nd', 'background' );
        $this->prepare_color( 'mc-bgcolor-3rd', '.mc-bgcolor-3rd', 'background' );

        // text align
        $this->prepare_align( 'mc-text-align', '.mincalendar th, .mincalendar td ', 'text-align' );

        // 日付に関連づける記事のタグ
        $this->prepare( 'mc-tag' );

        // wp_options更新
        $this->update( 'mincalendar-options', 'update-appearance' );


        /*
         * cssファイル書き出し
         */
        if ( '' !== $this->css ) {
            // create mincalendar.css
            $fp = fopen( MC_CALENDAR_STYLESHEET, 'w' ) or die( __( 'Can not open file' ) );
            flock( $fp, LOCK_EX );
            fputs( $fp, $this->css );
            flock( $fp, LOCK_UN );
            fclose( $fp );
        }

        if ( false === empty( $_POST )  && $this->css == '' ) {
            // delete mincalendar.css
            if ( true === file_exists( MC_CALENDAR_STYLESHEET ) ) {
                unlink( MC_CALENDAR_STYLESHEET );
            }
        }


        /*
         * 設定値取得
         */
        $this->options = (array ) json_decode( get_option( 'mincalendar-options' ) );


        /*
         * 表示
         */
        ?>
    <div class="wrap">
        <h2>Min Calendar Appearance</h2>
        <form method="post" action="">
            <?php wp_nonce_field( 'update-appearance' ); ?>
            <table class="form-table">
                <?php echo $this->create_field( 'sunday', 'mc-sun' ) ; ?>
                <?php echo $this->create_field( 'monday', 'mc-mon' ) ; ?>
                <?php echo $this->create_field( 'tuesday', 'mc-tue' ) ; ?>
                <?php echo $this->create_field( 'wednesday', 'mc-wed' ) ; ?>
                <?php echo $this->create_field( 'thursday', 'mc-thu' ) ; ?>
                <?php echo $this->create_field( 'friday', 'mc-fri' ) ; ?>
                <?php echo $this->create_field( 'saturday', 'mc-sat' ) ; ?>
                <?php echo $this->create_field( 'sunday heading text color', 'mincalendar-th-sun', '(e.g #ff0000)' ) ; ?>
                <?php echo $this->create_field( 'satday heading text color', 'mincalendar-th-sat', '(e.g #0000ff)' ) ; ?>
                <?php echo $this->create_field( '1st value', 'mc-value-1st', '(e.g -)' ) ; ?>
                <?php echo $this->create_field( '2nd value', 'mc-value-2nd', '(e.g o)' ) ; ?>
                <?php echo $this->create_field( '3rd value', 'mc-value-3rd', '(e.g x)' ) ; ?>
                <?php echo $this->create_field( 'th width', 'mc-width-th', '(e.g 30px)' ) ; ?>
                <?php echo $this->create_field( 'th height', 'mc-height-th', '(e.g 30px)' ) ; ?>
                <?php echo $this->create_field( 'td width', 'mc-width-td', '(e.g 30px)' ) ; ?>
                <?php echo $this->create_field( 'td height', 'mc-height-td', '(e.g 30px)' ) ; ?>
                <?php echo $this->create_field( 'th border', 'mc-border-th', '(e.g #000000)' ) ; ?>
                <?php echo $this->create_field( 'td border', 'mc-border-td', '(e.g #000000)' ) ; ?>
                <?php echo $this->create_field( 'background color of 1st value', 'mc-bgcolor-1st', '(e.g #ffffff)' ) ; ?>
                <?php echo $this->create_field( 'background color of 2nd value', 'mc-bgcolor-2nd', '(e.g #ffffff)' ) ; ?>
                <?php echo $this->create_field( 'background color of 3rd value', 'mc-bgcolor-3rd', '(e.g #ffffff)' ) ; ?>
                <?php echo $this->create_field( 'text align', 'mc-text-align', '(left or center or right)' ) ; ?>
                <?php echo $this->create_field( 'tag attached for search post', 'mc-tag', '(only one tag)' ) ; ?>
            </table>
            <p class="submit"><input type="submit" class="button-primary" value="<?php echo esc_attr( __('Save', 'mincalendar') ); ?>" /></p>
        </form>
    </div><!-- wrap -->
    <?php
    }
}
