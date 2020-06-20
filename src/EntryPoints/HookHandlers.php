<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\EntryPoints;

use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
use SMW\DIProperty;
use SMW\PropertyRegistry;
use SMW\SemanticData;
use SMW\Store;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\Repo\WikibaseRepo;

class HookHandlers {

	public static function onSmwInitProperties( PropertyRegistry $propertyRegistry ): void {
		SemanticWikibase::getGlobalInstance()->getFixedProperties()
			->register( $propertyRegistry );
	}

	public static function onSmwAddCustomFixedPropertyTables( array &$customFixedProperties, array &$fixedPropertyTablePrefix ): void {
		SemanticWikibase::getGlobalInstance()->getFixedProperties()
			->registerFixedTables( $customFixedProperties, $fixedPropertyTablePrefix );
	}

	public static function onSmwUpdateDataBefore( Store $store, SemanticData $semanticData ): void {
		$subject = $semanticData->getSubject();

		if ( $subject === null || $subject->getTitle() === null || $subject->getTitle()->isSpecialPage() ) {
			return;
		}

		if ( $subject->getTitle()->getNamespace() === WB_NS_ITEM ) {
			$item = WikibaseRepo::getDefaultInstance()->getItemLookup()->getItemForId( new ItemId( $subject->getTitle()->getText() ) );

			$semanticData->addPropertyObjectValue(
				new DIProperty( '___WIKIBASE_ID' ),
				new \SMWDIBlob( $item->getId()->getSerialization() )
			);

			$semanticData->addPropertyObjectValue(
				new DIProperty( '___WIKIBASE_LABEL' ),
				new \SMWDIBlob( $item->getLabels()->getByLanguage( 'en' )->getText() )
			);
		}
	}

}
