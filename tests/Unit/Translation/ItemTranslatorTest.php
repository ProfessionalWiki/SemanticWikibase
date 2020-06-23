<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\PropertyValuePair;
use MediaWiki\Extension\SemanticWikibase\Translators\ItemTranslator;
use PHPUnit\Framework\TestCase;
use SMW\DIProperty;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translators\ItemTranslator
 */
class ItemTranslatorTest extends TestCase {

	public function testEmptyItem() {
		$this->assertSame(
			[],
			$this->translate( new Item() )
		);
	}

	/**
	 * @param Item $item
	 * @return PropertyValuePair[]
	 */
	private function translate( Item $item ): array {
		$translator = new ItemTranslator();
		return $translator->itemToSmwValues( $item )->getPropertyValuePairs();
	}

	public function testFoo() {
		$item = new Item( new ItemId( 'Q1' ) );
		$item->getStatements()->addNewStatement( new PropertyValueSnak(
			new PropertyId( 'P1' ),
			new StringValue( 'Hello there' )
		) );

		$this->assertEquals(
			[
				new PropertyValuePair(
					new DIProperty( FixedProperties::ID ),
					new \SMWDIBlob( 'Q1' )
				),
				new PropertyValuePair(
					new DIProperty( '___SWB_P1' ),
					new \SMWDIBlob( 'Hello there' )
				)
			],
			$this->translate( $item )
		);
	}

}
