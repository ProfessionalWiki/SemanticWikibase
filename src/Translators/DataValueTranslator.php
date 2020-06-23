<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\BooleanValue;
use DataValues\DataValue;
use DataValues\MonolingualTextValue;
use DataValues\NumberValue;
use DataValues\StringValue;
use SMW\DataValueFactory;
use SMW\DIProperty;
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
	}

	private function translateMonolingualTextValue( MonolingualTextValue $value ): SMWDataItem {
		return DataValueFactory::getInstance()->newDataValueByType(
			\SMW\DataValues\MonolingualTextValue::TYPE_ID,
			$value->getText() . '@' . $value->getLanguageCode(),
		)->getDataItem();
	}

}
