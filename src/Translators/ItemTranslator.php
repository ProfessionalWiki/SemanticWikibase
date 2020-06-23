<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\PropertyValuePair;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\SemanticEntity;
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
	 * @return SemanticEntity
	 */
	public function itemToSmwValues( Item $item ): SemanticEntity {
		$itemId = $item->getId();

		if ( $itemId === null ) {
			return new SemanticEntity( [] );
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

		return new SemanticEntity( $propertyValues );
	}

}
