<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\FixedProperties;
use MediaWiki\Extension\SemanticWikibase\PropertyValuePair;
use SMW\DataValueFactory;
use SMW\DataValues\MonolingualTextValue;
use SMW\DIProperty;
use SMW\DIWikiPage;
use Wikibase\DataModel\Term\Term;

class TermTranslator {

	private DataValueFactory $dataValueFactory;
	private DIWikiPage $subject;

	public function __construct( DataValueFactory $dataValueFactory, DIWikiPage $subject ) {
		$this->dataValueFactory = $dataValueFactory;
		$this->subject = $subject;
	}

	public function translateLabel( Term $label ) {
		return $this->translateTerm( $label, FixedProperties::LABEL );
	}

	public function translateDescription( Term $label ) {
		return $this->translateTerm( $label, FixedProperties::DESCRIPTION );
	}

	private function translateTerm( Term $term, string $propertyId ) {
		$dv = $this->dataValueFactory->newDataValueByType(
			MonolingualTextValue::TYPE_ID,
			$term->getText() . '@' . $term->getLanguageCode(),
			false,
			new DIProperty( $propertyId ),
			$this->subject
		);

		return new PropertyValuePair(
			$dv->getProperty(),
			$dv->getDataItem()
		);
	}

}
