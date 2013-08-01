jQuery( function( $ ) {

    /**
     * 年, 月セレクトボタンでカレンダーの日付、曜日並べ替え
     *
     * @namespace Mincalendar
     * @class example
     * @class CustomFields
     */
    var customFields = Mincalendar.namespace( 'CustomFields' );

    // CustomFields実装
    ( function() {

        /**
         * 追加日付で必要な属性値変更処理
         *
         * @method modify
         * @private
         * @param {Object} params key/value pair
         */
        function modify( params ) {
            $( 'span.date', params.elem ).text( params.date );
            $( 'span.days', params.elem ).text( params.days );
            $( 'select[name^="date-"]', params.elem ).attr( 'name', 'date-' + params.date );
            $( 'select', params.elem).get(0).selectIndex = -1;
        }
        /**
         *  日付のカスタムフィールド作成
         *
         * @method make
         * @public
         * @param {Number} year  作成するカレンダーの年
         * @param {Number} month 作成するカレンダーの月
         */
        function make( year, month ) {

            var week = [];
            week[0] = 'Sun';
            week[1] = 'Mon';
            week[2] = 'Tue';
            week[3] = 'Wed';
            week[4] = 'Thu';
            week[5] = 'Fri';
            week[6] = 'Sat';


            var day;
            // 操作前の末日
            var oldLength = $( '.days' ).length;
            // 操作後の末日
            // 0-11, 日付 1-31 また0は前月の末日
            var newLength = new Date( year, month, 0 ).getDate(); // 当月末日
            $( '.days' ).each( function( i ) {
                day = week[ new Date( year, month - 1, i + 1 ).getDay() ];
                $( this ).text( day );
                if ( i + 1 > newLength ) {
                    $( this ).parent().remove();
                }
            } );

            // 追加した日付の調整
            var j, args, clone;
            for ( j = 1; j <= ( newLength - oldLength ) ; j++ ) {
                clone = $( '#fields_date div:last-child' ).clone();
                $( clone ).appendTo( '#fields_date' );
                args = {
                    'elem': clone,
                    'date': oldLength + j,
                    'days':  week[ new Date( year, month - 1, length + 1 ).getDay() ]
                };
                modify( args );
            }

        }

        // 公開メソッド
        customFields.make = make;

    }() );

});