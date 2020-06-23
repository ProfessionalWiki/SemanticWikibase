<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\TranslationModel;

use SMW\DIProperty;
use SMWDataItem;

class SemanticEntity {

	private array $propertyValuePairs = [];
	private array $dataItemsPerProperty = [];

	public function addPropertyValue( string $propertyId, SMWDataItem $dataItem ) {
		$this->propertyValuePairs[] = new PropertyValuePair(
			new DIProperty( $propertyId ),
			$dataItem
		);

		$this->dataItemsPerProperty[$propertyId][] = $dataItem;
	}

	/**
	 * @return PropertyValuePair[]
	 */
	public function getPropertyValuePairs() {
		return $this->propertyValuePairs;
	}

	/**
	 * @param string $propertyId
	 * @return SMWDataItem[]
	 */
	public function getDataItemsForProperty( string $propertyId ): array {
		return $this->dataItemsPerProperty[$propertyId] ?? [];
	}

}
