<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use PHPUnit\Framework\TestCase;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator
 */
class ItemTranslatorTest extends TestCase {

	public function testEmptyItem() {
		$this->assertSame(
			[],
			$this->translate( new Item() )->getDataItemsForProperty( FixedProperties::ID )
		);
	}

	private function translate( Item $item ): SemanticEntity {
		$translator = SemanticWikibase::getGlobalInstance()->newItemTranslator();
		return $translator->itemToSmwValues( $item );
	}

	public function testItemIdIsTranslated() {
		$item = new Item( new ItemId( 'Q1' ) );

		$this->assertEquals(
			[ new \SMWDIBlob( 'Q1' ) ],
			$this->translate( $item )->getDataItemsForProperty( FixedProperties::ID )
		);
	}

	public function testStatementMainSnakValueIsTranslated() {
		$item = new Item( new ItemId( 'Q1' ) );
		$item->getStatements()->addNewStatement( new PropertyValueSnak(
			new PropertyId( 'P1' ),
			new StringValue( 'Hello there' )
		) );

		$this->assertEquals(
			[ new \SMWDIBlob( 'Hello there' ) ],
			$this->translate( $item )->getDataItemsForProperty( '___SWB_P1' )
		);
	}

	public function testMultipleStatementsForOneProperty() {
		$item = new Item( new ItemId( 'Q1' ) );

		$item->getStatements()->addNewStatement( new PropertyValueSnak(
			new PropertyId( 'P1' ),
			new StringValue( 'Hello there' )
		) );

		$item->getStatements()->addNewStatement( new PropertyValueSnak(
			new PropertyId( 'P1' ),
			new StringValue( 'fellow sentient' )
		) );

		$this->assertEquals(
			[
				new \SMWDIBlob( 'Hello there' ),
				new \SMWDIBlob( 'fellow sentient' )
			],
			$this->translate( $item )->getDataItemsForProperty( '___SWB_P1' )
		);
	}

	public function testOnlyPropertyValueSnaksGetAdded() {
		$item = new Item( new ItemId( 'Q1' ) );

		$item->getStatements()->addNewStatement( new PropertyNoValueSnak( new PropertyId( 'P1' ) ) );
		$item->getStatements()->addNewStatement( new PropertySomeValueSnak( new PropertyId( 'P1' ) ) );

		$this->assertSame(
			[],
			$this->translate( $item )->getDataItemsForProperty( '___SWB_P1' )
		);
	}

}
