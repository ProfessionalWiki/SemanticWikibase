<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

use MediaWiki\Revision\RevisionRecord;
use SMW\DIProperty;
use SMW\PropertyRegistry;
use SMW\SemanticData;
use SMW\Store;
use User;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\Repo\WikibaseRepo;

class HookHandlers {

	public static function onPageSaveComplete( \WikiPage &$wikiPage, User &$user, string $summary, int $flags, RevisionRecord $revision ) {
//		$content = $revision->getContent( SlotRecord::MAIN, RevisionRecord::RAW );
//
//		if ( $content instanceof ItemContent ) {
//
//			$smwFactory = ApplicationFactory::getInstance();
//
//			$parserData = $smwFactory->newParserData(
//				$wikiPage->getTitle(),
//				$wikiPage->getParserOutput( \ParserOptions::newCanonical( $user ) )
//			);
//
//			$parserData->getSemanticData()->addPropertyObjectValue(
//				new DIProperty( '___WIKIBASE_ID' ),
//				new \SMWDIBlob( 'fluff' )
//			);
//		}
	}

	public static function onSmwInitProperties( PropertyRegistry $propertyRegistry ) {
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

	public static function onSmwAddCustomFixedPropertyTables( array &$customFixedProperties, &$fixedPropertyTablePrefix ) {
		$customFixedProperties['___WIKIBASE_ID'] = '_WIKIBASE_ID';
		$fixedPropertyTablePrefix['___WIKIBASE_ID'] = 'swb_fpt';

		$customFixedProperties['___WIKIBASE_LABEL'] = '_WIKIBASE_LABEL';
		$fixedPropertyTablePrefix['___WIKIBASE_LABEL'] = 'swb_fpt';
	}

	public static function onSmwUpdateDataBefore( Store $store, SemanticData $semanticData ) {
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
