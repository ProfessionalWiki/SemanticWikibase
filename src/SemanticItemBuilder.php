<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use SMW\DIProperty;
use Wikibase\DataModel\Entity\Item;

class SemanticItemBuilder {

	/**
	 * @param Item $item
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

		$propertyValues[] = new PropertyValuePair(
			new DIProperty( FixedProperties::LABEL ),
			new \SMWDIBlob( $item->getLabels()->getByLanguage( 'en' )->getText() )
		);

		return $propertyValues;
	}

}
