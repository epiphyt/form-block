export const getSanitizedAttributeValue = ( value, settings = {} ) => {
	if ( Object.keys( settings ).length === 0 && Object.getPrototypeOf( settings ) === Object.prototype ) {
		return value;
	}
	
	if ( settings.stripSpecialChars ) {
		value = stripSpecialChars( value, settings.toLowerCase || false );
	}
	
	return value;
}

export const stripSpecialChars = ( name, toLowerCase = true ) => {
	if ( typeof name === 'undefined' || ! name.length ) {
		return name;
	}
	
	name = name.replace( /ä/gi, 'ae' );
	name = name.replace( /ö/gi, 'oe' );
	name = name.replace( /ü/gi, 'ue' );
	name = name.replace( /ß/gi, 'ss' );
	
	return name.replace( /[^a-z0-9\-_\.\[\]]/g, function( s ) {
		const c = s.charCodeAt( 0 );
		
		if ( c === 32 ) {
			return '-';
		}
		
		if ( c >= 65 && c <= 90 ) {
			if ( toLowerCase ) {
				return s.toLowerCase();
			}
			
			return s;
		}
		
		return '';
	} );
};
