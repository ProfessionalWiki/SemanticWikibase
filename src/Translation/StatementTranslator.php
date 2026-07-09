<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use MediaWiki\Extension\SemanticWikibase\Wikibase\TypedDataValue;
use Wikibase\DataModel\Services\Lookup\PropertyDataTypeLookup;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use SMW\DIWikiPage;
use SMWDataItem;
use SMW\Subobject;
use SMW\DataValueFactory;
use SMW\DIProperty;
use MediaWiki\MediaWikiServices;
use SMWDITime;

class StatementTranslator {

	private DataValueTranslator $dataValueTranslator;
	private ContainerValueTranslator $containerValueTranslator;
	private PropertyDataTypeLookup $propertyTypeLookup;

	public function __construct( DataValueTranslator $dataValueTranslator, ContainerValueTranslator $containerValueTranslator, PropertyDataTypeLookup $propertyTypeLookup ) {
		$this->propertyTypeLookup = $propertyTypeLookup;
		$this->containerValueTranslator = $containerValueTranslator;
		$this->dataValueTranslator = $dataValueTranslator;
	}

	public function statementToDataItem( Statement $statement, DIWikiPage $subject ): ?SMWDataItem {
		$mainSnak = $statement->getMainSnak();
		return $this->snakToDataItem($mainSnak, $statement, $subject);
	}

	public function statementToQualifiersDataItemList( Statement $statement, DIWikiPage $subject ): array {
		$qualifiers = $statement->getQualifiers();
		$result = [];
		foreach( $qualifiers as $actQualifier) {
            $result[] = $this->snakToDataItem($actQualifier, $statement, $subject);
		} 

		return $result;
	}

	public function snakToDataItem( PropertyValueSnak $snak, Statement $statement, DIWikiPage $subject ): ?SMWDataItem {
		if ( !( $snak instanceof PropertyValueSnak ) ) {
			return null;
		}

		if ( $this->containerValueTranslator->supportsStatement( $statement ) ) {
			return $this->containerValueTranslator->statementToDataItem( $statement, $subject );
		}

		return $this->snakWithSimpleDataValueToDataItem( $snak );
	}

	private function snakWithSimpleDataValueToDataItem( PropertyValueSnak $snak ): SMWDataItem {
		return $this->dataValueTranslator->translate(
			new TypedDataValue(
				$this->propertyTypeLookup->getDataTypeIdForProperty( $snak->getPropertyId() ),
				$snak->getDataValue()
			)
		);
	}

	public function statementToSubobjects( Statement $statement, DIWikiPage $subject , $statementNr): array {
		$result = [];
		$wbPropertyNamespaceName = MediaWikiServices::getInstance()->getNamespaceInfo()->getCanonicalName( WB_NS_PROPERTY );
		$smwPropertyNamespaceName = MediaWikiServices::getInstance()->getNamespaceInfo()->getCanonicalName( SMW_NS_PROPERTY );

		$mainSnak = $statement->getMainSnak();
		$qualifiers = $statement->getQualifiers();
		$mainSnakDI = $this->snakToDataItem($mainSnak, $statement, $subject);
		// create a smw semantic subobject for the statement
		$statementProperty = $mainSnak->getPropertyId()->getLocalPart();
		$statementValue = StatementTranslator::valueDIToString($mainSnakDI);
		
		$statementObj = new Subobject($subject->getTitle());
		$statementObj->setEmptyContainerForId("statement".$statementNr);
		$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('subObjectType', 'statement', false, $subject) );
		//$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('item', $subject->getTitle(), false, $subject) );
		$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('property_name', $statementProperty, false, $subject) );
		$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('property_wb', $wbPropertyNamespaceName.":".$statementProperty, false, $subject) );
		$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('property_smw', $smwPropertyNamespaceName.":".$statementProperty, false, $subject) );
		$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('value', $statementValue, false, $subject) );
		$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText($statementProperty, $statementValue, false, $subject) );
		#$prop =  DIProperty::newFromUserLabel($mainSnak->getPropertyId()->getLocalPart());
		$prop =  new DIProperty( 'semanticProperty:value');
		wfDebug('swb: prop: '.$prop);
		$propertyDV = DataValueFactory::getInstance()->newPropertyValueByLabel("value");
		$propertyDI = $propertyDV->getDataItem();
		#$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByItem($mainSnakDI, $propertyDI));
		$result[] = $statementObj;
		$qNr = 1;
		$statementPage = $subject->getTitle()."#statement".$statementNr;
		foreach( $qualifiers as $actQualifier){
			$qualifierProperty = $actQualifier->getPropertyId()->getLocalPart();
			$qualifierId = "statement".$statementNr."-qualifier".$qNr;
			$qualifierObj = new Subobject($subject->getTitle());
			$qualifierObj->setEmptyContainerForId($qualifierId);
			$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('subObjectType', 'qualifier', false, $subject) );
			//$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('item', $subject->getTitle(), false, $subject) );
			$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('statement', $statementPage, false, $subject) );
			$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('property_name', $qualifierProperty, false, $subject) );
			$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('property_wb', $wbPropertyNamespaceName.":".$qualifierProperty, false, $subject) );
			$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('property_smw', $smwPropertyNamespaceName.":".$qualifierProperty, false, $subject) );
			$qualifierDI = $this->snakToDataItem($actQualifier, $statement, $subject);
			$qualifierValue = StatementTranslator::valueDIToString($qualifierDI);
			$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('value', $qualifierValue, false, $subject) );
			$qualifierObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText($qualifierProperty, $qualifierValue, false, $subject) );

			// set reference in statement obj
			$qualifierPage = $subject->getTitle()."#".$qualifierId;
			$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText('qualifier', $qualifierPage, false, $subject) );
			$statementObj->addDataValue( DataValueFactory::getInstance()->newDataValueByText($statementProperty.$statementValue, $qualifierPage, false, $subject) );
			

			$qNr++;
			$result[] = $qualifierObj;
		}

		return $result;
	
	}



	private static function valueDIToString($di) : string {
		wfDebug('smw: translate DI: '.get_class($di));
		if( $di instanceof DIWikiPage ) {
			return $di->getTitle()->getText()."";
		} else if ( $di instanceof SMWDITime){
			wfDebug('time:'.$di->getSerialization());
			return $di->asDateTime()->format( 'Y-m-d H:i:s' );
			#return "01.01.1700";
		}
		wfdebug('default');
		return $di."";

	}

}

