<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use DataValues\StringValue;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\UserDefinedProperties;
use SMW\DataValueFactory;
use SMW\DataValues\MonolingualTextValue;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDataItem;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Term\Term;

class ItemTranslator {

	private $subject;

	public function __construct() {
	}

	public function itemToSmwValues( Item $item ): SemanticEntity {
		$semanticEntity = new SemanticEntity();

		$itemId = $item->getId();

		if ( $itemId === null ) {
			return $semanticEntity;
		}

		// TODO
		$this->subject = DIWikiPage::newFromText( $item->getId()->getSerialization(), WB_NS_ITEM );

		$semanticEntity->addPropertyValue(
			FixedProperties::ID,
			new \SMWDIBlob( $itemId->getSerialization() )
		);

		foreach ( $item->getLabels() as $label ) {
			$semanticEntity->addPropertyValue(
				FixedProperties::LABEL,
				$this->translateTerm( $label, FixedProperties::newLabel()->getId() )
			);
		}

		foreach ( $item->getDescriptions() as $description ) {
			$semanticEntity->addPropertyValue(
				FixedProperties::DESCRIPTION,
				$this->translateTerm( $description, FixedProperties::newDescription()->getId() )
			);
		}

		$dataValueTranslator = new DataValueTranslator( DataValueFactory::getInstance() );

		// TODO
		foreach ( $item->getStatements()->getBestStatements()->getByRank( [ Statement::RANK_PREFERRED, Statement::RANK_NORMAL ] )->getMainSnaks() as $snak ) {
			if ( $snak instanceof PropertyValueSnak ) {
				$value = $snak->getDataValue();

				$semanticEntity->addPropertyValue(
					UserDefinedProperties::idFromWikibaseProperty( $snak->getPropertyId() ),
					$dataValueTranslator->translate( $value )
				);
			}
		}

		return $semanticEntity;
	}

	private function translateTerm( Term $term, string $propertyId ): SMWDataItem {
		return DataValueFactory::getInstance()->newDataValueByType(
			MonolingualTextValue::TYPE_ID,
			$term->getText() . '@' . $term->getLanguageCode(),
			false,
			new DIProperty( $propertyId ),
			$this->subject
		)->getDataItem();
	}

}
