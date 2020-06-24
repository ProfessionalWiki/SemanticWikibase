<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translators;

use DataValues\BooleanValue;
use DataValues\DataValue;
use DataValues\Geo\Values\GlobeCoordinateValue;
use DataValues\Geo\Values\LatLongValue;
use DataValues\MonolingualTextValue;
use DataValues\NumberValue;
use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\Translators\DataValueTranslator;
use PHPUnit\Framework\TestCase;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMWDataItem;
use SMWDIContainer;
use SMWDIGeoCoord;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translators\DataValueTranslator
 */
class DataValueTranslatorTest extends TestCase {

	public function testString() {
		$this->assertEquals(
			new \SMWDIBlob( 'foo' ),
			$this->translate( new StringValue( 'foo' ) )
		);
	}

	private function translate( DataValue $dataValue ): \SMWDataItem {
		return ( new DataValueTranslator(
			DataValueFactory::getInstance()
		) )->translate( $dataValue );
	}

	public function testBoolean() {
		$this->assertEquals(
			new \SMWDIBoolean( false ),
			$this->translate( new BooleanValue( false ) )
		);
	}

	public function testNumber() {
		$this->assertEquals(
			new \SMWDINumber( 42 ),
			$this->translate( new NumberValue( 42 ) )
		);
	}

	public function testMonolingualText() {
		/**
		 * @var SMWDIContainer $dataItem
		 */
		$dataItem = $this->translate( new MonolingualTextValue( 'en', 'fluffy bunnies' ) );

		$this->assertSame( SMWDataItem::TYPE_CONTAINER, $dataItem->getDIType() );

		$this->assertEquals(
			[ 'fluffy bunnies' ],
			$dataItem->getSemanticData()->getPropertyValues( new DIProperty( '_TEXT' ) )
		);

		$this->assertEquals(
			[ 'en' ],
			$dataItem->getSemanticData()->getPropertyValues( new DIProperty( '_LCODE' ) )
		);
	}

	public function testGlobeCoordinate() {
		$this->assertEquals(
			SMWDIGeoCoord::newFromLatLong( 1.006, 1.47 ),
			$this->translate( new GlobeCoordinateValue(
				new LatLongValue( 1.006, 1.47 ),
				0.01,
			) )
		);
	}

}
