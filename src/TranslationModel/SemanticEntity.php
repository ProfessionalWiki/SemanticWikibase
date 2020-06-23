<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\TranslationModel;

class SemanticEntity {

	private array $propertyValuePairs;

	/**
	 * @param PropertyValuePair[] $propertyValuePairs
	 */
	public function __construct( array $propertyValuePairs ) {
		$this->propertyValuePairs = $propertyValuePairs;
	}

	/**
	 * @return PropertyValuePair[]
	 */
	public function getPropertyValuePairs() {
		return $this->propertyValuePairs;
	}

}
