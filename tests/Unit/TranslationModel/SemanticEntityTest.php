<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\TranslationModel;

use MediaWiki\Extension\SemanticWikibase\TranslationModel\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\SemanticEntity;
use PHPUnit\Framework\TestCase;

class SemanticEntityTest extends TestCase {

	public function testWhenEntityIsEmpty_getDataItemsForPropertyReturnsEmptyArray() {
		$this->assertSame(
			[],
			( new SemanticEntity() )->getDataItemsForProperty( FixedProperties::LABEL )
		);
	}

	public function testWhenPropertyHasValues_getDataItemsForPropertyReturnsThem() {
		$entity = new SemanticEntity();
		$entity->addPropertyValue( FixedProperties::LABEL, new \SMWDIBlob( 'hi' ) );
		$entity->addPropertyValue( FixedProperties::LABEL, new \SMWDIBlob( 'there' ) );

		$this->assertEquals(
			[ new \SMWDIBlob( 'hi' ), new \SMWDIBlob( 'there' ) ],
			$entity->getDataItemsForProperty( FixedProperties::LABEL )
		);
	}

	public function testWhenOtherValuesExist_getDataItemsForPropertyDoesNotReturnThem() {
		$entity = new SemanticEntity();
		$entity->addPropertyValue( FixedProperties::LABEL, new \SMWDIBlob( 'hi' ) );
		$entity->addPropertyValue( FixedProperties::DESCRIPTION, new \SMWDIBlob( 'nope' ) );
		$entity->addPropertyValue( FixedProperties::LABEL, new \SMWDIBlob( 'there' ) );

		$this->assertEquals(
			[ new \SMWDIBlob( 'hi' ), new \SMWDIBlob( 'there' ) ],
			$entity->getDataItemsForProperty( FixedProperties::LABEL )
		);
	}

}
