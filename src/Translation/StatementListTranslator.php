<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;

class StatementListTranslator {

	private StatementTranslator $statementTranslator;
	private DIWikiPage $subject;

	public function __construct( StatementTranslator $statementTranslator, DIWikiPage $subject ) {
		$this->statementTranslator = $statementTranslator;
		$this->subject = $subject;
	}

	public function translateStatements( StatementList $statements ): SemanticEntity {
		$semanticEntity = new SemanticEntity();

		foreach ( $statements->getBestStatements()->getByRank( [ Statement::RANK_PREFERRED, Statement::RANK_NORMAL ] ) as $statement ) {
			$this->addStatement( $semanticEntity, $statement );
		}

		return $semanticEntity;
	}

	private function addStatement( SemanticEntity $semanticEntity, Statement $statement ): void {
		$dataItem = $this->statementTranslator->statementToDataItem( $statement, $this->subject );

		if ( $dataItem !== null ) {
			// TODO: belongs in statement translator
			if ( $dataItem instanceof \SMWDIContainer ) {
				$semanticEntity->addPropertyValue( DIProperty::TYPE_SUBOBJECT, $dataItem );

				$semanticEntity->addPropertyValue(
					$this->propertyIdForStatement( $statement ),
					$dataItem->getSemanticData()->getSubject()
				);
			}
			else {
				$semanticEntity->addPropertyValue(
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
