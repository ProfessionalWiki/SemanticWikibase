<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use SMW\PropertyRegistry;

class FixedProperties {

	public function register( PropertyRegistry $propertyRegistry ): void {
		$propertyRegistry->registerProperty(
			'___WIKIBASE_ID',
			'_txt',
			'Wikibase ID',
			true,
			false
		);

		$propertyRegistry->registerProperty(
			'___WIKIBASE_LABEL',
			'_txt',
			'Wikibase label',
			true,
			false
		);
	}

	public function registerFixedTables( array &$customFixedProperties, array &$fixedPropertyTablePrefix ): void {
		$customFixedProperties['___WIKIBASE_ID'] = '_WIKIBASE_ID';
		$fixedPropertyTablePrefix['___WIKIBASE_ID'] = 'swb_fpt';

		$customFixedProperties['___WIKIBASE_LABEL'] = '_WIKIBASE_LABEL';
		$fixedPropertyTablePrefix['___WIKIBASE_LABEL'] = 'swb_fpt';
	}

}
