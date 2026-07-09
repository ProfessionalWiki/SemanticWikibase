<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\EntryPoints;

use MediaWiki\Revision\RenderedRevision;
use MediaWiki\User\UserIdentity;
use CommentStoreComment;
use Status;
use MediaWiki\MediaWikiServices;
use MediaWiki\TitleFactory;
use MediaWiki\Extension\SemanticWikibase\SemanticWikibase;
use SMW\Services\ServicesFactory as ApplicationFactory;
use SMW\PropertyRegistry;
use SMW\SemanticData;
use SMW\DIWikiPage;
use SMW\StoreFactory;
use SMW\Store;

use Parser;

class HookHandlers {

	public static function onExtensionRegistration(): void {
		global $smwgNamespacesWithSemanticLinks;
		$smwgNamespacesWithSemanticLinks[WB_NS_ITEM] = true;
		$smwgNamespacesWithSemanticLinks[WB_NS_PROPERTY] = true;
	}

	public static function onParserFirstCallInit( Parser $parser ) {
		# getInstance() will call initProperties() which aktivates existing hook onSmwInitProperties()
		wfDebug( __METHOD__ . "SWB: onParserFirstCallInit" );
		PropertyRegistry::getInstance();
	}

	public static function onSmwInitProperties( PropertyRegistry $propertyRegistry ): void {
		wfDebug( __METHOD__ . "SWB: onSmwInitProperties..." );
		SemanticWikibase::getGlobalInstance()->registerProperties( $propertyRegistry );	
	}

	public static function onSmwAddCustomFixedPropertyTables( array &$customFixedProperties, array &$fixedPropertyTablePrefix ): void {
		wfDebug( __METHOD__ . "SWB: onSmwAddCustomFixedPropertyTables" );
		SemanticWikibase::getGlobalInstance()->getFixedProperties()
			->registerFixedTables( $customFixedProperties, $fixedPropertyTablePrefix );
	}

	public static function onSmwUpdateDataBefore( Store $store, SemanticData $semanticData ): void {
		wfDebug( __METHOD__ . "SWB: onSmwUpdateDataBefore" );
		SemanticWikibase::getGlobalInstance()->getSemanticDataUpdate()
			->run( $semanticData );

	}

	public static function onPageSaveComplete( WikiPage $wikiPage, MediaWiki\User\UserIdentity $user, string $summary, int $flags, MediaWiki\Revision\RevisionRecord $revisionRecord, MediaWiki\Storage\EditResult $editResult ) {
        wfDebug( __METHOD__ . "SWB: onPageSaveComplete" );
		// Access the semantic data of the page via the Semantic MediaWiki API
        $semanticData = \SMW\MediaWiki\Hooks\ParserHooks::getSemanticDataForPage($wikiPage);

        onSmwUpdateDataBefore(null, $semanticData);
    }

	public static function onMultiContentSave( RenderedRevision $renderedRevision, UserIdentity $user, CommentStoreComment $summary, $flags, Status $hookStatus ) {
		wfDebug( __METHOD__ . "SWB: onMultiContentSave" );
        $revision = $renderedRevision->getRevision();
		
		$titleFactory = MediaWikiServices::getInstance()->getTitleFactory();
        $title = $titleFactory->newFromLinkTarget($revision->getPageAsLinkTarget());
		wfDebug( __METHOD__ . "SWB: onMultiContentSave...title:".$title );
		$subject = DIWikiPage::newFromTitle( $title );
        #$new_content = $revision->getContent(SlotRecord::MAIN, RevisionRecord::RAW)->getNativeData();
		$semanticData = StoreFactory::getStore()->getSemanticData( DIWikiPage::newFromTitle( $title ) );
		wfDebug( __METHOD__ . "SWB: onMultiContentSave: ".json_encode($semanticData) );
        SemanticWikibase::getGlobalInstance()->getSemanticDataUpdate()->run( $semanticData );

        return true;
    } 

}
