<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\SMW\SemanticProperty;
use Wikibase\DataModel\Entity\NumericPropertyId;
use Wikibase\DataModel\Services\Lookup\TermLookup;
use Wikibase\Lib\Store\PropertyInfoLookup;
use Wikibase\Lib\Store\StorageException;
use Wikimedia\Rdbms\DBError;

class UserDefinedProperties {

	private PropertyInfoLookup $propertyInfoLookup;
	private PropertyTypeTranslator $propertyTypeTranslator;
	private TermLookup $termLookup;
	private string $labelLanguageCode;

	public function __construct( PropertyInfoLookup $propertyInfoLookup, PropertyTypeTranslator $propertyTypeTranslator,
		TermLookup $termLookup, string $labelLanguageCode ) {

		$this->propertyInfoLookup = $propertyInfoLookup;
		$this->propertyTypeTranslator = $propertyTypeTranslator;
		$this->termLookup = $termLookup;
		$this->labelLanguageCode = $labelLanguageCode;
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
						new NumericPropertyId( $id ),
						$this->labelLanguageCode
					)
				);
			}else{
				wfDebug("swb: cannot translate ".$propertyInfo['type']);
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

	private static function idFromWikibaseString( string $NumericPropertyId ): string {
		return  '___SWB_' . $NumericPropertyId;
	}

	public static function idFromWikibaseProperty( NumericPropertyId $id ): string {
		return self::idFromWikibaseString( $id->getSerialization() );
	}

}
