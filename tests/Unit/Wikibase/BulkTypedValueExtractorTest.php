<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Wikibase;

use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\Wikibase\BulkTypedValueExtractor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Wikibase\BulkTypedValueExtractor
 */
class BulkTypedValueExtractorTest extends TestCase {

	public function testEmpty() {
		$this->assertSame( [], $this->newTyper()->snaksToTypedValues( [] ) );
	}

	private function newTyper(): BulkTypedValueExtractor {
		return new BulkTypedValueExtractor();
	}

//	public function testNotEmpty() {
//		$this->assertSame(
//			[
//
//			],
//			$this->newTyper()->snaksToTypedValues( [
//				new StringValue()
//			] )
//		);
//	}

}
