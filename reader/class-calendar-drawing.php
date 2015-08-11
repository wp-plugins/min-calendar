<?php

/**
 * カレンダー描画
 *
 * ショートタグmincalendarのカレンダーを出力します。
 */
class MC_Calendar_Drawing
{

	/*
	 * カレンダー描画処理
	 * 
	 */
	public function run()
	{
		add_action( 'plugins_loaded', array( &$this, 'add_shortcodes' ), 1 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles_base' ) );
	}

	/**
	 * ショートコード実行
	 */
	function add_shortcodes()
	{
		add_shortcode( 'mincalendar', array( &$this, 'draw' ) );
	}

	/**
	 * ショートコードのカレンダー描画
	 *
	 * [mincalendar id="1745" title="test"]
	 *
	 * @param $atts
	 *   id is post id, title is post title
	 * @param null $content
	 * @param string $code mincalendar
	 * @return string calendar html markup
	 */
	public function draw( $atts, $content = null, $code = '' )
	{
		if ( is_feed() ) {
			return '[mincalendar]';
		}

		if ( 'mincalendar' === $code ) {
			$atts = shortcode_atts(
				array( 'id' => 0 ),
				$atts
			);
			$id   = (int) $atts['id'];
		}

		// make calendar markup
		$unit_tag = 'mincalendar-' . $id;
		$html     = '<div id="' . $unit_tag . '" class="mincalendar">';
		/*
		 * ショートコードのからカレンダーを作成
		 */
		$html .= MC_Calendar_Maker::draw( $id );
		$html .= '</div>';

		return $html;

	}

	/**
	 * 外観設定した値のスタイル出力
	 */
	function enqueue_styles()
	{
		wp_enqueue_style(
			'mincalendar',
			MC_PLUGIN_URL . '/reader/css/mincalendar.css',
			array(),
			MC_VERSION,
			'all'
		);
		do_action( 'enqueue_styles' );
	}

	/**
	 * min-calendarの基本スタイル出力
	 */
	function enqueue_styles_base()
	{
		wp_enqueue_style(
			'mincalendar-base',
			MC_PLUGIN_URL . '/reader/css/mincalendar-base.css',
			array(),
			MC_VERSION,
			'all'
		);
		do_action( 'enqueue_styles_base' );
	}

}
