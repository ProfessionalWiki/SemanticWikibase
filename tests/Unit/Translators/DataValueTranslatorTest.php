<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translators;

use DataValues\DataValue;
use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\Translators\DataValueTranslator;
use PHPUnit\Framework\TestCase;

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

}
