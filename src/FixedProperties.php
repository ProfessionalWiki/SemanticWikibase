<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use SMW\DataValues\MonolingualTextValue;
use SMW\DataValues\StringValue;
use SMW\PropertyRegistry;

class FixedProperties {

	public const ID = '___WIKIBASE_ID';
	public const LABEL = '___WIKIBASE_LABEL';
	public const DESCRIPTION = '___WIKIBASE_DESCRIPTION';

	private const PROPERTIES = [
		self::ID => [
			'type' => StringValue::TYPE_ID,
			'label' => 'Wikibase ID'
		],
		self::LABEL => [
			'type' => MonolingualTextValue::TYPE_ID,
			'label' => 'Wikibase label'
		],
		self::DESCRIPTION => [
			'type' => MonolingualTextValue::TYPE_ID,
			'label' => 'Wikibase description'
		]
	];

	public function register( PropertyRegistry $propertyRegistry ): void {
		foreach ( self::PROPERTIES as $id => $definition ) {
			$propertyRegistry->registerProperty(
				$id,
				$definition['type'],
				$definition['label'],
				true,
				false
			);
		}
	}

	public function registerFixedTables( array &$customFixedProperties, array &$fixedPropertyTablePrefix ): void {
		foreach ( array_keys( self::PROPERTIES ) as $id ) {
			$customFixedProperties[$id] = str_replace( '___', '_', $id );
			$fixedPropertyTablePrefix[$id] = 'swb_fpt';
		}
	}

}
