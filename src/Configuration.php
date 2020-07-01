<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\SemanticWikibase;

class Configuration {

	private array $mwGlobals;

	private function __construct( array $mwGlobals ) {
		$this->mwGlobals = $mwGlobals;
	}

	public static function newFromGlobals( array $mwGlobals ): self {
		return new self( $mwGlobals );
	}

	public function getLanguageCode(): string {
		if ( $this->mwGlobals['wgSemanticWikibaseLanguage'] === '' ) {
			return $this->mwGlobals['wgLanguageCode'];
		}

		return $this->mwGlobals['wgSemanticWikibaseLanguage'];
	}

}
