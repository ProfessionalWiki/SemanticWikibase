<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests\Translation;

use DataValues\MonolingualTextValue;
use MediaWiki\Extension\SemanticWikibase\Tests\TestFactory;
use PHPUnit\Framework\TestCase;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDataItem;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Translation\MonoTextTranslator
 */
class MonoTextTranslatorTest extends TestCase {

	private const TEXT = 'fluffy bunnies';
	private const LANGUAGE = 'en';
	private const SUBJECT_TITLE = 'Q12345';

	public function testMonolingualText() {
		$translator = TestFactory::newTestInstance()->getMonoTextTranslator();

		$subject = new DIWikiPage( self::SUBJECT_TITLE, WB_NS_ITEM );

		$dataItem = $translator->valueToDataItem(
			new MonolingualTextValue( self::LANGUAGE, self::TEXT ),
			$subject
		);

		$this->assertIsContainer( $dataItem );
		$this->assertHasText( $dataItem );
		$this->assertHasLanguage( $dataItem );
		$this->assertHasSubject( $subject, $dataItem );
	}

	private function assertIsContainer( \SMWDIContainer $dataItem ): void {
		$this->assertSame( SMWDataItem::TYPE_CONTAINER, $dataItem->getDIType() );
	}

	private function assertHasText( \SMWDIContainer $dataItem ): void {
		$this->assertEquals(
			[ 'fluffy bunnies' ],
			$dataItem->getSemanticData()->getPropertyValues( new DIProperty( '_TEXT' ) )
		);
	}

	private function assertHasLanguage( \SMWDIContainer $dataItem ): void {
		$this->assertEquals(
			[ 'en' ],
			$dataItem->getSemanticData()->getPropertyValues( new DIProperty( '_LCODE' ) )
		);
	}

	private function assertHasSubject( DIWikiPage $subject, \SMWDIContainer $dataItem ): void {
		$this->assertEquals(
			$subject->getDBkey(),
			$dataItem->getSemanticData()->getSubject()->getDBkey()
		);
	}

}
