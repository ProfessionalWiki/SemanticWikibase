<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use DataValues\QuantityValue;
use DataValues\UnboundedQuantityValue;
use SMW\DataModel\ContainerSemanticData;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDIContainer;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

class ContainerValueTranslator {

	private DataValueTranslator $dataValueTranslator;

	public function __construct( DataValueTranslator $dataValueTranslator ) {
		$this->dataValueTranslator = $dataValueTranslator;
	}

	public function supportsStatement( Statement $statement ): bool {
		$mainSnak = $statement->getMainSnak();

		if ( $mainSnak instanceof PropertyValueSnak ) {
			$value = $mainSnak->getDataValue();
			return $value instanceof UnboundedQuantityValue;
		}

		return false;
	}

	public function statementToDataItem( Statement $statement, DIWikiPage $subject ): SMWDIContainer {
		$mainSnak = $statement->getMainSnak();

		if ( !( $mainSnak instanceof PropertyValueSnak ) ) {
			throw new \InvalidArgumentException( 'Not a PropertyValueSnak' );
		}

		$dataValue = $mainSnak->getDataValue();

		if ( $dataValue instanceof UnboundedQuantityValue ) {
			return $this->translateQuantityValue( $dataValue, $subject );
		}

		throw new \InvalidArgumentException( 'DataValue type not supported' );
	}

	private function translateQuantityValue( UnboundedQuantityValue $quantityValue, DIWikiPage $subject ): SMWDIContainer {
		$container = new ContainerSemanticData( new DIWikiPage(
			$subject->getDBkey(),
			$subject->getNamespace(),
			$subject->getInterwiki(),
			'Quantity ' . $quantityValue->getHash() // TODO: Q1$d40469c7-4586-70f5-7a75-cccef9381c4c
		) );

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_VALUE ),
			$this->dataValueTranslator->translateDecimalValue( $quantityValue->getAmount() )
		);

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_UNIT ),
			new \SMWDIBlob( $quantityValue->getUnit() )
		);

		if ( $quantityValue instanceof QuantityValue ) {
			$container->addPropertyObjectValue(
				new DIProperty( FixedProperties::QUANTITY_LOWER_BOUND ),
				$this->dataValueTranslator->translateDecimalValue( $quantityValue->getLowerBound() )
			);

			$container->addPropertyObjectValue(
				new DIProperty( FixedProperties::QUANTITY_UPPER_BOUND ),
				$this->dataValueTranslator->translateDecimalValue( $quantityValue->getUpperBound() )
			);
		}

		return new SMWDIContainer( $container );
	}

}
