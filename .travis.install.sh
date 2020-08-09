#! /bin/bash

set -x

originalDirectory=$(pwd)

cd ..

wget https://github.com/wikimedia/mediawiki/archive/$MW.tar.gz
tar -zxf $MW.tar.gz
mv mediawiki-$MW phase3

cd phase3

composer install --prefer-source

if [ "$DB" == "postgres" ]
then
	psql -c 'create database its_a_mw;' -U postgres
	php maintenance/install.php --dbtype $DBTYPE --dbuser postgres --dbname its_a_mw --pass AdminPassword TravisWiki admin --scriptpath /TravisWiki
else
	mysql -e 'create database its_a_mw;'
	php maintenance/install.php --dbtype $DBTYPE --dbuser root --dbname its_a_mw --dbpath $(pwd) --pass AdminPassword TravisWiki admin --scriptpath /TravisWiki
fi

cd extensions
cp -r $originalDirectory SemanticWikibase
cd SemanticWikibase
composer install
cd ..
cd ..

composer require "mediawiki/semantic-media-wiki=$SMW"
composer require "wikibase/wikibase=$WB"

cat <<EOT >> composer.local.json
{
	"extra": {
		"merge-plugin": {
			"merge-dev": true,
			"include": [
				"extensions/*/composer.json"
			]
		}
	}
}
EOT

composer install

echo '$wgEnableWikibaseRepo = true;' >> LocalSettings.php
echo '$wgEnableWikibaseClient = false;' >> LocalSettings.php
echo 'require_once __DIR__ . "/extensions/Wikibase/repo/Wikibase.php";' >> LocalSettings.php
echo 'require_once __DIR__ . "/extensions/Wikibase/repo/ExampleSettings.php";' >> LocalSettings.php
echo '$wgExtraNamespaces[WB_NS_PROPERTY] = "WikibaseProperty";' >> LocalSettings.php
echo '$wgExtraNamespaces[WB_NS_PROPERTY_TALK] = "WikibaseProperty_talk";' >> LocalSettings.php

echo 'wfLoadExtension( "SemanticWikibase" );' >> LocalSettings.php

echo 'wfLoadExtension( "SemanticMediaWiki" );' >> LocalSettings.php
echo 'enableSemantics( "example.org" );' >> LocalSettings.php

echo 'error_reporting(E_ALL| E_STRICT);' >> LocalSettings.php
echo 'ini_set("display_errors", 1);' >> LocalSettings.php
echo '$wgShowExceptionDetails = true;' >> LocalSettings.php
echo '$wgShowDBErrorBacktrace = true;' >> LocalSettings.php
echo '$wgDevelopmentWarnings = true;' >> LocalSettings.php
echo "putenv( 'MW_INSTALL_PATH=$(pwd)' );" >> LocalSettings.php

php maintenance/update.php --quick
