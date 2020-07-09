<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use DataValues\MonolingualTextValue;
use SMW\DIWikiPage;
use Wikibase\DataModel\Term\Term;

class TermTranslator {

	private MonoTextTranslator $monoTextTranslator;
	private DIWikiPage $subject;

	public function __construct( MonoTextTranslator $monoTextTranslator, DIWikiPage $subject ) {
		$this->monoTextTranslator = $monoTextTranslator;
		$this->subject = $subject;
	}

	public function translateTerm( Term $term ) {
		return $this->monoTextTranslator->valueToDataItem(
			new MonolingualTextValue( $term->getLanguageCode(), $term->getText() ),
			$this->subject
		);
	}

}
