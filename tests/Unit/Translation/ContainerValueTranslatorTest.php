<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use DataValues\BooleanValue;
use DataValues\DecimalValue;
use DataValues\QuantityValue;
use DataValues\UnboundedQuantityValue;
use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
use MediaWiki\Extension\SemanticWikibase\Translation\ContainerValueTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use PHPUnit\Framework\TestCase;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDIContainer;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\ContainerValueTranslator
 */
class ContainerValueTranslatorTest extends TestCase {

	private const SUBJECT_TITLE = 'TestPage';
	private const SUBJECT_NAMESPACE = NS_MAIN;

	public function testTranslateBoundedQuantity() {
		$container = $this->translate(
			new Statement(
				new PropertyValueSnak(
					new PropertyId( 'P1' ),
					new QuantityValue(
						new DecimalValue( 42 ),
						'mega awesome',
						new DecimalValue( 42.49 ),
						new DecimalValue( 41.51 ),
					)
				)
			)
		);

		$semanticData = $container->getSemanticData();

		$this->assertEquals(
			[ new \SMWDINumber( 42 ) ],
			$semanticData->getPropertyValues( new DIProperty( FixedProperties::QUANTITY_VALUE ) )
		);

		$this->assertEquals(
			[ new \SMWDINumber( 41.51 ) ],
			$semanticData->getPropertyValues( new DIProperty( FixedProperties::QUANTITY_LOWER_BOUND ) )
		);

		$this->assertEquals(
			[ new \SMWDINumber( 42.49 ) ],
			$semanticData->getPropertyValues( new DIProperty( FixedProperties::QUANTITY_UPPER_BOUND ) )
		);

		$this->assertEquals(
			[ new \SMWDIBlob( 'mega awesome' ) ],
			$semanticData->getPropertyValues( new DIProperty( FixedProperties::QUANTITY_UNIT ) )
		);
	}

	public function testTranslateUnboundedQuantity() {
		$container = $this->translate(
			new Statement(
				new PropertyValueSnak(
					new PropertyId( 'P1' ),
					new UnboundedQuantityValue(
						new DecimalValue( 42 ),
						'mega awesome'
					)
				)
			)
		);

		$semanticData = $container->getSemanticData();

		$this->assertEquals(
			[ new \SMWDINumber( 42 ) ],
			$semanticData->getPropertyValues( new DIProperty( FixedProperties::QUANTITY_VALUE ) )
		);

		$this->assertEquals(
			[ new \SMWDIBlob( 'mega awesome' ) ],
			$semanticData->getPropertyValues( new DIProperty( FixedProperties::QUANTITY_UNIT ) )
		);
	}

	private function translate( Statement $statement ): SMWDIContainer {
		return $this->newTranslator()->statementToDataItem(
			$statement,
			$this->getSubject()
		);
	}

	private function newTranslator(): ContainerValueTranslator {
		return SemanticWikibase::getGlobalInstance()->getContainerValueTranslator();
	}

	private function getSubject(): DIWikiPage {
		return new DIWikiPage( self::SUBJECT_TITLE, self::SUBJECT_NAMESPACE );
	}

	public function testSupportsQuantityStatements() {
		$this->assertTrue( $this->newTranslator()->supportsStatement(
			new Statement(
				new PropertyValueSnak(
					new PropertyId( 'P1' ),
					new UnboundedQuantityValue(
						new DecimalValue( 42 ),
						'mega awesome'
					)
				)
			)
		) );
	}

	public function testDoesNotSupportNonValueStatements() {
		$this->assertFalse( $this->newTranslator()->supportsStatement(
			new Statement(
				new PropertySomeValueSnak(
					new PropertyId( 'P1' )
				)
			)
		) );
	}

	public function testDoesNotSupportBooleanValueStatements() {
		$this->assertFalse( $this->newTranslator()->supportsStatement(
			new Statement(
				new PropertyValueSnak(
					new PropertyId( 'P1' ),
					new BooleanValue( true )
				)
			)
		) );
	}

}
