<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use SMW\PropertyRegistry;
use SMW\SemanticData;
use SMW\Store;
use User;
use Wikibase\ItemContent;

class HookHandlers {

	public static function onPageContentSaveComplete( \WikiPage &$wikiPage, User &$user, \Content $content ) {
		if ( $content instanceof ItemContent ) {

		}
	}

	public static function onSmwInitProperties( PropertyRegistry $propertyRegistry ) {
		$propertyRegistry->registerProperty(
			'___WIKIBASE_ID',
			'_txt',
			'Wikibase ID',
			true,
			false
		);
	}

	public static function onSmwAddCustomFixedPropertyTables( array &$customFixedProperties, &$fixedPropertyTablePrefix ) {
		$customFixedProperties['___WIKIBASE_ID'] = '_WIKIBASE_ID';
		$fixedPropertyTablePrefix['___WIKIBASE_ID'] = 'swb_fpt';
	}

	public static function onSmwUpdateDataBefore( Store $store, SemanticData $semanticData ) {
		$subject = $semanticData->getSubject();

		if ( $subject === null || $subject->getTitle() === null || $subject->getTitle()->isSpecialPage() ) {
			return;
		}

		if ( $subject->getTitle()->getNamespace() === WB_NS_PROPERTY ) {
			
		}
	}

}
