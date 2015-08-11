<?php
/*
 * 曜日処理
 */
class MC_Day
{

	/**
	 * 曜日取得関数
	 *
	 * 年と月を指定して1日から末尾の曜日を取得
	 *
	 * @param number $year year
	 * @param number $month month
	 * @param bool $admin
	 * @return array day_of_week is day title array, key days is day array
	 */
	public static function get_days( $year, $month, $admin = false )
	{
		$options = (array) json_decode( get_option( 'mincalendar-options' ) );
		if ( false === $admin ) {
			if ( false === isset( $options['mc-sun'] )
				|| '' === $options['mc-sun']
			) {
				$sun = null;
			} else {
				$sun = $options['mc-sun'];
			}
			$sun = ( false === empty( $sun ) ) ? $sun : 'Sun';
			if ( false === isset( $options['mc-mon'] )
				|| '' === $options['mc-mon']
			) {
				$mon = null;
			} else {
				$mon = $options['mc-mon'];
			}
			$mon = ( false === empty( $mon ) ) ? $mon : 'Mon';
			if ( false === isset( $options['mc-tue'] )
				|| '' === $options['mc-tue']
			) {
				$tue = null;
			} else {
				$tue = $options['mc-tue'];
			}
			$tue = ( false === empty( $tue ) ) ? $tue : 'Tue';
			if ( false === isset( $options['mc-wed'] )
				|| '' === $options['mc-wed']
			) {
				$wed = null;
			} else {
				$wed = $options['mc-wed'];
			}
			$wed = ( false === empty( $wed ) ) ? $wed : 'Wed';
			if ( false === isset( $options['mc-thu'] )
				|| '' === $options['mc-thu']
			) {
				$thu = null;
			} else {
				$thu = $options['mc-thu'];
			}
			$thu = ( false === empty( $thu ) ) ? $thu : 'Thu';
			if ( false === isset( $options['mc-fri'] )
				|| '' === $options['mc-fri']
			) {
				$fri = null;
			} else {
				$fri = $options['mc-fri'];
			}
			$fri = ( false === empty( $fri ) ) ? $fri : 'Fri';
			if ( false === isset( $options['mc-sat'] )
				|| '' === $options['mc-sat']
			) {
				$sat = null;
			} else {
				$sat = $options['mc-sat'];
			}
			$sat = ( false === empty( $sat ) ) ? $sat : 'Sat';
		} else {
			$sun = 'Sun';
			$mon = 'Mon';
			$tue = 'Tue';
			$wed = 'Wed';
			$thu = 'Thu';
			$fri = 'Fri';
			$sat = 'Sat';
		}

		$day_of_week = array(
			0 => esc_html( $sun ),
			1 => esc_html( $mon ),
			2 => esc_html( $tue ),
			3 => esc_html( $wed ),
			4 => esc_html( $thu ),
			5 => esc_html( $fri ),
			6 => esc_html( $sat )
		);

		$timestamp_1st = mktime( 0, 0, 0, $month, 1, $year ); // year年month月1日のUNIXTIME
		$day_1st       = date( 'w', $timestamp_1st );        // 1日の曜日（0:日～6:土）
		$total         = date( 't', $timestamp_1st );        // $y年$m月の日数

		$days = array();
		for ( $i = 1 - $day_1st; $i <= $total + 7; $i ++ ) {
			// 対象月の日付が有効な場合の処理
			if ( 0 < $i && $i <= $total ) {
				$timestamp = mktime( 0, 0, 0, $month, $i, $year );
				$day       = date( 'w', $timestamp ); // $i日の曜日（0:日～6:土）
				$days[$i]  = $day_of_week[$day];
			}
		}

		return array(
			'days'        => $days,
			'day_of_week' => $day_of_week,
		);

	}
}
