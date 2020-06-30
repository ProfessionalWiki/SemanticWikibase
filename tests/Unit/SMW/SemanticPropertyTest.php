<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\SMW;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticProperty;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\SMW\SemanticProperty
 */
class SemanticPropertyTest extends TestCase {

	public function testGetAliasesReturnsTheAlias() {
		$property = new SemanticProperty( 'id', '_txt', 'Cats', 'Kittens' );

		$this->assertSame(
			[ 'Kittens' ],
			$property->getAliases()
		);
	}

	public function testGetAliasesReturnsEmptyArrayWhenThereAreNone() {
		$property = new SemanticProperty( 'id', '_txt', 'Cats' );

		$this->assertSame(
			[],
			$property->getAliases()
		);
	}

}
