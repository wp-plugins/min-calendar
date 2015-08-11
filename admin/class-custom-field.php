<?php
/**
 * MC_Custom_Field
 *
 * Custom field of calendar.
 */
class MC_Custom_Field
{

	/**
	 * set
	 *
	 * @param MC_Post_Wrapper $post_wrapper
	 * @param string $html markup
	 */
	public static function set_field( $post_wrapper, $html )
	{
		/*
		 * get custom filed value
		 */
		// get existing value of year, month
		$year  = (int) get_post_meta( $post_wrapper->id, 'year', true );
		$month = (int) get_post_meta( $post_wrapper->id, 'month', true );

		// set today if existing value is not.
		$today = getdate();
		$year  = ( ! empty( $year ) ) ? $year : (int) $today[ 'year' ];
		$month = ( ! empty( $month ) ) ? $month : (int) $today[ 'mon' ];

		// get days(a day of week)
		$days = MC_Day::get_days( $year, $month, true  );
		$days = $days["days"];
		$total = count( $days );

		// get existing date
		$date = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key        = 'date-' . $i;
			// if post_wrapper->id is NULL, then $data[ $i ] is false.
			$date[ $i ] = get_post_meta( $post_wrapper->id, $key, true );
		}

		// get related posts
		$related_posts = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key                 = 'post-' . $i;
			// if post_wrapper->id is NULL, then $related_posts[ $i ] is false.
			$related_posts[ $i ] = get_post_meta( $post_wrapper->id, $key, true );
		}

		// get text (markup and script convert 実体参照 when display)
		$texts = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key         = 'text-' . $i;
			// if post_wrapper->id is NULL, then $texts[ $i ] is false.
			$texts[ $i ] = get_post_meta( $post_wrapper->id, $key, true );
		}

		/*
		 * display custom field valuve
		 */
		$html .= '<div id="fields">';
		// yeaer
		$html .= '<div id="fields_year_month">' . PHP_EOL
			. __( 'Year', 'mincalendar' )
			. ' : <select name="year"><option value="-">--</option>';
		for ( $y = 2000; $y < 2050; $y ++ ) {
			if ( $y === $year ) {
				$html .= '<option value="' . $year . '" selected="selected">' . $year . '</option>';
			} else {
				$html .= '<option value="' . $y . '">' . $y . '</option>';
			}
		}
		$html .= '</select>&nbsp;';

		// month 
		$html .= 'Month : <select name="month"><option value="-">--</option>';
		for ( $m = 1; $m < 13; $m ++ ) {
			if ( $m === $month ) {
				$html .= '<option value="' . $month . '" selected = "selected">' . $month . '</option>';
			} else {
				$html .= '<option value="' . $m . '">' . $m . '</option>';
			}
		}
		$html .= '</select></div><!-- mincalendar_fileds_yearandmonth -->' . PHP_EOL;

		/*
		 * date processing
		 */
		$html .= '<div id="fields_date">';

		// get wp_option
		$options  = (array) json_decode( get_option( 'mincalendar-options' ) );
		// -, ○, ×
		if ( isset( $options['mc-value-1st'] ) && ! empty( $options['mc-value-1st'] ) ) {
			$context1 = $options['mc-value-1st'];
		} else {
			$context1 = '';
		}
		if ( isset( $options['mc-value-2st'] ) && ! empty( $options['mc-value-2st'] ) ) {
			$context2 = $options['mc-value-2st'];
		} else {
			$context2 = '○';
		}
		if ( isset( $options['mc-value-3st'] ) && ! empty( $options['mc-value-3st'] ) ) {
			$context3 = $options['mc-value-3st'];
		} else {
			$context3 = '×';
		}
		$tags     = ( true === isset( $options[ 'mc-tag' ] ) ) ? $options[ 'mc-tag' ] : false;
		for ( $i = 1; $i <= $total; $i ++ ) {
			$html .= '<div class="field">' . PHP_EOL;
			$html .= '<div class="cell cell-date">';
			// 既存の投稿
			if ( 'mc-value-1st' === $date[ $i ] ) {
				$html .= '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
					. ' : <select name="date-' . $i . '">'
					. '<option value="mc-value-1st" selected="selected">' . esc_html( $context1 ) . '</option>'
					. '<option value="mc-value-2nd">' . esc_html( $context2 ) . '</option>'
					. '<option value="mc-value-3rd">' . esc_html( $context3 ) . '</option>'
					. '</select>';
			}
			if ( 'mc-value-2nd' === $date[ $i ] ) {
				$html .= '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
					. ' : <select name="date-' . $i . '">'
					. '<option value="mc-value-1st">' . esc_html( $context1 ) . '</option>'
					. '<option value="mc-value-2nd" selected="selected">' . esc_html( $context2 ) . '</option>'
					. '<option value="mc-value-3rd">' . esc_html( $context3 ) . '</option>'
					. '</select>';
			}
			if ( 'mc-value-3rd' === $date[ $i ] ) {
				$html .= '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
					. ' : <select name="date-' . $i . '">'
					. '<option value="mc-value-1st">' . esc_html( $context1 ) . '</option>'
					. '<option value="mc-value-2nd">' . esc_html( $context2 ) . '</option>'
					. '<option value="mc-value-3rd" selected="selected">' . esc_html( $context3 ) . '</option>'
					. '</select>';
			}
			// 新規の作成
			if (false === $date[ $i ]) {
				$html .= '<span class="date">' . $i . '</span>' . ' ' . '<span class="days">' . $days[ $i ] . '</span> '
					. ' : <select name="date-' . $i . '">'
					. '<option value="mc-value-1st" selected="selected">' . esc_html( $context1 ) . '</option>'
					. '<option value="mc-value-2nd">' . esc_html( $context2 ) . '</option>'
					. '<option value="mc-value-3rd">' . esc_html( $context3 ) . '</option>'
					. '</select>';
			}
			$html .= '</div><!-- cell-date -->';

			// related post
			if ( ! empty( $tags ) ) {
				$tags_name = explode( ',', $tags );
				$tags_id   = array();
				foreach ( $tags_name as $key => $value ) {
					$tag_prop        = get_term_by( 'name', trim( $value ), 'post_tag' );
					$tags_id[ $key ] = $tag_prop->term_id;
				}

				$myposts = get_posts(
					array(
						'numberposts' => 100,
						'tag__in'     => $tags_id
					)
				);
				$html .= '<div class="cell cell-post">' . PHP_EOL;
				$html .= 'post: <select name="post-' . $i . '">' . PHP_EOL;
				$html .= '<option value="-">--</option>';
				foreach ( $myposts as $mypost ) {
					if ( $related_posts[ $i ] == $mypost->ID ) {
						$html .= '<option value="' . $mypost->ID . '" selected="selected">' . $mypost->post_title . '</option>';
					} else {
						$html .= '<option value="' . $mypost->ID . '">' . $mypost->post_title . '</option>';
					}
				}
				$html .= '</select>' . PHP_EOL;
				$html .= '</div><!-- cell-post -->' . PHP_EOL;
			}

			// text
			$html .= '<div class="cell cell-text">' . PHP_EOL;
			$html .= 'text: <textarea type="text" rows="8" cols="30" name="text-' . $i . '">' . esc_html(
					$texts[ $i ]
				) . '</textarea>';
			$html .= '</div><!-- cell-text -->';

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
	 * save MC_POST
	 * 
	 * @param MC_Post_Wrapper $post_wrapper
	 */
	public static function update_field( $post_wrapper )
	{
		$post_id = $post_wrapper->id;
		
		// security check (_wpnonce:wp number used once)
		$nonce = isset( $_POST[ '_wpnonce' ] ) ? $_POST[ '_wpnonce' ] : null;
		if ( ! wp_verify_nonce( $nonce, 'save_' . $post_id ) && ! wp_verify_nonce( $nonce, 'save_' . - 1 ) ) {
			return $post_id;
		}

		/*
		 * get value
		 */
		// date
		$year  = (int) $_POST[ 'year' ];
		$month = (int) $_POST[ 'month' ];
		$days  = MC_Day::get_days( $year, $month, true );
		$total = count( $days[ 'days' ] );
		$date  = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'date-' . $i;
			if ( isset( $_POST[ $key ] ) ) {
				$date[ $i ] = $_POST[ $key ];
			} else {
				$date[ $i ] = '';
			}
		}
		// 関連記事
		$related_posts = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'post-' . $i;
			if ( isset( $_POST[ $key ] ) ) {
				$related_posts[ $i ] = $_POST[ $key ];
			} else {
				$related_posts[ $i ] = '';
			}
		}
		// テキスト
		$texts = array();
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'text-' . $i;
			if ( isset( $_POST[ $key ] ) ) {
				$texts[ $i ] = $_POST[ $key ];
			} else {
				$texts[ $i ] = '';
			}
		}

		/*
		 * 更新
		 */
		if ( '' === $year ) {
			delete_post_meta( $post_id, 'year' );
		} else {
			update_post_meta( $post_id, 'year', $year );
		}
		if ( '' === $month ) {
			delete_post_meta( $post_id, 'month' );
		} else {
			update_post_meta( $post_id, 'month', $month );
		}
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'date-' . $i;
			if ( '' === $date[ $i ] ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $date[ $i ] );
			}
		}
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'post-' . $i;
			if ( '' === $related_posts[ $i ] ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $related_posts[ $i ] );
			}
		}
		for ( $i = 1; $i <= $total; $i ++ ) {
			$key = 'text-' . $i;
			if ( '' === $texts[ $i ] ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $texts[ $i ] );
			}
		}

	}
}