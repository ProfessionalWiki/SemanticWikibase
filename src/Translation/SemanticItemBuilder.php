<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\PropertyValuePair;
use SMW\DataValueFactory;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Wikibase\DataModel\Entity\Item;

class SemanticItemBuilder {

	private DIWikiPage $subject;

	public function __construct( DIWikiPage $subject ) {
		$this->subject = $subject;
	}

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

		$termTranslator = new TermTranslator( DataValueFactory::getInstance(), $this->subject );

		foreach ( $item->getLabels() as $label ) {
			$propertyValues[] = $termTranslator->translateLabel( $label );
		}

		foreach ( $item->getDescriptions() as $description ) {
			$propertyValues[] = $termTranslator->translateDescription( $description );
		}

		return $propertyValues;
	}

}
