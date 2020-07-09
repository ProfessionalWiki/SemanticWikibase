<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests;

use DataValues\MonolingualTextValue;
use PHPUnit\Framework\TestCase;
use SMW\DIProperty;
use SMWDIContainer;

abstract class SWBTestCase extends TestCase {

	/**
	 * The complexity in both tests and production code caused by SMWs "smart" reuse of the record type is unbelievable.
	 * Thanks James.
	 *
	 * @param MonolingualTextValue[] $expected
	 * @param \SMWDataItem[] $actual
	 */
	protected function assertHasMonolingualTexts( array $expected, array $actual ): void {
		$this->assertContainsOnlyInstancesOf( SMWDIContainer::class, $actual );
		$this->assertSameSize( $expected, $actual );

		foreach ( $actual as $index => $textContainer ) {
			if ( $textContainer instanceof SMWDIContainer ) {
				$this->assertSame(
					$expected[$index]->getText(),
					$textContainer->getSemanticData()->getPropertyValues( new DIProperty( '_TEXT' ) )[0]->getSerialization()
				);
				$this->assertSame(
					$expected[$index]->getLanguageCode(),
					$textContainer->getSemanticData()->getPropertyValues( new DIProperty( '_LCODE' ) )[0]->getSerialization()
				);
			}
		}
	}

}
