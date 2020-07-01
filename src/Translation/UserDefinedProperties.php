<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticProperty;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Services\Lookup\TermLookup;
use Wikibase\Lib\Store\PropertyInfoLookup;
use Wikibase\Lib\Store\StorageException;
use Wikibase\Repo\WikibaseRepo;
use Wikimedia\Rdbms\DBError;

class UserDefinedProperties {

	private PropertyInfoLookup $propertyInfoLookup;
	private PropertyTypeTranslator $propertyTypeTranslator;
	private TermLookup $termLookup;

	public function __construct( PropertyInfoLookup $propertyInfoLookup, PropertyTypeTranslator $propertyTypeTranslator, TermLookup $termLookup ) {
		$this->propertyInfoLookup = $propertyInfoLookup;
		$this->propertyTypeTranslator = $propertyTypeTranslator;
		$this->termLookup = $termLookup;
	}

	/**
	 * @return SemanticProperty[]
	 */
	public function getAll(): array {
		$properties = [];

		foreach ( $this->getAllPropertyInfo() as $id => $propertyInfo ) {
			if ( $this->propertyTypeTranslator->canTranslate( $propertyInfo['type'] ) ) {
				$properties[] = new SemanticProperty(
					self::idFromWikibaseString( $id ),
					$this->propertyTypeTranslator->translate( $propertyInfo['type'] ),
					$id,
					$this->termLookup->getLabel(
						new PropertyId( $id ),
						'en'
					)
				);
			}
		}

		return $properties;
	}

	public function getAllPropertyInfo(): array {
		try {
			return $this->propertyInfoLookup->getAllPropertyInfo();
		}
		catch ( StorageException | DBError $ex ) {
			return [];
		}
	}

	private static function idFromWikibaseString( string $propertyId ): string {
		return  '___SWB_' . $propertyId;
	}

	public static function idFromWikibaseProperty( PropertyId $id ): string {
		return self::idFromWikibaseString( $id->getSerialization() );
	}

}
