<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\SMW;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity
 */
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

	public function testMergeWithSameProperties() {
		$entity1 = new SemanticEntity();
		$entity1->addPropertyValue( FixedProperties::LABEL, new \SMWDIBlob( 'hi' ) );

		$entity2 = new SemanticEntity();
		$entity2->addPropertyValue( FixedProperties::LABEL, new \SMWDIBlob( 'there' ) );

		$this->assertEquals(
			[ new \SMWDIBlob( 'hi' ), new \SMWDIBlob( 'there' ) ],
			$entity1->functionalMerge( $entity2 )->getDataItemsForProperty( FixedProperties::LABEL )
		);
	}

	public function testMergeWithDifferentProperties() {
		$entity1 = new SemanticEntity();
		$entity1->addPropertyValue( FixedProperties::LABEL, new \SMWDIBlob( 'hi' ) );

		$entity2 = new SemanticEntity();
		$entity2->addPropertyValue( FixedProperties::DESCRIPTION, new \SMWDIBlob( 'there' ) );

		$merged = $entity1->functionalMerge( $entity2 );

		$this->assertEquals(
			[ new \SMWDIBlob( 'hi' ) ],
			$merged->getDataItemsForProperty( FixedProperties::LABEL )
		);

		$this->assertEquals(
			[ new \SMWDIBlob( 'there' ) ],
			$merged->getDataItemsForProperty( FixedProperties::DESCRIPTION )
		);
	}

	public function testMergeIsFunctional() {
		$entity1 = new SemanticEntity();

		$entity2 = new SemanticEntity();
		$entity2->addPropertyValue( FixedProperties::DESCRIPTION, new \SMWDIBlob( 'there' ) );

		$entity1->functionalMerge( $entity2 );

		$this->assertSame(
			[],
			$entity1->getDataItemsForProperty( FixedProperties::DESCRIPTION )
		);
	}

}
