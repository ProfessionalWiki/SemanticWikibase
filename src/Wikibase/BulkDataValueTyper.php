<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Wikibase;

use DataValues\DataValue;

class BulkDataValueTyper implements DataValueTyper {

	/**
	 * @param DataValue[] $dataValues
	 * @return TypedDataValue[]
	 */
	public function addTypesToValues( array $dataValues ): array {
		return [];
	}

}
