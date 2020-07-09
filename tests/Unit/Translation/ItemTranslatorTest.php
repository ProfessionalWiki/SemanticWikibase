<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use DataValues\DecimalValue;
use DataValues\MonolingualTextValue;
use DataValues\StringValue;
use DataValues\UnboundedQuantityValue;
use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\Tests\SWBTestCase;
use MediaWiki\Extension\SemanticWikibase\Tests\TestFactory;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDIContainer;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\StatementListTranslator
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\FingerprintTranslator
 */
class ItemTranslatorTest extends SWBTestCase {

	public function testEmptyItem() {
		$this->assertSame(
			[],
			$this->translate( new Item() )->getDataItemsForProperty( FixedProperties::ID )
		);
	}

	private function translate( Item $item ): SemanticEntity {
		return TestFactory::newTestInstance()->newItemTranslator(
			DIWikiPage::newFromTitle( \Title::newFromText( 'Item:Q1' ) )
		)->translateItem( $item );
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

	public function testTranslationToSubobject() {
		$item = new Item( new ItemId( 'Q1' ) );

		$item->getStatements()->addNewStatement(
			new PropertyValueSnak(
				new PropertyId( 'P1' ),
				new UnboundedQuantityValue(
					new DecimalValue( 42 ),
					'mega awesome'
				)
			),
			null,
			null,
			'Q1$d40469c7-4586-70f5-7a75-cccef9381c4c'
		);

		$semanticEntity = $this->translate( $item );

		$this->assertEquals(
			[
				new DIWikiPage(
					'Q1',
					WB_NS_ITEM,
					'',
					'Q1$d40469c7-4586-70f5-7a75-cccef9381c4c'
				)
			],
			$semanticEntity->getDataItemsForProperty( '___SWB_P1' )
		);

		$this->assertHasSubobjectWithPropertyValue(
			$semanticEntity,
			FixedProperties::QUANTITY_VALUE,
			new \SMWDINumber( 42 )
		);
	}

	private function assertHasSubobjectWithPropertyValue( SemanticEntity $semanticEntity, string $propertyId, \SMWDataItem $expectedDataItem ) {
		/**
		 * @var SMWDIContainer $container
		 */
		$container = $semanticEntity->getDataItemsForProperty( DIProperty::TYPE_SUBOBJECT )[0];

		$this->assertEquals(
			[ $expectedDataItem ],
			$container->getSemanticData()->getPropertyValues( new DIProperty( $propertyId ) )
		);
	}

	public function testItemLabelsAreTranslated() {
		$item = new Item( new ItemId( 'Q1' ) );
		$item->setLabel( 'en', 'English' );
		$item->setLabel( 'de', 'German' );

		$this->assertHasMonolingualTexts(
			[
				new MonolingualTextValue( 'en', 'English' ),
				new MonolingualTextValue( 'de', 'German' )
			],
			$this->translate( $item )->getDataItemsForProperty( FixedProperties::LABEL )
		);
	}

	public function testItemDescriptionsAreTranslated() {
		$item = new Item( new ItemId( 'Q1' ) );
		$item->setDescription( 'en', 'English' );
		$item->setDescription( 'de', 'German' );

		$this->assertHasMonolingualTexts(
			[
				new MonolingualTextValue( 'en', 'English' ),
				new MonolingualTextValue( 'de', 'German' )
			],
			$this->translate( $item )->getDataItemsForProperty( FixedProperties::DESCRIPTION )
		);
	}

	public function testItemAliasesAreTranslated() {
		$item = new Item( new ItemId( 'Q1' ) );
		$item->setAliases( 'en', [ 'Cat', 'Kittens' ] );
		$item->setAliases( 'de', [ 'Katze' ] );

		$this->assertHasMonolingualTexts(
			[
				new MonolingualTextValue( 'en', 'Cat' ),
				new MonolingualTextValue( 'en', 'Kittens' ),
				new MonolingualTextValue( 'de', 'Katze' )
			],
			$this->translate( $item )->getDataItemsForProperty( FixedProperties::ALIAS )
		);
	}

}
