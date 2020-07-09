<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\PropertyTranslator;
use MediaWiki\Extension\SemanticWikibase\Translation\TranslatorFactory;
use SMW\DIWikiPage;
use SMW\SemanticData;
use Title;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\ItemLookup;
use Wikibase\DataModel\Services\Lookup\PropertyLookup;

class SemanticDataUpdate {

	private TranslatorFactory $translatorFactory;
	private ItemLookup $itemLookup;
	private PropertyLookup $propertyLookup;

	public function __construct( TranslatorFactory $translatorFactory, ItemLookup $itemLookup, PropertyLookup $propertyLookup ) {
		$this->translatorFactory = $translatorFactory;
		$this->itemLookup = $itemLookup;
		$this->propertyLookup = $propertyLookup;
	}

	public function run( SemanticData $semanticData ): void {
		$title = $semanticData->getSubject()->getTitle();

		if ( $title === null ) {
			return;
		}

		$semanticData->importDataFrom(
			$this->getSemanticEntityFromTitle( $title )->toSemanticData( $semanticData->getSubject() )
		);
	}

	private function getSemanticEntityFromTitle( Title $title ): SemanticEntity {
		if ( $title->getNamespace() === WB_NS_ITEM ) {
			return $this->getSemanticEntityForItemTitle( $title );
		}

		if ( $title->getNamespace() === WB_NS_PROPERTY ) {
			return $this->getSemanticEntityForPropertyTitle( $title );
		}

		return new SemanticEntity();
	}

	private function getSemanticEntityForItemTitle( Title $title ): SemanticEntity {
		return $this->newItemTranslator( $title )->translateItem(
			$this->itemLookup->getItemForId( new ItemId( $title->getText() ) )
		);
	}

	private function newItemTranslator( Title $title ): ItemTranslator {
		return $this->translatorFactory->newItemTranslator( DIWikiPage::newFromTitle( $title ) );
	}

	private function getSemanticEntityForPropertyTitle( Title $title ): SemanticEntity {
		return $this->newPropertyTranslator( $title )->translateProperty(
			$this->propertyLookup->getPropertyForId( new PropertyId( $title->getText() ) )
		);
	}

	private function newPropertyTranslator( Title $title ): PropertyTranslator {
		return $this->translatorFactory->newPropertyTranslator( DIWikiPage::newFromTitle( $title ) );
	}

}
