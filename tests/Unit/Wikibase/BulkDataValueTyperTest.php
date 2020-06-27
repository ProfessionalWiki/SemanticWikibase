<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Wikibase;

use MediaWiki\Extension\SemanticWikibase\Wikibase\BulkDataValueTyper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Wikibase\BulkDataValueTyper
 */
class BulkDataValueTyperTest extends TestCase {

	public function testEmpty() {
		$this->assertSame( [], $this->newTyper()->addTypesToValues( [] ) );
	}

	private function newTyper(): BulkDataValueTyper {
		return new BulkDataValueTyper();
	}

}
