<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\Translation\ItemTranslator;
use SMW\SemanticData;
use Title;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\Repo\WikibaseRepo;

class SemanticDataUpdate {

	private ItemTranslator $itemTranslator;

	public function __construct( ItemTranslator $itemTranslator ) {
		$this->itemTranslator = $itemTranslator;
	}

	public function run( SemanticData $semanticData ): void {
		$title = $semanticData->getSubject()->getTitle();

		if ( $title === null || !$this->isWikibaseItem( $title ) ) {
			return;
		}

		$semanticEntity = $this->getSemanticEntityFromItemTitle( $title );


		$semanticData->importDataFrom( $semanticEntity->toSemanticData( $semanticData->getSubject() ) );
	}

	private function isWikibaseItem( Title $title ): bool {
		return $title->getNamespace() === WB_NS_ITEM;
	}

	private function getSemanticEntityFromItemTitle( Title $title ): ?SemanticEntity {
		$item = WikibaseRepo::getDefaultInstance()->getItemLookup()->getItemForId( new ItemId( $title->getText() ) );

		if ( $item === null ) {
			return null;
		}

		return $this->itemTranslator->itemToSmwValues( $item );
	}

}
