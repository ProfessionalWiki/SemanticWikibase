<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\BooleanValue;
use DataValues\DataValue;
use DataValues\DecimalValue;
use DataValues\Geo\Values\GlobeCoordinateValue;
use DataValues\MonolingualTextValue;
use DataValues\NumberValue;
use DataValues\QuantityValue;
use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\FixedProperties;
use SMW\DataModel\ContainerSemanticData;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDataItem;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;

class DataValueTranslator {

	private DataValueFactory $dataValueFactory;
	private DIWikiPage $subject;

	public function __construct( DataValueFactory $dataValueFactory, DIWikiPage $subject ) {
		$this->dataValueFactory = $dataValueFactory;
		$this->subject = $subject;
	}

	// TODO: DataValue + property type
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
		if ( $value instanceof EntityIdValue ) {
			return $this->translateEntityIdValue( $value );
		}
		if ( $value instanceof DecimalValue ) {
			return $this->translateDecimalValue( $value );
		}
		if ( $value instanceof QuantityValue ) {
			return $this->translateQuantityValue( $value );
		}
		if ( $value instanceof GlobeCoordinateValue ) {
			return $this->translateGlobeCoordinateValue( $value );
		}

		throw new \RuntimeException( 'Support for DataValue type not implemented' );
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

	private function translateEntityIdValue( EntityIdValue $idValue ): SMWDataItem {
		return new DIWikiPage(
			$idValue->getEntityId()->getSerialization(),
			$this->entityIdToNamespaceId( $idValue->getEntityId() )
		);
	}

	private function entityIdToNamespaceId( EntityId $idValue ): int {
		if ( $idValue instanceof ItemId ) {
			return WB_NS_ITEM;
		}

		if ( $idValue instanceof PropertyId ) {
			return WB_NS_PROPERTY;
		}

		throw new \RuntimeException( 'Support for EntityId type not implemented' );
	}

	private function translateDecimalValue( DecimalValue $value ): SMWDataItem {
		return new \SMWDINumber( $value->getValueFloat() );
	}

	private function translateQuantityValue( QuantityValue $quantityValue ): SMWDataItem {
		$container = new ContainerSemanticData( $this->subject );

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_VALUE ),
			$this->translateDecimalValue( $quantityValue->getAmount() )
		);

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_LOWER_BOUND ),
			$this->translateDecimalValue( $quantityValue->getLowerBound() )
		);

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_UPPER_BOUND ),
			$this->translateDecimalValue( $quantityValue->getUpperBound() )
		);

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_UNIT ),
			new \SMWDIBlob( $quantityValue->getUnit() )
		);

		return new \SMWDIContainer( $container );
	}

}
