<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticEntity;
use SMWDataItem;
use Wikibase\DataModel\Term\AliasGroupList;
use Wikibase\DataModel\Term\Fingerprint;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\TermList;

class FingerprintTranslator {

	private TermTranslator $termTranslator;

	public function __construct( TermTranslator $termTranslator ) {
		$this->termTranslator = $termTranslator;
	}

	public function translateFingerprint( Fingerprint $fingerprint ): SemanticEntity {
		$semanticEntity = new SemanticEntity();

		$this->addLabels( $semanticEntity, $fingerprint->getLabels() );
		$this->addDescriptions( $semanticEntity, $fingerprint->getDescriptions() );
		$this->addAliases( $semanticEntity, $fingerprint->getAliasGroups() );

		return $semanticEntity;
	}

	private function addLabels( SemanticEntity $semanticEntity, TermList $labels ): void {
		foreach ( $labels as $label ) {
			$semanticEntity->addPropertyValue(
				FixedProperties::LABEL,
				$this->translateTerm( $label )
			);
		}
	}

	private function translateTerm( Term $term ): SMWDataItem {
		return $this->termTranslator->translateTerm( $term );
	}

	private function addDescriptions( SemanticEntity $semanticEntity, TermList $descriptions ): void {
		foreach ( $descriptions as $description ) {
			$semanticEntity->addPropertyValue(
				FixedProperties::DESCRIPTION,
				$this->translateTerm( $description )
			);
		}
	}

	private function addAliases( SemanticEntity $semanticEntity, AliasGroupList $aliasGroups ): void {
		foreach ( $aliasGroups as $aliasGroup ) {
			foreach ( $aliasGroup->getAliases() as $aliasText ) {
				$semanticEntity->addPropertyValue(
					FixedProperties::ALIAS,
					$this->translateTerm( new Term( $aliasGroup->getLanguageCode(), $aliasText ) )
				);
			}
		}
	}

}
