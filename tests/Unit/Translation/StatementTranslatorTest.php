<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use DataValues\DecimalValue;
use DataValues\StringValue;
use DataValues\UnboundedQuantityValue;
use MediaWiki\Extension\SemanticWikibase\Tests\TestFactory;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\Translation\StatementTranslator;
use PHPUnit\Framework\TestCase;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\StatementTranslator
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\DataValueTranslator
 */
class StatementTranslatorTest extends TestCase {

	private const SUBJECT_TITLE = 'TestPage';
	private const SUBJECT_NAMESPACE = NS_MAIN;

	public function testTranslateSimpleValue() {
		$this->assertEquals(
			new \SMWDIBlob( 'Kittens' ),
			$this->newTranslator()->statementToDataItem(
				new Statement(
					new PropertyValueSnak(
						new PropertyId( 'P1' ),
						new StringValue( 'Kittens' )
					)
				),
				$this->getSubject()
			)
		);
	}

	public function testTranslateContainerValue() {
		$container = $this->newTranslator()->statementToDataItem(
			new Statement(
				new PropertyValueSnak(
					new PropertyId( 'P1' ),
					new UnboundedQuantityValue(
						new DecimalValue( 42 ),
						'mega awesome'
					)
				)
			),
			$this->getSubject()
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

	private function newTranslator(): StatementTranslator {
		return TestFactory::newTestInstance()->getStatementTranslator();
	}

	private function getSubject(): DIWikiPage {
		return new DIWikiPage( self::SUBJECT_TITLE, self::SUBJECT_NAMESPACE );
	}

}
