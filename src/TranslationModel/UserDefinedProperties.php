<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\TranslationModel;

use SMW\DataValues\StringValue;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\Lib\Store\PropertyInfoLookup;

class UserDefinedProperties {

	private PropertyInfoLookup $propertyInfoLookup;

	public function __construct( PropertyInfoLookup $propertyInfoLookup ) {
		$this->propertyInfoLookup = $propertyInfoLookup;
	}

	/**
	 * @return SemanticProperty[]
	 */
	public function getAll(): array {
		$properties = [];

		foreach ( $this->propertyInfoLookup->getAllPropertyInfo() as $id => $propertyInfo ) {
			$properties[] = new SemanticProperty(
				self::idFromWikibaseString( $id ),
				StringValue::TYPE_ID,
				$id
			);
		}

		return $properties;
	}

	private static function idFromWikibaseString( string $propertyId ): string {
		return  '___SWB_' . $propertyId;
	}

	public static function idFromWikibaseProperty( PropertyId $id ): string {
		return self::idFromWikibaseString( $id->getSerialization() );
	}

}
