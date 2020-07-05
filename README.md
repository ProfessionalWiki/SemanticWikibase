# Semantic Wikibase

MediaWiki extension that makes [Wikibase] data available in [Semantic MediaWiki].

## Installation

Platform requirements

* PHP 7.4 or later
* MediaWiki 1.35.x or later
* [Semantic MediaWiki] 1.32 or later
* [Wikibase Repository]

The recommended way to install Semantic Wikibase is using [Composer](https://getcomposer.org) with
[MediaWiki's built-in support for Composer](https://professional.wiki/en/articles/installing-mediawiki-extensions-with-composer).

On the commandline, go to your wikis root directory. Then run these two commands:

```shell script
COMPOSER=composer.local.json composer require --no-update professional-wiki/semantic-wikibase:dev-master
composer update professional-wiki/semantic-wikibase --no-dev -o
```

Then enable the extension by adding the following to the bottom of your wikis `LocalSettings.php` file:

```php
wfLoadExtension( 'SemanticWikibase' );
```

You can verify the extension was enabled successfully by opening your wikis Special:Version page in your browser.

## Translated data

Data part of [Wikibase Items and properties] gets translated to Semantic MediaWiki property value pairs.

### Labels, descriptions, IDs, etc

Translated data in the form `Wikibase datamodel element => SMW property name (SMW property type)`

* EntityId => Wikibase ID (Text)
* Labels => Wikibase label (Monolingual text)
* Descriptions => Wikibase description (Monolingual text)
* Aliases => Wikibase alias (Monolingual text)

### Statements

When a [statement] is translated, only the value of the "main snak" is stored in SMW.

The [SMW property] name is the ID of the Wikibase property, for instance P42. The label of the Wikibase
property gets added as alias. This means both `[[P42::+]]` and `[[Capital city::+]]` are valid in SMW
queries.

Deprecated statements are never translated. Normal statements are not translated if there are preferred statements.
The SMW property type is based on the [Wikibase property type]. Only statements with a supported property type are translated.

## Supported property types

<table>
    <tr>
        <th>Wikibase name (en)</th>
        <th>SMW name (en)</th>
        <th>Wikibase id</th>
        <th>SMW id</th>
    </tr>
    <tr>
        <td><strong>Commons media file</strong></td>
        <td>Text</td>
        <td>commonsMedia</td>
        <td>_txt</td>
    </tr>
    <tr>
        <td><strong>External identifier</strong></td>
        <td>External identifier</td>
        <td>external-id</td>
        <td>_eid</td>
    </tr>
    <tr>
        <td><strong>Geographic coordinates</strong></td>
        <td>Geographic coordinates</td>
        <td>globe-coordinate</td>
        <td>_geo</td>
    </tr>
    <tr>
        <td><strong>Item</strong></td>
        <td>Page</td>
        <td>wikibase-item</td>
        <td>_wpg</td>
    </tr>
    <tr>
        <td><strong>Monolingual text</strong></td>
        <td>Monolingual text</td>
        <td>monolingualtext</td>
        <td>_mlt_rec</td>
    </tr>
    <tr>
        <td><strong>Point in time</strong></td>
        <td>Date</td>
        <td>time</td>
        <td>_dat</td>
    </tr>
    <tr>
        <td><strong>Property</strong></td>
        <td>Page</td>
        <td>wikibase-property</td>
        <td>_wpg</td>
    </tr>
    <tr>
        <td><strong>Quantity</strong></td>
        <td>Subobject (Page + Record)</td>
        <td>quantity</td>
        <td>_wpg + _rec</td>
    </tr>
    <tr>
        <td><strong>String</strong></td>
        <td>Text</td>
        <td>string</td>
        <td>_txt</td>
    </tr>
    <tr>
        <td><strong>URL</strong></td>
        <td>URL</td>
        <td>url</td>
        <td>_uri</td>
    </tr>
    <tr>
        <td><strong></strong></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>

Currently not supported types:

* Entity Schema (entity-schema) 
* Geographic shape (geo-shape) 
* Tabular Data (tabular-data) 

## Configuration

<table>
    <tr>
        <th>Setting name</th>
        <th>Default value</th>
        <th>Description</th>
    </tr>
    <tr>
        <td><strong>$wgSemanticWikibaseLanguage</strong></td>
        <td><i>$wgLanguageCode</i></td>
        <td>The language used for translation of property labels</td>
    </tr>
</table>

TODO: 
$wgExtraNamespaces[WB_NS_PROPERTY] = 'WikibaseProperty';
$wgExtraNamespaces[WB_NS_PROPERTY_TALK] = 'WikibaseProperty_talk';

### Disabling translation for an entity type / namespace

```php
$smwgNamespacesWithSemanticLinks[WB_NS_ITEM] = false;
$smwgNamespacesWithSemanticLinks[WB_NS_PROPERTY] = false;
```

## Enhancement ideas

Data translation:

* Ability to whitelist or blacklist entities from being translated
* Ability to whitelist or blacklist statements from being translated
* Translation of qualifiers, references, statement rank and other non-main-snak data
* Support for Entities other than Items and Properties
* Translation of Item sitelinks

Properties:

* Detection and possibly prevention of property name conflicts between Wikibase and SMW
* (Multilingual) descriptions of Wikibase properties on SMW property pages
* Grouping of Wikibase properties on Special:Browse

## Release notes

### Version 1.0.0

TODO

[Semantic MediaWiki]: https://www.semantic-mediawiki.org
[Wikibase]: https://wikiba.se
[Wikibase Items and properties]: https://www.mediawiki.org/wiki/Wikibase/DataModel
[statement]: https://www.mediawiki.org/wiki/Wikibase/DataModel#Statements
[Wikibase property type]: https://www.mediawiki.org/wiki/Wikibase/DataModel#Datatypes_and_their_Values
[SMW property]: https://www.semantic-mediawiki.org/wiki/Help:Properties_and_types
[Wikibase Repository]: https://www.mediawiki.org/wiki/Extension:Wikibase_Repository
