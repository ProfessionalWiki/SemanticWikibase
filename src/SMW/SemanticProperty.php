<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase\SMW;

class SemanticProperty {

	private string $id;
	private string $type;
	private string $label;
	private ?string $alias;

	public function __construct( string $id, string $dvType, string $label, ?string $alias = null ) {
		$this->id = $id;
		$this->type = $dvType;
		$this->label = $label;
		$this->alias = $alias;
	}

	public function getId(): string {
		return $this->id;
	}

	public function getType(): string {
		return $this->type;
	}

	public function getLabel(): string {
		return $this->label;
	}

	/**
	 * @return string[]
	 */
	public function getAliases(): array {
		return $this->alias === null ? [] : [ $this->alias ];
	}

}
