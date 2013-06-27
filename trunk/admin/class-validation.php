<?php
class MC_Validation
{

    /**
     * カラー表記が16進数かをチェック
     *
     * @param $text CSSから表記
     * @return bool 16進数表記ときはtrue, それ以外はfalse
     */
    public static function is_color( $color )
    {
        if ( 1 === preg_match( '/#?[ABCDEFabcdef0-9]{3,6}$/', $color ) ) {
            return true;
        };
        return false;
    }

    /**
     * 16進数カラー表記の先頭に#が無いとき追加
     *
     * @param $color 16進数カラー表記
     * @return mixed #を付けた16進数カラー表記
     */
    public static function normalize_color( $color ) {
        return preg_replace( "/^([^#]+)$/", "#$1", $color );
    }


    /**
     * サイズ指定が数字または数字+(px|em|%)の形かチェック
     *
     * @param $size CSSボックスモデルのサイズ
     * @return bool 正しい入力はtrue
     */
    public static function is_size( $size ) {
        if ( 1 === preg_match( '/^[0-9]+(px|em|%)?$/', $size ) ) {
            return true;
        }
        return false;
    }


    /**
     * 幅、縦の指定文字にpxがないとき追加
     *
     * @param $color 16進数カラー表記
     * @return mixed #を付けた16進数カラー表記
     */
    public static function normalize_size( $size, $unit = 'px' ) {
        if ( 1 === preg_match( '/^[0-9]+$/', $size ) ) {
             return $size . $unit ;
        }
        return $size;
    }


    /**
     * 配置指定チェック
     *
     * @param $align 配置プロパティの値
     * @return bool left, center, rightならtrue, それ以外はfalseを返す
     */
    public static function is_align( $align ) {
        if ( 1 === preg_match( '/^(left|center|right)$/', $align ) ) {
            return true ;
        }
        return false;
    }


}
