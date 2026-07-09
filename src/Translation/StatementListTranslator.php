<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;
use MWException;
use SMW\PropertyRegistry;
use SMW\Subobject;
use SMW\StoreFactory;
use SMW\Store;

class StatementListTranslator {

	private StatementTranslator $statementTranslator;
	private DIWikiPage $subject;

	public function __construct( StatementTranslator $statementTranslator, DIWikiPage $subject ) {
		$this->statementTranslator = $statementTranslator;
		$this->subject = $subject;
	}

	public function translateStatements( StatementList $statements ): SemanticEntity {
		$semanticEntity = new SemanticEntity();

        $i = 1;
		foreach ( $statements->getBestStatements()->getByRank( [ Statement::RANK_PREFERRED, Statement::RANK_NORMAL ] ) as $statement ) {
			$this->addStatement( $semanticEntity, $statement, $i );
            $i++;
		}

		return $semanticEntity;
	}

	private function addStatement( SemanticEntity $semanticEntity, Statement $statement, $statementNr ): void {
		$mainSnakDataItem = $this->statementTranslator->statementToDataItem( $statement, $this->subject );

		if ( $mainSnakDataItem !== null ) {
			// first link the statement value directly via property name 
			// TODO: belongs in statement translator
			if ( $mainSnakDataItem instanceof \SMWDIContainer ) {
				$semanticEntity->addPropertyValue( DIProperty::TYPE_SUBOBJECT, $mainSnakDataItem );

				$semanticEntity->addPropertyValue(
					$this->NumericPropertyIdForStatement( $statement ),
					$mainSnakDataItem->getSemanticData()->getSubject()
				);
			}
			else {
				$semanticEntity->addPropertyValue(
					$this->NumericPropertyIdForStatement( $statement ),
					$mainSnakDataItem
				);
			}

            // for complex data e.g. using qualifiers, we add the statement additionally as a subobject
			$subObjs = $this->statementTranslator->statementToSubobjects($statement, $this->subject, $statementNr);
			foreach($subObjs as $subObject ){
				$semanticEntity->addPropertyValue( DIProperty::TYPE_SUBOBJECT, $subObject->getContainer() );
			}
			//$semanticEntity->addPropertyValue( 'STATEMENTS', $subObject->getContainer() );

			// now process qualifiers
			/*
			$qualifiers = $statement->getQualifiers();
		    $i = 1;
			foreach( $qualifiers as $actQualifier) {
                $actQualifierDataItem = $this->statementTranslator->snakToDataItem($actQualifier, $statement, $this->subject);
		
				// TODO: do we need to handle type SMWDIContainer here?
				//throw new MWException(json_encode($actQualifier->getDataValue()));
				$newPropId = $statement->getPropertyId()->getSerialization()."hasQualifier".$actQualifier->getPropertyId();
				$semanticEntity->addPropertyValue(
					$newPropId,
					$actQualifierDataItem
				);
				$i++;			
			}*/
		}
	}

	private function NumericPropertyIdForStatement( Statement $statement ): string {
		return UserDefinedProperties::idFromWikibaseProperty( $statement->getPropertyId() );
	}

}
