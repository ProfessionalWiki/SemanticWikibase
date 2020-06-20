<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use SMW\DIProperty;
use SMW\SemanticData;
use Title;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\Repo\WikibaseRepo;

class SemanticDataUpdate {

	public function run( SemanticData $semanticData ): void {
		$title = $semanticData->getSubject()->getTitle();

		if ( $title === null ) {
			return;
		}

		if ( $this->isWikibaseItem( $title ) ) {
			$this->storeItemPage( $semanticData, $title );
		}
	}

	private function isWikibaseItem( Title $title ): bool {
		return $title->getNamespace() === WB_NS_ITEM;
	}

	private function storeItemPage( SemanticData $semanticData, Title $title ): void {
		$item = WikibaseRepo::getDefaultInstance()->getItemLookup()->getItemForId( new ItemId( $title->getText() ) );

		if ( $item === null ) {
			return;
		}

		$this->storeItem( $semanticData, $item );
	}

	private function storeItem( SemanticData $semanticData, Item $item ): void {
		$builder = new SemanticItemBuilder();

		foreach ( $builder->itemToSmwValues( $item ) as $propertyValue ) {
			$semanticData->addPropertyObjectValue(
				$propertyValue->getProperty(),
				$propertyValue->getValue()
			);
		}
	}

}
