<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\TranslationModel;

use SMW\DIProperty;
use SMWDataItem;

class SemanticEntity {

	private array $propertyValuePairs = [];

	public function __construct() {
	}

	public function addPropertyValue( string $propertyId, SMWDataItem $dataItem ) {
		$this->propertyValuePairs[] = new PropertyValuePair(
			new DIProperty( $propertyId ),
			$dataItem
		);
	}

	/**
	 * @return PropertyValuePair[]
	 */
	public function getPropertyValuePairs() {
		return $this->propertyValuePairs;
	}

}
