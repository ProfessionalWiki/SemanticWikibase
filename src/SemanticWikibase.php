<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Extension\SemanticWikibase\TranslationModel\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\SemanticProperty;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\UserDefinedProperties;
use SMW\PropertyRegistry;
use Wikibase\Repo\WikibaseRepo;

class SemanticWikibase {

	public static function getGlobalInstance(): self {
		return new self();
	}

	public function getSemanticDataUpdate(): SemanticDataUpdate {
		return new SemanticDataUpdate();
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
			WikibaseRepo::getDefaultInstance()->getWikibaseServices()->getPropertyInfoLookup()
		);
	}

}
