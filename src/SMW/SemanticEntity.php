<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\SMW;

use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SemanticData;
use SMWDataItem;

class SemanticEntity {

	private array $dataItemsPerProperty = [];

	public function addPropertyValue( string $propertyId, SMWDataItem $dataItem ) {
		$this->dataItemsPerProperty[$propertyId][] = $dataItem;
	}

	/**
	 * @param string $propertyId
	 * @return SMWDataItem[]
	 */
	public function getDataItemsForProperty( string $propertyId ): array {
		return $this->dataItemsPerProperty[$propertyId] ?? [];
	}

	public function toSemanticData( DIWikiPage $subject ): SemanticData {
		$semanticData = new SemanticData( $subject );

		foreach ( $this->dataItemsPerProperty as $propertyId => $dataItems ) {
			$property = new DIProperty( $propertyId );

			foreach ( $dataItems as $dataItem ) {
				$semanticData->addPropertyObjectValue(
					$property,
					$dataItem
				);
			}
		}

		return $semanticData;
	}

}
