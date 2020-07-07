<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Tests;

use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
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

	protected function getPropertyTypeLookup(): PropertyDataTypeLookup {
		return $this->propertyDataTypeLookup;
	}

}
