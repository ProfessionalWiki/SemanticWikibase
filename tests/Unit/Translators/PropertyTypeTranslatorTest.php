<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translators;

use MediaWiki\Extension\SemanticWikibase\Translators\PropertyTypeTranslator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translators\PropertyTypeTranslator
 */
class PropertyTypeTranslatorTest extends TestCase {

	public function testFoo() {
		$this->assertSame( '_txt', $this->translate( 'string' ) );
	}

	private function translate( string $wikibasePropertyType ): string {
		return ( new PropertyTypeTranslator() )->translate( $wikibasePropertyType );
	}

}
