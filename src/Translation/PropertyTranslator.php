<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use Wikibase\DataModel\Entity\Property;
use Wikibase\DataModel\Entity\PropertyId;

class PropertyTranslator {

	private FingerprintTranslator $fingerprintTranslator;
	private StatementListTranslator $statementListTranslator;

	public function __construct( FingerprintTranslator $fingerprintTranslator, StatementListTranslator $statementListTranslator ) {
		$this->fingerprintTranslator = $fingerprintTranslator;
		$this->statementListTranslator = $statementListTranslator;
	}

	public function translateProperty( Property $property ): SemanticEntity {
		$semanticEntity = new SemanticEntity();

		if ( $property->getId() === null ) {
			return $semanticEntity;
		}

		$this->addId( $semanticEntity, $property->getId() );
		$semanticEntity->add( $this->fingerprintTranslator->translateFingerprint( $property->getFingerprint() ) );
		$semanticEntity->add( $this->statementListTranslator->translateStatements( $property->getStatements() ) );

		return $semanticEntity;
	}

	private function addId( SemanticEntity $semanticEntity, PropertyId $itemId ): void {
		$semanticEntity->addPropertyValue(
			FixedProperties::ID,
			new \SMWDIBlob( $itemId->getSerialization() )
		);
	}

}
