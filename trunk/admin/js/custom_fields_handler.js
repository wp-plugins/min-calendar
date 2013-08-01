jQuery( function( $ ) {

    var customFields = Mincalendar.namespace( 'CustomFields' );

    $( 'select' ).filter( function () {
        return $( this ).attr('name').match(/^(y|m)/);
    }).change( function() {
        var year  = parseInt( $( 'select[name=year]' ).val() , 10);
        var month = parseInt( $( 'select[name=month]' ).val() , 10);

        if ( isNaN( year ) === true
            || isNaN( month ) ) {
            return false;
        }

        customFields.make( year, month );
    } );

} );