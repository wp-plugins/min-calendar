jQuery( function( $ ) {
    ( function() {
        try {

            $( '#mincalendar-title:disabled' ).css( 'cursor', 'default' );

            $( 'input#mincalendar-title' ).mouseover( function() {
                $( this ).not( '.focus' ).addClass( 'mouseover' );
            } );

            $( 'input#mincalendar-title' ).mouseout( function() {
                $( this ).removeClass( 'mouseover' );
            } );

            $( 'input#mincalendar-title' ).focus( function() {
                $( this ).addClass( 'focus' ).removeClass( 'mouseover' );
            } );

            $( 'input#mincalendar-title' ).blur( function() {
                $( this ).removeClass( 'focus' );
            } );

            $( 'input#mincalendar-title' ).change( function() {
                updateTag();
            } );

            updateTag();

        } catch ( e ) {
        }
    }() );

    function updateTag() {
        var title = $( 'input#mincalendar-title' ).val();

        if ( title ) {
            title = title.replace(/["'\[\]]/g, '' );
        }

        $( 'input#mincalendar-title' ).val( title );
        var postId = $('input#post_id' ).val();
        var tag    = '[mincalendar id="' + postId + '" title="' + title + '"]';
        $('input#mincalendar-anchor-text' ).val( tag );

        var oldId = $( 'input#mincalendar-id' ).val();

        if ( 0 !== parseInt( oldId, 10 ) ) {
            var tagOld = '[mincalendar ' + oldId + ' "' + title + '"]';
            $( 'input#mincalendar-anchor-text-old' ).val( tagOld ).parent( 'p.tagcode' ).show();
        }
    }

} );