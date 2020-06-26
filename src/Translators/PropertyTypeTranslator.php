<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use SMW\DataValues\MonolingualTextValue;
use SMW\DataValues\StringValue;
use SMWTimeValue;

class PropertyTypeTranslator {

	private const MAP = [
		'commonsMedia' => StringValue::TYPE_ID,
		'geo-shape' => StringValue::TYPE_ID,
		'globe-coordinate' => null, // TODO: maps only
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

	public function canTranslate( string $wikibasePropertyType ): bool {
		return array_key_exists( $wikibasePropertyType, self::MAP )
			&& is_string( self::MAP[$wikibasePropertyType] );
	}

	/**
	 * Wikibase property type => SMW DataValue type
	 *
	 * @param string $wikibasePropertyType
	 *
	 * @return string SMW DataValue type ID
	 */
	public function translate( string $wikibasePropertyType ): string {
		return self::MAP[$wikibasePropertyType];
	}

}
