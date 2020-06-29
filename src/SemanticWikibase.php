<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\SMW\SemanticProperty;
use MediaWiki\Extension\SemanticWikibase\Translation\UserDefinedProperties;
use MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\PropertyTypeTranslator;
use SMW\DataValueFactory;
use SMW\PropertyRegistry;
use Wikibase\DataModel\Services\Lookup\PropertyDataTypeLookup;
use Wikibase\Repo\WikibaseRepo;

class SemanticWikibase {

	public static function getGlobalInstance(): self {
		return new self();
	}

	public function getSemanticDataUpdate(): SemanticDataUpdate {
		return new SemanticDataUpdate( $this->newItemTranslator() );
	}

	public function newItemTranslator(): ItemTranslator {
		return new ItemTranslator(
			DataValueFactory::getInstance(),
			$this->getPropertyTypeLookup()
		);
	}

	private function getPropertyTypeLookup(): PropertyDataTypeLookup {
		return WikibaseRepo::getDefaultInstance()->getPropertyDataTypeLookup();
	}

	public function registerProperties( PropertyRegistry $propertyRegistry ) {
		foreach ( $this->getAllProperties() as $property ) {
			$propertyRegistry->registerProperty(
				$property->getId(),
				$property->getType(),
				$property->getLabel(),
				true,
				false
			);
		}
	}

	/**
	 * @return SemanticProperty[]
	 */
	private function getAllProperties(): array {
		return array_merge(
			$this->getFixedProperties()->getAll(),
			$this->getUserDefinedProperties()->getAll()
		);
	}

	public function getFixedProperties(): FixedProperties {
		return new FixedProperties();
	}

	public function getUserDefinedProperties(): UserDefinedProperties {
		return new UserDefinedProperties(
			WikibaseRepo::getDefaultInstance()->getWikibaseServices()->getPropertyInfoLookup(),
			$this->getPropertyTypeTranslator()
		);
	}

	private function getPropertyTypeTranslator(): PropertyTypeTranslator {
		return new PropertyTypeTranslator();
	}

}
