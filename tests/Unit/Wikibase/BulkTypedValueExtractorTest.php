<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Wikibase;

use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\Wikibase\BulkTypedValueExtractor;
use MediaWiki\Extension\SemanticWikibase\Wikibase\TypedDataValue;
use PHPUnit\Framework\TestCase;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\Lib\Store\PropertyInfoLookup;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Wikibase\BulkTypedValueExtractor
 */
class BulkTypedValueExtractorTest extends TestCase {

	public function testEmpty() {
		$this->assertSame( [], $this->newTyper()->snaksToTypedValues( [] ) );
	}

	private function newTyper(): BulkTypedValueExtractor {
		return new BulkTypedValueExtractor(
			new class implements PropertyInfoLookup {
				public function getPropertyInfo( PropertyId $propertyId ) {
				}

				public function getPropertyInfoForDataType( $dataType ) {
				}

				public function getAllPropertyInfo() {
					return [
						'P42' => [
							'type' => 'url'
						]
					];
				}
			}
		);
	}

	public function testNotEmpty() {
		$this->assertEquals(
			[
				new TypedDataValue( 'url', new StringValue( 'http://the.answer' ) )
			],
			$this->newTyper()->snaksToTypedValues( [
				new PropertyValueSnak( new PropertyId( 'P42' ), new StringValue( 'http://the.answer' ) )
			] )
		);
	}

}
