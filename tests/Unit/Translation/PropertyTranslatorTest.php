<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use DataValues\MonolingualTextValue;
use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\Tests\SWBTestCase;
use MediaWiki\Extension\SemanticWikibase\Tests\TestFactory;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use SMW\DIWikiPage;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\PropertyTranslator
 */
class PropertyTranslatorTest extends SWBTestCase {

	private const ID = 'P1';
	private const TYPE = 'string';

	public function testIdIsTranslated() {
		$this->assertEquals(
			[ new \SMWDIBlob( self::ID ) ],
			$this->translate( $this->newProperty() )->getDataItemsForProperty( FixedProperties::ID )
		);
	}

	private function newProperty(): Property {
		return new Property(
			new PropertyId( self::ID ),
			null,
			self::TYPE
		);
	}

	private function translate( Property $property ): SemanticEntity {
		return TestFactory::newTestInstance()->newPropertyTranslator(
			DIWikiPage::newFromTitle( \Title::newFromText( 'Item:Q1' ) )
		)->translateProperty( $property );
	}

	public function testLabelIsTranslated() {
		$property = $this->newProperty();
		$property->setLabel( 'en', 'Kittens' );

		$this->assertHasMonolingualTexts(
			[
				new MonolingualTextValue( 'en', 'Kittens' )
			],
			$this->translate( $property )->getDataItemsForProperty( FixedProperties::LABEL )
		);
	}

}
