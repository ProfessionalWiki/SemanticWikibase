<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Term\Fingerprint;

class ItemTranslator {

	private StatementTranslator $statementTranslator;

	private SemanticEntity $semanticEntity;
	private DIWikiPage $subject;


	public function __construct( StatementTranslator $statementTranslator ) {
		$this->statementTranslator = $statementTranslator;
	}

	public function itemToSmwValues( Item $item ): SemanticEntity {
		if ( $item->getId() === null ) {
			return new SemanticEntity();
		}

		$this->semanticEntity = new SemanticEntity();
		$this->subject = DIWikiPage::newFromText( $item->getId()->getSerialization(), WB_NS_ITEM );

		$this->addId( $item->getId() );
		$this->addFingerprint( $item->getFingerprint() );
		$this->addStatements( $item->getStatements()->getBestStatements()->getByRank( [ Statement::RANK_PREFERRED, Statement::RANK_NORMAL ] ) );

		return $this->semanticEntity;
	}

	private function addId( ItemId $itemId ): void {
		$this->semanticEntity->addPropertyValue(
			FixedProperties::ID,
			new \SMWDIBlob( $itemId->getSerialization() )
		);
	}

	private function addFingerprint( Fingerprint $fingerprint ): void {
		SemanticWikibase::getGlobalInstance()
			->newFingerprintTranslator( $this->semanticEntity, $this->subject )
			->addFingerprintValues( $fingerprint );
	}

	private function addStatements( StatementList $statements ): void {
		foreach ( $statements as $statement ) {
			$this->addStatement( $statement );
		}
	}

	private function addStatement( Statement $statement ): void {
		$dataItem = $this->statementTranslator->statementToDataItem( $statement, $this->subject );

		if ( $dataItem !== null ) {
			if ( $dataItem instanceof \SMWDIContainer ) {
				$this->semanticEntity->addPropertyValue( DIProperty::TYPE_SUBOBJECT, $dataItem );

				$this->semanticEntity->addPropertyValue(
					$this->propertyIdForStatement( $statement ),
					$dataItem->getSemanticData()->getSubject()
				);
			}
			else {
				$this->semanticEntity->addPropertyValue(
					$this->propertyIdForStatement( $statement ),
					$dataItem
				);
			}

		}
	}

	private function propertyIdForStatement( Statement $statement ): string {
		return UserDefinedProperties::idFromWikibaseProperty( $statement->getPropertyId() );
	}

}
