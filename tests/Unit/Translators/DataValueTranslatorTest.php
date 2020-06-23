<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translators;

use DataValues\BooleanValue;
use DataValues\DataValue;
use DataValues\NumberValue;
use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\Translators\DataValueTranslator;
use PHPUnit\Framework\TestCase;

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
		return ( new DataValueTranslator() )->translate( $dataValue );
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

}
