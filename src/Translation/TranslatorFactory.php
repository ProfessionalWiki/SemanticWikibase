<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\Translation;

use SMW\DataValueFactory;
use SMW\DIWikiPage;
use Wikibase\DataModel\Services\Lookup\PropertyDataTypeLookup;

class TranslatorFactory {

	private DataValueFactory $dataValueFactory;
	private PropertyDataTypeLookup $propertyTypeLookup;

	public function __construct( DataValueFactory $dataValueFactory, PropertyDataTypeLookup $propertyTypeLookup ) {
		$this->dataValueFactory = $dataValueFactory;
		$this->propertyTypeLookup = $propertyTypeLookup;
	}

	public function newItemTranslator( DIWikiPage $subject ): ItemTranslator {
		return new ItemTranslator(
			$this->newFingerprintTranslator( $subject ),
			$this->newStatementListTranslator( $subject )
		);
	}

	public function newPropertyTranslator( DIWikiPage $subject ): PropertyTranslator {
		return new PropertyTranslator(
			$this->newFingerprintTranslator( $subject ),
			$this->newStatementListTranslator( $subject )
		);
	}

	private function newFingerprintTranslator( DIWikiPage $subject ) {
		return new FingerprintTranslator(
			new TermTranslator( $this->getMonoTextTranslator(), $subject )
		);
	}

	private function newStatementListTranslator( DIWikiPage $subject ): StatementListTranslator {
		return new StatementListTranslator(
			$this->getStatementTranslator(),
			$subject
		);
	}

	public function getStatementTranslator(): StatementTranslator {
		return new StatementTranslator(
			$this->getDataValueTranslator(),
			$this->getContainerValueTranslator(),
			$this->propertyTypeLookup
		);
	}

	private function getDataValueTranslator(): DataValueTranslator {
		return new DataValueTranslator();
	}

	public function getContainerValueTranslator(): ContainerValueTranslator {
		return new ContainerValueTranslator(
			$this->getDataValueTranslator(),
			$this->getMonoTextTranslator()
		);
	}

	public function getMonoTextTranslator(): MonoTextTranslator {
		return new MonoTextTranslator(
			$this->dataValueFactory
		);
	}

}
