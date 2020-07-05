<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\EntryPoints;

use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
use SMW\PropertyRegistry;
use SMW\SemanticData;
use SMW\Store;

class HookHandlers {

	public static function onExtensionRegistration(): void {
		global $smwgNamespacesWithSemanticLinks;
		$smwgNamespacesWithSemanticLinks[WB_NS_ITEM] = true;
		$smwgNamespacesWithSemanticLinks[WB_NS_PROPERTY] = true;
	}

	public static function onSmwInitProperties( PropertyRegistry $propertyRegistry ): void {
		SemanticWikibase::getGlobalInstance()->registerProperties( $propertyRegistry );
	}

	public static function onSmwAddCustomFixedPropertyTables( array &$customFixedProperties, array &$fixedPropertyTablePrefix ): void {
		SemanticWikibase::getGlobalInstance()->getFixedProperties()
			->registerFixedTables( $customFixedProperties, $fixedPropertyTablePrefix );
	}

	public static function onSmwUpdateDataBefore( Store $store, SemanticData $semanticData ): void {
		SemanticWikibase::getGlobalInstance()->getSemanticDataUpdate()
			->run( $semanticData );
	}

}
