<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\BooleanValue;
use DataValues\DecimalValue;
use DataValues\Geo\Values\GlobeCoordinateValue;
use DataValues\MonolingualTextValue;
use DataValues\NumberValue;
use DataValues\QuantityValue;
use DataValues\StringValue;
use DataValues\TimeValue;
use DataValues\UnboundedQuantityValue;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\Wikibase\TypedDataValue;
use SMW\DataModel\ContainerSemanticData;
use SMW\DataValueFactory;
use SMW\DataValues\ValueParsers\TimeValueParser;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDataItem;
use SMWDITime;
use SMWDIUri;
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

	public function translate( TypedDataValue $typedValue ): SMWDataItem {
		$value = $typedValue->getValue();

		if ( $value instanceof StringValue ) {
			return $this->translateStringValue( $typedValue );
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
		if ( $value instanceof UnboundedQuantityValue ) {
			return $this->translateQuantityValue( $value );
		}
		if ( $value instanceof GlobeCoordinateValue ) {
			return $this->translateGlobeCoordinateValue( $value );
		}
		if ( $value instanceof TimeValue ) {
			return $this->translateTimeValue( $value );
		}

		throw new \RuntimeException( 'Support for DataValue type "' . get_class( $value ) . '" not implemented' );
	}

	private function translateStringValue( TypedDataValue $typedValue ): SMWDataItem {
		if ( $typedValue->getPropertyType() === 'url' ) {
			return SMWDIUri::doUnserialize( $typedValue->getValue()->getValue() );
		}

		return new \SMWDIBlob( $typedValue->getValue()->getValue() );
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

	private function translateQuantityValue( UnboundedQuantityValue $quantityValue ): SMWDataItem {
		$container = new ContainerSemanticData( new DIWikiPage(
			$this->subject->getDBkey(),
			$this->subject->getNamespace(),
			$this->subject->getInterwiki(),
			'Quantity ' . $quantityValue->getHash()
		) );

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_VALUE ),
			$this->translateDecimalValue( $quantityValue->getAmount() )
		);

		$container->addPropertyObjectValue(
			new DIProperty( FixedProperties::QUANTITY_UNIT ),
			new \SMWDIBlob( $quantityValue->getUnit() )
		);

		if ( $quantityValue instanceof QuantityValue ) {
			$container->addPropertyObjectValue(
				new DIProperty( FixedProperties::QUANTITY_LOWER_BOUND ),
				$this->translateDecimalValue( $quantityValue->getLowerBound() )
			);

			$container->addPropertyObjectValue(
				new DIProperty( FixedProperties::QUANTITY_UPPER_BOUND ),
				$this->translateDecimalValue( $quantityValue->getUpperBound() )
			);
		}

		return new \SMWDIContainer( $container );
	}

	private function translateTimeValue( TimeValue $value ): \SMWDITime {
		$components = ( new TimeValueParser() )->parse( $value->getTime() );

		return new \SMWDITime(
			$this->wbToSmwCalendarModel( $value->getCalendarModel() ),
			$components->get( 'datecomponents' )[0],
			$components->get( 'datecomponents' )[2],
			$components->get( 'datecomponents' )[4],
			$components->get( 'hours' ),
			$components->get( 'minutes' ),
			$components->get( 'seconds' ),
		);
	}

	private function wbToSmwCalendarModel( string $wbCalendarModel ): int {
		$julianModel = 'http://www.wikidata.org/entity/Q1985786';
		return $wbCalendarModel === $julianModel ? SMWDITime::CM_JULIAN : SMWDITime::CM_GREGORIAN;
	}

}
