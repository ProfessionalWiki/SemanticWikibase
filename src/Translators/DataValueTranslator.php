<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\DataValue;
use DataValues\StringValue;

class DataValueTranslator {

	public function translate( DataValue $value ): \SMWDataItem {
		if ( $value instanceof StringValue ) {
			return new \SMWDIBlob( $value->getValue() );
		}
	}

}
