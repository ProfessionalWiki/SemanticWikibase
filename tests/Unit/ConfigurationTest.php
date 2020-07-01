<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests;

use MediaWiki\Extension\SemanticWikibase\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\SemanticWikibase\Configuration
 */
class ConfigurationTest extends TestCase {

	public function testLanguageIsWikiLanguageByDefault() {
		$this->assertSame(
			'nl',
			Configuration::newFromGlobals( [
				'wgLanguageCode' => 'nl',
				'wgSemanticWikibaseLanguage' => '',
			] )->getLanguageCode()
		);
	}

	public function testWhenLanguageIsConfiguredItIsUsed() {
		$this->assertSame(
			'de',
			Configuration::newFromGlobals( [
				'wgLanguageCode' => 'nl',
				'wgSemanticWikibaseLanguage' => 'de',
			] )->getLanguageCode()
		);
	}

}
