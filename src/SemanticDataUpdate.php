<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\PropertyTranslator;
use SMW\SemanticData;
use Title;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\Repo\WikibaseRepo;

class SemanticDataUpdate {

	private ItemTranslator $itemTranslator;
	private PropertyTranslator $propertyTranslator;

	public function __construct( ItemTranslator $itemTranslator, PropertyTranslator $propertyTranslator ) {
		$this->itemTranslator = $itemTranslator;
		$this->propertyTranslator = $propertyTranslator;
	}

	public function run( SemanticData $semanticData ): void {
		$title = $semanticData->getSubject()->getTitle();

		if ( $title === null || !$this->isSupportedWikibaseTitle( $title ) ) {
			return;
		}

		$semanticData->importDataFrom(
			$this->getSemanticEntityFromTitle( $title )->toSemanticData( $semanticData->getSubject() )
		);
	}

	private function isSupportedWikibaseTitle( Title $title ): bool {
		return $title->getNamespace() === WB_NS_ITEM
			|| $title->getNamespace() === WB_NS_PROPERTY;
	}

	private function getSemanticEntityFromTitle( Title $title ): ?SemanticEntity {
		if ( $title->getNamespace() === WB_NS_ITEM ) {
			$item = WikibaseRepo::getDefaultInstance()->getItemLookup()->getItemForId( new ItemId( $title->getText() ) );

			if ( $item === null ) {
				return null;
			}

			return $this->itemTranslator->itemToSmwValues( $item );
		}

		$property = WikibaseRepo::getDefaultInstance()->getPropertyLookup()->getPropertyForId( new PropertyId( $title->getText() ) );

		if ( $property === null ) {
			return null;
		}

		return $this->propertyTranslator->propertyToSmwValues( $property );
	}

}
