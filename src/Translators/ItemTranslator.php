<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translators;

use MediaWiki\Extension\SemanticWikibase\TranslationModel\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\SemanticEntity;
use MediaWiki\Extension\SemanticWikibase\TranslationModel\UserDefinedProperties;
use SMW\DataValueFactory;
use SMW\DataValues\MonolingualTextValue;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMWDataItem;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\DataModel\Statement\StatementList;
use Wikibase\DataModel\Term\Term;
use Wikibase\DataModel\Term\TermList;

class ItemTranslator {

	private SemanticEntity $semanticEntity;
	private DIWikiPage $subject;

	public function __construct() {
	}

	public function itemToSmwValues( Item $item ): SemanticEntity {
		if ( $item->getId() === null ) {
			return new SemanticEntity();
		}

		$this->semanticEntity = new SemanticEntity();
		$this->subject = DIWikiPage::newFromText( $item->getId()->getSerialization(), WB_NS_ITEM );

		$this->addId( $item->getId() );
		$this->addLabels( $item->getLabels() );
		$this->addDescriptions( $item->getDescriptions() );
		$this->addStatements( $item->getStatements()->getBestStatements()->getByRank( [ Statement::RANK_PREFERRED, Statement::RANK_NORMAL ] ) );

		return $this->semanticEntity;
	}

	private function addId( ItemId $itemId ) {
		$this->semanticEntity->addPropertyValue(
			FixedProperties::ID,
			new \SMWDIBlob( $itemId->getSerialization() )
		);
	}

	private function addLabels( TermList $labels ) {
		foreach ( $labels as $label ) {
			$this->semanticEntity->addPropertyValue(
				FixedProperties::LABEL,
				$this->translateTerm( $label, FixedProperties::LABEL )
			);
		}
	}

	private function translateTerm( Term $term, string $propertyId ): SMWDataItem {
		// TODO
		return DataValueFactory::getInstance()->newDataValueByType(
			MonolingualTextValue::TYPE_ID,
			$term->getText() . '@' . $term->getLanguageCode(),
			false,
			new DIProperty( $propertyId ),
			$this->subject
		)->getDataItem();
	}

	private function addDescriptions( TermList $descriptions ) {
		foreach ( $descriptions as $description ) {
			$this->semanticEntity->addPropertyValue(
				FixedProperties::DESCRIPTION,
				$this->translateTerm( $description, FixedProperties::DESCRIPTION )
			);
		}
	}

	private function addStatements( StatementList $statements ) {
		$dataValueTranslator = new DataValueTranslator( DataValueFactory::getInstance(), $this->subject );

		foreach ( $statements->getMainSnaks() as $snak ) {
			if ( $snak instanceof PropertyValueSnak ) {
				$value = $snak->getDataValue();

				$this->semanticEntity->addPropertyValue(
					UserDefinedProperties::idFromWikibaseProperty( $snak->getPropertyId() ),
					$dataValueTranslator->translate( $value )
				);
			}
		}
	}


}
