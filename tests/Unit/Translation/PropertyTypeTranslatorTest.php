<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use Maps\SemanticMW\CoordinateValue;
use MediaWiki\Extension\SemanticWikibase\Translation\PropertyTypeTranslator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\PropertyTypeTranslator
 */
class PropertyTypeTranslatorTest extends TestCase {

	public function testTranslation() {
		$this->assertSame( '_txt', $this->translate( 'string' ) );
		$this->assertSame( '_txt', $this->translate( 'commonsMedia' ) );
		$this->assertSame( '_mlt_rec', $this->translate( 'monolingualtext' ) );
		$this->assertSame( '_wpg', $this->translate( 'wikibase-item' ) );

		if ( class_exists( CoordinateValue::class ) ) {
			$this->assertSame( '_geo', $this->translate( 'globe-coordinate' ) );
		}
	}

	private function translate( string $wikibasePropertyType ): string {
		return ( new PropertyTypeTranslator() )->translate( $wikibasePropertyType );
	}

}
