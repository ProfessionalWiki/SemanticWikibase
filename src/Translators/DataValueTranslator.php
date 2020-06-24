<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\BooleanValue;
use DataValues\DataValue;
use DataValues\Geo\Values\GlobeCoordinateValue;
use DataValues\MonolingualTextValue;
use DataValues\NumberValue;
use DataValues\StringValue;
use SMW\DataValueFactory;
use SMWDataItem;

class DataValueTranslator {

	private DataValueFactory $dataValueFactory;

	public function __construct( DataValueFactory $dataValueFactory ) {
		$this->dataValueFactory = $dataValueFactory;
	}

	public function translate( DataValue $value ): SMWDataItem {
		if ( $value instanceof StringValue ) {
			return new \SMWDIBlob( $value->getValue() );
		}
		if ( $value instanceof BooleanValue ) {
			return new \SMWDIBoolean( $value->getValue() );
		}
		if ( $value instanceof NumberValue ) {
			return new \SMWDINumber( $value->getValue() );
		}
		if ( $value instanceof MonolingualTextValue ) {
			return $this->translateMonolingualTextValue( $value );
		}
		if ( $value instanceof GlobeCoordinateValue ) {
			return $this->translateGlobeCoordinateValue( $value );
		}
	}

	private function translateMonolingualTextValue( MonolingualTextValue $value ): SMWDataItem {
		return $this->dataValueFactory->newDataValueByType(
			\SMW\DataValues\MonolingualTextValue::TYPE_ID,
			$value->getText() . '@' . $value->getLanguageCode(),
		)->getDataItem();
	}

	private function translateGlobeCoordinateValue( GlobeCoordinateValue $globeValue ): \SMWDIGeoCoord {
		return \SMWDIGeoCoord::newFromLatLong(
			$globeValue->getLatitude(),
			$globeValue->getLongitude()
		);
	}

}
