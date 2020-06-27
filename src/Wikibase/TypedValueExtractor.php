<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Wikibase;

use Wikibase\DataModel\Snak\PropertyValueSnak;

interface TypedValueExtractor {

	/**
	 * @param PropertyValueSnak[] $snaks
	 * @return TypedDataValue[]
	 */
	public function snaksToTypedValues( array $snaks ): array;

}
