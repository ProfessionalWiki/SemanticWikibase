<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Wikibase;

use DataValues\DataValue;

class TypedDataValue {

	private DataValue $value;
	private string $propertyType;

	public function __construct( string $propertyType, DataValue $value ) {
		$this->value = $value;
		$this->propertyType = $propertyType;
	}

	public function getValue(): DataValue {
		return $this->value;
	}

	public function getPropertyType(): string {
		return $this->propertyType;
	}

}
