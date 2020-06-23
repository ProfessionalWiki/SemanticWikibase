<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\BooleanValue;
use DataValues\DataValue;
use DataValues\NumberValue;
use DataValues\StringValue;

class DataValueTranslator {

	public function translate( DataValue $value ): \SMWDataItem {
		if ( $value instanceof StringValue ) {
			return new \SMWDIBlob( $value->getValue() );
		}
		if ( $value instanceof BooleanValue ) {
			return new \SMWDIBoolean( $value->getValue() );
		}
		if ( $value instanceof NumberValue ) {
			return new \SMWDINumber( $value->getValue() );
		}
	}

}
