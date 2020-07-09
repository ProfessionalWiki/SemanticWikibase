<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\SMW\SemanticProperty;
use MediaWiki\Extension\SemanticWikibase\Translation\ContainerValueTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\DataValueTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\FingerprintTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\MonoTextTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\PropertyTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\PropertyTypeTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\StatementTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\TermTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\UserDefinedProperties;
use SMW\DataValueFactory;
use SMW\DIWikiPage;
use SMW\PropertyRegistry;
use Wikibase\DataModel\Services\Lookup\PropertyDataTypeLookup;
use Wikibase\Repo\WikibaseRepo;

class SemanticWikibase {

	protected static ?self $instance;
	private Configuration $config;

	public static function getGlobalInstance(): self {
		if ( !isset( self::$instance ) ) {
			self::$instance = self::newDefault();
		}

		return self::$instance;
	}

	protected static function newDefault(): self {
		return new static( Configuration::newFromGlobals( $GLOBALS ) );
	}

	public function __construct( Configuration $config ) {
		$this->config = $config;
	}

	public function getSemanticDataUpdate(): SemanticDataUpdate {
		return new SemanticDataUpdate( $this->newItemTranslator(), $this->newPropertyTranslator() );
	}

	public function newItemTranslator(): ItemTranslator {
		return new ItemTranslator(
			$this->getStatementTranslator()
		);
	}

	public function newPropertyTranslator(): PropertyTranslator {
		return new PropertyTranslator(
			$this->getStatementTranslator()
		);
	}

	public function newFingerprintTranslator( SemanticEntity $semanticEntity, DIWikiPage $subject ) {
		return new FingerprintTranslator(
			$semanticEntity,
			new TermTranslator( $this->getMonoTextTranslator(), $subject )
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
		return new DataValueTranslator();
	}

	private function getDataValueFactory(): DataValueFactory {
		return DataValueFactory::getInstance();
	}

	public function getContainerValueTranslator(): ContainerValueTranslator {
		return new ContainerValueTranslator(
			$this->getDataValueTranslator(),
			$this->getMonoTextTranslator()
		);
	}

	public function getMonoTextTranslator(): MonoTextTranslator {
		return new MonoTextTranslator(
			$this->getDataValueFactory()
		);
	}

	protected function getPropertyTypeLookup(): PropertyDataTypeLookup {
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
			$this->config->getLanguageCode()
		);
	}

	private function getPropertyTypeTranslator(): PropertyTypeTranslator {
		return new PropertyTypeTranslator();
	}

}
