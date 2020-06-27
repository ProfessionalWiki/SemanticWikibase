<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Wikibase;

use DataValues\DataValue;

interface DataValueTyper {

	/**
	 * @param DataValue[] $dataValues
	 * @return TypedDataValue[]
	 */
	public function addTypesToValues( array $dataValues ): array;

}
