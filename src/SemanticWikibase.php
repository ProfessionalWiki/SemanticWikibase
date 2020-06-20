<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

class SemanticWikibase {

	public static function getGlobalInstance(): self {
		return new self();
	}

	public function getFixedProperties(): FixedProperties {
		return new FixedProperties();
	}

	public function getSemanticDataUpdate(): SemanticDataUpdate {
		return new SemanticDataUpdate();
	}

}
