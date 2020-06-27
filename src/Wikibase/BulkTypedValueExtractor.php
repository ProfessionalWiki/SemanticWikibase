<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Wikibase;

use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\Lib\Store\PropertyInfoLookup;

class BulkTypedValueExtractor implements TypedValueExtractor {

	private PropertyInfoLookup $propertyInfoLookup;

	public function __construct( PropertyInfoLookup $propertyInfoLookup ) {
		$this->propertyInfoLookup = $propertyInfoLookup;
	}

	/**
	 * @param PropertyValueSnak[] $snaks
	 * @return TypedDataValue[]
	 */
	public function snaksToTypedValues( array $snaks ): array {
		$idToTypeMap = $this->buildPropertyToTypeMap();

		return array_map(
			function( PropertyValueSnak $snak ) use ( $idToTypeMap ): TypedDataValue {
				return new TypedDataValue(
					$idToTypeMap[$snak->getPropertyId()->getSerialization()],
					$snak->getDataValue()
				);
			},
			$snaks
		);
	}

	private function buildPropertyToTypeMap(): array {
		$map = [];

		foreach ( $this->propertyInfoLookup->getAllPropertyInfo() as $id => $info ) {
			$map[$id] = $info[PropertyInfoLookup::KEY_DATA_TYPE];
		}

		return $map;
	}

}
