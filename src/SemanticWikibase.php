<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticProperty;
use MediaWiki\Extension\SemanticWikibase\Translation\ContainerValueTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\DataValueTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\PropertyTypeTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\StatementTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\UserDefinedProperties;
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
			$this->getDataValueFactory(),
			$this->getStatementTranslator()
		);
	}

	public function getStatementTranslator(): StatementTranslator {
		return new StatementTranslator(
			$this->getDataValueTranslator(),
			$this->getContainerValueTranslator(),
			$this->getPropertyTypeLookup()
		);
	}

	private function getDataValueTranslator(): DataValueTranslator {
		return new DataValueTranslator(
			$this->getDataValueFactory()
		);
	}

	private function getDataValueFactory(): DataValueFactory {
		return DataValueFactory::getInstance();
	}

	public function getContainerValueTranslator(): ContainerValueTranslator {
		return new ContainerValueTranslator(
			$this->getDataValueTranslator()
		);
	}

	private function getPropertyTypeLookup(): PropertyDataTypeLookup {
		return $this->getWikibaseRepo()->getPropertyDataTypeLookup();
	}

	private function getWikibaseRepo(): WikibaseRepo {
		return WikibaseRepo::getDefaultInstance();
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

			foreach ( $property->getAliases() as $alias ) {
				$propertyRegistry->registerPropertyAlias( $property->getId(), $alias );
			}
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
			$this->getWikibaseRepo()->getWikibaseServices()->getPropertyInfoLookup(),
			$this->getPropertyTypeTranslator(),
			$this->getWikibaseRepo()->getTermLookup(),
			'en'
		);
	}

	private function getPropertyTypeTranslator(): PropertyTypeTranslator {
		return new PropertyTypeTranslator();
	}

}
