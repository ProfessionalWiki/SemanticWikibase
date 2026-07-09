<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use DataValues\BooleanValue;
use DataValues\DecimalValue;
use DataValues\Geo\Values\GlobeCoordinateValue;
use DataValues\NumberValue;
use DataValues\StringValue;
use DataValues\TimeValue;
use MediaWiki\Extension\SemanticWikibase\Wikibase\TypedDataValue;
use SMW\DataValues\ValueParsers\TimeValueParser;
use SMW\DIWikiPage;
use SMWDataItem;
use SMWDITime;
use SMWDIUri;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\EDTF\Services\TimeValueBuilder;
use Wikibase\EDTF\EdtfValue;
use MediaWiki\Logger\LoggerFactory;
use EDTF\EdtfFactory;
use EDTF\Model\ExtDate;
use EDTF\Model\ExtDateTime;
use EDTF\Model\Interval;

class DataValueTranslator {

	public function translate( TypedDataValue $typedValue ): SMWDataItem {
		
		
		$value = $typedValue->getValue();
		$propertyType = $typedValue->getPropertyType();
		if( $value != null) {
			wfDebug( 'swb: translate: '.get_class( $value ).' ptype: '.$propertyType );
		}
		
		if ( $value instanceof StringValue ) {
			wfDebug( 'swb: translate: '. $typedValue->getValue()->getValue()  );
			if( $propertyType == 'edtf') {
				return $this->translateEDTF("".$value->getValue());
			}else if ($propertyType == 'localMedia') {
                return $this->translateLocalMedia("".$value->getValue());
            }
			return $this->translateStringValue( $typedValue );
		}
		if ( $value instanceof BooleanValue ) {
			return new \SMWDIBoolean( $value->getValue() );
		}
		if ( $value instanceof NumberValue ) {
			return new \SMWDINumber( $value->getValue() );
		}
		if ( $value instanceof EntityIdValue ) {
			return $this->translateEntityIdValue( $value );
		}
		if ( $value instanceof DecimalValue ) {
			return $this->translateDecimalValue( $value );
		}
		if ( $value instanceof GlobeCoordinateValue ) {
			return $this->translateGlobeCoordinateValue( $value );
		}
		
		if ( $value instanceof TimeValue ) {
			wfDebug( 'swb: translate time: '. $$value.toString()   );
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

	private function translateGlobeCoordinateValue( GlobeCoordinateValue $globeValue ): \SMWDIGeoCoord {
		return \SMWDIGeoCoord::newFromLatLong(
			$globeValue->getLatitude(),
			$globeValue->getLongitude()
		);
	}

	private function translateLocalMedia( String $imagePage): SMWDataItem {
			return new DIWikiPage(
					$imagePage,
					NS_FILE
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

		if ( $idValue instanceof NumericPropertyId ) {
			return WB_NS_PROPERTY;
		}

		throw new \RuntimeException( 'Support for EntityId type not implemented' );
	}

	public function translateDecimalValue( DecimalValue $value ): SMWDataItem {
		return new \SMWDINumber( $value->getValueFloat() );
	}

	private function translateEDTF( String $value ): \SMWDITime {
		wfDebug( 'swb: translate edtf' );
		$tvb = new TimeValueBuilder( EdtfFactory::newParser() );
		$tvArr = $tvb->edtfToTimeValues( $value );
		$parser = \EDTF\EdtfFactory::newParser();
		$parsingResult = $parser->parse($value);
		wfDebug($parsingResult->isValid()); // true
		$edtfValue = $parsingResult->getEdtfValue(); // \EDTF\EdtfValue
		$humanizer = \EDTF\EdtfFactory::newHumanizerForLanguage( 'en' );
		wfDebug($humanizer->humanize($edtfValue)); // string
		wfDebug(get_class($edtfValue)); // string
		$result = null;

		if ( $edtfValue instanceof Interval ) {
			if($edtfValue->hasStartDate()){
				$edtfValue = $edtfValue->getStartDate();
			} else if ($edtfValue->hasEndDate()){
				$edtfValue = $edtfValue->getEndDate();
			} else {
				wfDebug('ERROR: unable to translate empty edtf interval to smw date');
				return null;
			}
		}

		if( $edtfValue instanceof ExtDate ) {
			$result = new \SMWDITime(
				SMWDITime::CM_GREGORIAN,
				$edtfValue->getYear(),
				$edtfValue->getMonth(),
				$edtfValue->getDay()
			);

		} else if ( $edtfValue instanceof ExtDateTime ) {
			$result = new \SMWDITime(
				SMWDITime::CM_GREGORIAN,
				$edtfValue->getYear(),
				$edtfValue->getMonth(),
				$edtfValue->getDay(),
				$edtfValue->getHour(),
				$edtfValue->getMinute(),
				$edtfValue->getSecond()
			);
		} else {
		$result = new \SMWDITime(
				SMWDITime::CM_GREGORIAN,
				1970
			);
				
		}
	
		return $result;
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
