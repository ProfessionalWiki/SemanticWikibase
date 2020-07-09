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

	private SemanticEntity $semanticEntity;
	private TermTranslator $termTranslator;

	public function __construct( SemanticEntity $semanticEntity, TermTranslator $termTranslator ) {
		$this->semanticEntity = $semanticEntity;
		$this->termTranslator = $termTranslator;
	}

	public function addFingerprintValues( Fingerprint $fingerprint ) {
		$this->addLabels( $fingerprint->getLabels() );
		$this->addDescriptions( $fingerprint->getDescriptions() );
		$this->addAliases( $fingerprint->getAliasGroups() );
	}

	private function addLabels( TermList $labels ): void {
		foreach ( $labels as $label ) {
			$this->semanticEntity->addPropertyValue(
				FixedProperties::LABEL,
				$this->translateTerm( $label )
			);
		}
	}

	private function translateTerm( Term $term ): SMWDataItem {
		return $this->termTranslator->translateTerm( $term );
	}

	private function addDescriptions( TermList $descriptions ): void {
		foreach ( $descriptions as $description ) {
			$this->semanticEntity->addPropertyValue(
				FixedProperties::DESCRIPTION,
				$this->translateTerm( $description )
			);
		}
	}

	private function addAliases( AliasGroupList $aliasGroups ): void {
		foreach ( $aliasGroups as $aliasGroup ) {
			foreach ( $aliasGroup->getAliases() as $aliasText ) {
				$this->semanticEntity->addPropertyValue(
					FixedProperties::ALIAS,
					$this->translateTerm( new Term( $aliasGroup->getLanguageCode(), $aliasText ) )
				);
			}
		}
	}

}
