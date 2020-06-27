<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use Maps\SemanticMW\CoordinateValue;
use SMW\DataValues\MonolingualTextValue;
use SMW\DataValues\StringValue;
use SMWTimeValue;

class PropertyTypeTranslator {

	public function canTranslate( string $wikibasePropertyType ): bool {
		return array_key_exists( $wikibasePropertyType, $this->getMap() )
			&& is_string( $this->getMap()[$wikibasePropertyType] );
	}

	/**
	 * Wikibase property type => SMW DataValue type
	 *
	 * @param string $wikibasePropertyType
	 *
	 * @return string SMW DataValue type ID
	 */
	public function translate( string $wikibasePropertyType ): string {
		return $this->getMap()[$wikibasePropertyType];
	}

	private function getMap(): array {
		$map = [
			'commonsMedia' => StringValue::TYPE_ID,
			'geo-shape' => StringValue::TYPE_ID,
			'monolingualtext' => MonolingualTextValue::TYPE_ID,
			'quantity' => 'subobject', // TODO // Could also use SMWQuantityValue
			'string' => StringValue::TYPE_ID,
			'tabular-data' => null, // TODO
			'entity-schema' => null, // TODO
			'time' => SMWTimeValue::TYPE_ID,
			'url' => null, // TODO
			'external-id' => null, // TODO
			'wikibase-item' => '_wpg',
			'wikibase-property' => '_wpg',
		];

		if ( class_exists( CoordinateValue::class ) ) {
			$map['globe-coordinate'] = '_geo';
		}

		return $map;
	}

}
