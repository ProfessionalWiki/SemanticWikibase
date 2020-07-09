<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests;

use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
use MediaWiki\Extension\SemanticWikibase\Translation\ContainerValueTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\MonoTextTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\StatementTranslator;
use SMW\DIWikiPage;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\InMemoryDataTypeLookup;
use Wikibase\DataModel\Services\Lookup\PropertyDataTypeLookup;

class TestFactory extends SemanticWikibase {

	private PropertyDataTypeLookup $propertyDataTypeLookup;

	/**
	 * Initializes a new test instance, updates the global instance used by production code and returns it.
	 */
	public static function newTestInstance(): self {
		self::$instance = self::newDefault();

		self::$instance->initialize();

		return self::$instance;
	}

	private function initialize(): void {
		$this->propertyDataTypeLookup = $this->newPropertyTypeLookup();
	}

	private function newPropertyTypeLookup(): InMemoryDataTypeLookup {
		$lookup = new InMemoryDataTypeLookup();

		$lookup->setDataTypeForProperty( new PropertyId( 'P1' ), 'string' );

		return $lookup;
	}

	public function newPropertyTranslator( DIWikiPage $subject ) {
		return $this->getTranslatorFactory()->newPropertyTranslator( $subject );
	}

	protected function getPropertyTypeLookup(): PropertyDataTypeLookup {
		return $this->propertyDataTypeLookup;
	}

	public function getStatementTranslator(): StatementTranslator {
		return $this->getTranslatorFactory()->getStatementTranslator();
	}

	public function newItemTranslator( DIWikiPage $subject ): ItemTranslator {
		return $this->getTranslatorFactory()->newItemTranslator( $subject );
	}

	public function getMonoTextTranslator(): MonoTextTranslator {
		return $this->getTranslatorFactory()->getMonoTextTranslator();
	}

	public function getContainerValueTranslator(): ContainerValueTranslator {
		return $this->getTranslatorFactory()->getContainerValueTranslator();
	}

}
