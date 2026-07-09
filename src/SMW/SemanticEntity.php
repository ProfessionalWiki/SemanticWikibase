<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\SMW;

use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\SemanticData;
use SMWDataItem;
use SMW\Subobject;

class SemanticEntity {

	private array $dataItemsPerProperty = [];
	private array $subObjectsPerProperty=[];

	public function addPropertyValue( string $NumericPropertyId, SMWDataItem $dataItem ) {
		$this->dataItemsPerProperty[$NumericPropertyId][] = $dataItem;
	}

	public function addSubobject( string $NumericPropertyId, SubObject $subobject ){
		$this->subObjectsPerProperty[$NumericPropertyId][] = $subobject;
	}

	/**
	 * @param string $NumericPropertyId
	 * @return SMWDataItem[]
	 */
	public function getDataItemsForProperty( string $NumericPropertyId ): array {
		return $this->dataItemsPerProperty[$NumericPropertyId] ?? [];
	}

	public function toSemanticData( DIWikiPage $subject ): SemanticData {
		$semanticData = new SemanticData( $subject );

		foreach ( $this->dataItemsPerProperty as $NumericPropertyId => $dataItems ) {
			$property = new DIProperty( $NumericPropertyId );

			foreach ( $dataItems as $dataItem ) {
				$semanticData->addPropertyObjectValue(
					$property,
					$dataItem
				);
			}
		}

		foreach ( $this->subObjectsPerProperty as $NumericPropertyId => $subobjects ) {
			$property = new DIProperty( $NumericPropertyId );

			foreach ( $subobjects as $subobject ) {
				$semanticData->addSubobject($subobject);
			}
		}

		return $semanticData;
	}

	public function functionalMerge( self $entity ): self {
		$merged = new SemanticEntity();

		$merged->add( $this );
		$merged->add( $entity );

		return $merged;
	}

	public function add( self $entity ): void {
		foreach ( $entity->dataItemsPerProperty as $NumericPropertyId => $dataItems ) {
			foreach ( $dataItems as $dataItem ) {
				$this->addPropertyValue( $NumericPropertyId, $dataItem );
			}
		}
	}

}
