<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\TranslationModel;

use SMW\DataValues\MonolingualTextValue;
use SMW\DataValues\StringValue;

class FixedProperties {

	public const ID = '___WIKIBASE_ID';
	public const LABEL = '___WIKIBASE_LABEL';
	public const DESCRIPTION = '___WIKIBASE_DESCRIPTION';

	public static function newEntityId(): SemanticProperty {
		return new SemanticProperty( self::ID, StringValue::TYPE_ID, 'Wikibase ID' );
	}

	public static function newLabel(): SemanticProperty {
		return new SemanticProperty( self::LABEL, MonolingualTextValue::TYPE_ID, 'Wikibase label' );
	}

	public static function newDescription(): SemanticProperty {
		return new SemanticProperty( self::DESCRIPTION, MonolingualTextValue::TYPE_ID, 'Wikibase description' );
	}

	public function registerFixedTables( array &$customFixedProperties, array &$fixedPropertyTablePrefix ): void {
		foreach ( $this->getAll() as $property ) {
			$customFixedProperties[$property->getId()] = str_replace( '___', '_', $property->getId() );
			$fixedPropertyTablePrefix[$property->getId()] = 'swb_fpt';
		}
	}

	/**
	 * @return SemanticProperty[]
	 */
	public function getAll(): array {
		return [
			self::newEntityId(),
			self::newLabel(),
			self::newDescription(),
		];
	}

}
