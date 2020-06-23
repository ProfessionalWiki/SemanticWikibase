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

		foreach ( $item->getStatements()->getBestStatements()->getByRank( [ Statement::RANK_PREFERRED, Statement::RANK_NORMAL ] )->getMainSnaks() as $snak ) {
			if ( $snak instanceof PropertyValueSnak ) {
				$value = $snak->getDataValue();

				if ( $value instanceof StringValue ) {
					$semanticEntity->addPropertyValue(
						UserDefinedProperties::idFromWikibaseProperty( $snak->getPropertyId() ),
						new \SMWDIBlob( $value->getValue() )
					);
				}
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
