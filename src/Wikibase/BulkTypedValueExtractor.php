<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Wikibase;

use Wikibase\DataModel\Snak\PropertyValueSnak;

class BulkTypedValueExtractor implements TypedValueExtractor {

	/**
	 * @param PropertyValueSnak[] $snaks
	 * @return TypedDataValue[]
	 */
	public function snaksToTypedValues( array $snaks ): array {
		return [];
	}

}
