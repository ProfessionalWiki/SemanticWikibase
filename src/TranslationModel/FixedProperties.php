<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\TranslationModel;

use SMW\DataValues\MonolingualTextValue;
use SMW\DataValues\StringValue;

class FixedProperties {

	public const ID = '___WIKIBASE_ID';
	public const LABEL = '___WIKIBASE_LABEL';
	public const DESCRIPTION = '___WIKIBASE_DESCRIPTION';

	public const QUANTITY_VALUE = '___WIKIBASE_QUANTITY';
	public const QUANTITY_LOWER_BOUND = '___WIKIBASE_LBOUND';
	public const QUANTITY_UPPER_BOUND= '___WIKIBASE_UBOUND';
	public const QUANTITY_UNIT = '___WIKIBASE_UNIT';

	public function registerFixedTables( array &$customFixedProperties, array &$fixedPropertyTablePrefix ): void {
		foreach ( $this->getAll() as $property ) {
			$customFixedProperties[$property->getId()] = str_replace( '___', '_', $property->getId() );
			$fixedPropertyTablePrefix[$property->getId()] = 'swb_fpt';
		}
	}

	/**
	 * @return SemanticProperty[]
	 */
	public function getAll(): array {
		return [
			$this->newEntityId(),
			$this->newLabel(),
			$this->newDescription(),

			$this->newQuantityValue(),
			$this->newQuantityLowerBound(),
			$this->newQuantityUpperBound(),
			$this->newQuantityUnit(),
		];
	}

	private function newEntityId(): SemanticProperty {
		return new SemanticProperty( self::ID, StringValue::TYPE_ID, 'Wikibase ID' );
	}

	private function newLabel(): SemanticProperty {
		return new SemanticProperty( self::LABEL, MonolingualTextValue::TYPE_ID, 'Wikibase label' );
	}

	private function newDescription(): SemanticProperty {
		return new SemanticProperty( self::DESCRIPTION, MonolingualTextValue::TYPE_ID, 'Wikibase description' );
	}

	private function newQuantityValue(): SemanticProperty {
		return new SemanticProperty( self::QUANTITY_VALUE, \SMWNumberValue::TYPE_ID, 'Wikibase quantity' );
	}

	private function newQuantityLowerBound(): SemanticProperty {
		return new SemanticProperty( self::QUANTITY_LOWER_BOUND, \SMWNumberValue::TYPE_ID, 'Wikibase lower bound' );
	}

	private function newQuantityUpperBound(): SemanticProperty {
		return new SemanticProperty( self::QUANTITY_UPPER_BOUND, \SMWNumberValue::TYPE_ID, 'Wikibase upper bound' );
	}

	private function newQuantityUnit(): SemanticProperty {
		return new SemanticProperty( self::QUANTITY_UNIT, StringValue::TYPE_ID, 'Wikibase unit' );
	}


}
