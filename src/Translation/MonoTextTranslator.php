<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use DataValues\MonolingualTextValue;
use SMW\DataValueFactory;
use SMW\DIWikiPage;
use SMWDIContainer;

class MonoTextTranslator {

	private DataValueFactory $dataValueFactory;

	public function __construct( DataValueFactory $dataValueFactory ) {
		$this->dataValueFactory = $dataValueFactory;
	}

	public function valueToDataItem( MonolingualTextValue $textValue, DIWikiPage $subject ): SMWDIContainer {
		$dataItem = $this->dataValueFactory->newDataValueByType(
			\SMW\DataValues\MonolingualTextValue::TYPE_ID,
			$textValue->getText() . '@' . $textValue->getLanguageCode(),
			false,
			null,
			$subject
		)->getDataItem();

		if ( $dataItem instanceof SMWDIContainer ) {
			return $dataItem;
		}

		throw new \RuntimeException( 'dataValueFactory did not return a DV with SMWDIContainer' );
	}

}
