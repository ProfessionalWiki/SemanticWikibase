<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use ReflectionClass;
use SMW\PropertyRegistry;

class FixedProperties {

	public const ID = '___WIKIBASE_ID';
	public const LABEL = '___WIKIBASE_LABEL';

	private const PROPERTIES = [
		self::ID => [
			'type' => '_txt',
			'label' => 'Wikibase ID'
		],
		self::LABEL => [
			'type' => '_txt',
			'label' => 'Wikibase label'
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
