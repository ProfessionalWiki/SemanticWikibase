<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\Wikibase\TypedDataValue;
use SMW\DataValueFactory;
use SMW\DIWikiPage;
use Wikibase\DataModel\Services\Lookup\PropertyDataTypeLookup;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

class StatementTranslator {

	private DataValueFactory $dataValueFactory;
	private PropertyDataTypeLookup $propertyTypeLookup;

	public function __construct( DataValueFactory $dataValueFactory, PropertyDataTypeLookup $propertyTypeLookup ) {
		$this->dataValueFactory = $dataValueFactory;
		$this->propertyTypeLookup = $propertyTypeLookup;
	}

	public function statementToDataItem( Statement $statement, DIWikiPage $subject ): ?\SMWDataItem {
		$mainSnak = $statement->getMainSnak();

		if ( !( $mainSnak instanceof PropertyValueSnak ) ) {
			return null;
		}

		$dataValueTranslator = new DataValueTranslator( $this->dataValueFactory, $subject );

		return $dataValueTranslator->translate( $this->snakToTypedValue( $mainSnak ) );
	}

	private function snakToTypedValue( PropertyValueSnak $snak ): TypedDataValue {
		return new TypedDataValue(
			$this->propertyTypeLookup->getDataTypeIdForProperty( $snak->getPropertyId() ),
			$snak->getDataValue()
		);
	}

}
