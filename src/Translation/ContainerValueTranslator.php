<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use DataValues\MonolingualTextValue;
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
	private MonoTextTranslator $monoTextTranslator;

	public function __construct( DataValueTranslator $dataValueTranslator, MonoTextTranslator $monoTextTranslator ) {
		$this->dataValueTranslator = $dataValueTranslator;
		$this->monoTextTranslator = $monoTextTranslator;
	}

	public function supportsStatement( Statement $statement ): bool {
		$mainSnak = $statement->getMainSnak();

		if ( $mainSnak instanceof PropertyValueSnak ) {
			$value = $mainSnak->getDataValue();
			return $value instanceof UnboundedQuantityValue
				|| $value instanceof MonolingualTextValue;
		}

		return false;
	}

	public function statementToDataItem( Statement $statement, DIWikiPage $subject ): SMWDIContainer {
		$mainSnak = $statement->getMainSnak();

		if ( !( $mainSnak instanceof PropertyValueSnak ) ) {
			throw new \InvalidArgumentException( 'Not a PropertyValueSnak' );
		}

		$dataValue = $mainSnak->getDataValue();

		if ( $dataValue instanceof MonolingualTextValue ) {
			return $this->translateMonolingualTextValue( $dataValue, $subject );
		}

		$container = $this->newContainerSemanticData( $statement, $subject );

		if ( $dataValue instanceof UnboundedQuantityValue ) {
			return $this->translateQuantityValue( $dataValue, $container );
		}

		throw new \InvalidArgumentException( 'DataValue type not supported' );
	}

	private function newContainerSemanticData( Statement $statement, DIWikiPage $subject ): ContainerSemanticData {
		return new ContainerSemanticData( new DIWikiPage(
			$subject->getDBkey(),
			$subject->getNamespace(),
			$subject->getInterwiki(),
			$statement->getGuid()
		) );
	}

	private function translateQuantityValue( UnboundedQuantityValue $quantityValue, ContainerSemanticData $container ): SMWDIContainer {
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

	private function translateMonolingualTextValue( MonolingualTextValue $value, DIWikiPage $subject ): SMWDIContainer {
		return $this->monoTextTranslator->valueToDataItem( $value, $subject );
	}

}
