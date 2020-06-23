<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\TranslationModel;

use SMW\DIProperty;
use SMWDataItem;

class PropertyValuePair {

	private DIProperty $property;
	private SMWDataItem $value;

	public function __construct( DIProperty $property, SMWDataItem $value ) {
		$this->property = $property;
		$this->value = $value;
	}

	public function getProperty(): DIProperty {
		return $this->property;
	}

	public function getValue(): SMWDataItem {
		return $this->value;
	}

}
