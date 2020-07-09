<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;

class ItemTranslator {

	private FingerprintTranslator $fingerprintTranslator;
	private StatementListTranslator $statementListTranslator;

	public function __construct( FingerprintTranslator $fingerprintTranslator, StatementListTranslator $statementListTranslator ) {
		$this->fingerprintTranslator = $fingerprintTranslator;
		$this->statementListTranslator = $statementListTranslator;
	}

	public function translateItem( Item $item ): SemanticEntity {
		$semanticEntity = new SemanticEntity();

		if ( $item->getId() === null ) {
			return $semanticEntity;
		}

		$this->addId( $semanticEntity, $item->getId() );
		$semanticEntity->add( $this->fingerprintTranslator->translateFingerprint( $item->getFingerprint() ) );
		$semanticEntity->add( $this->statementListTranslator->translateStatements( $item->getStatements() ) );

		return $semanticEntity;
	}

	private function addId( SemanticEntity $semanticEntity, ItemId $itemId ): void {
		$semanticEntity->addPropertyValue(
			FixedProperties::ID,
			new \SMWDIBlob( $itemId->getSerialization() )
		);
	}

}
