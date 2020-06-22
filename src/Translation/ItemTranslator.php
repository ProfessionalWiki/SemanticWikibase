<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\PropertyValuePair;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

class ItemTranslator {

	public function __construct() {
	}

	/**
	 * @param Item $item
	 *
	 * @return PropertyValuePair[]
	 */
	public function itemToSmwValues( Item $item ): array {
		$itemId = $item->getId();

		if ( $itemId === null ) {
			return [];
		}

		$propertyValues = [];

		$propertyValues[] = new PropertyValuePair(
			new DIProperty( FixedProperties::ID ),
			new \SMWDIBlob( $itemId->getSerialization() )
		);

		$termTranslator = new TermTranslator(
			DataValueFactory::getInstance(),
			DIWikiPage::newFromText( $item->getId()->getSerialization(), WB_NS_ITEM )
		);

		foreach ( $item->getLabels() as $label ) {
			$propertyValues[] = $termTranslator->translateLabel( $label );
		}

		foreach ( $item->getDescriptions() as $description ) {
			$propertyValues[] = $termTranslator->translateDescription( $description );
		}

		foreach ( $item->getStatements()->getBestStatements()->getByRank( [ Statement::RANK_PREFERRED, Statement::RANK_NORMAL ] )->getMainSnaks() as $snak ) {
			if ( $snak instanceof PropertyValueSnak ) {
				$value = $snak->getDataValue();

				if ( $value instanceof StringValue ) {
					$propertyValues[] = new PropertyValuePair(
						new DIProperty( '___SWB_' . $snak->getPropertyId()->getSerialization() ),
						new \SMWDIBlob( $value->getValue() )
					);
				}

			}
		}

		return $propertyValues;
	}

}
