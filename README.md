# Semantic Wikibase

MediaWiki extension that makes [Wikibase] data available in [Semantic MediaWiki].

## Platform requirements

* PHP 7.4 or later
* MediaWiki 1.35.x
* [Semantic MediaWiki] 3.2.x
* [Wikibase Repository] branch: REL1_35

## Installation

First install MediaWiki, Semantic MediaWiki and Wikibase Repository.

The recommended way to install Semantic Wikibase is using [Composer](https://getcomposer.org) with
[MediaWiki's built-in support for Composer](https://professional.wiki/en/articles/installing-mediawiki-extensions-with-composer).

On the commandline, go to your wikis root directory. Then run these two commands:

```shell script
COMPOSER=composer.local.json composer require --no-update professional-wiki/semantic-wikibase:*
composer update professional-wiki/semantic-wikibase --no-dev -o
```

Then enable the extension by adding the following to the bottom of your wikis `LocalSettings.php` file:

```php
wfLoadExtension( 'SemanticWikibase' );
```

You can verify the extension was enabled successfully by opening your wikis Special:Version page in your browser.

As a final step you need to configure the property namespaces. See the configuration section.

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
</table>

Currently not supported types:

* Entity Schema (entity-schema) 
* Geographic shape (geo-shape) 
* Tabular Data (tabular-data) 

## Configuration

You can configure Semantic Wikibase via [LocalSettings.php].

### Property namespaces

This is the only required configuration for setting up Semantic Wikibase.

Wikibase and Semantic MediaWiki both add a Property namespace called "Property". This results in a conflict which
can be resolved by renaming either the Wikibase property namespace or the Semantic MediaWiki property namespace.

Renaming the Wikibase property namespace:

```php
$wgExtraNamespaces[WB_NS_PROPERTY] = 'WikibaseProperty';
$wgExtraNamespaces[WB_NS_PROPERTY_TALK] = 'WikibaseProperty_talk';
```

Renaming the SMW property namespace:

```php
$wgExtensionFunctions[] = function() {
    $GLOBALS['wgExtraNamespaces'][SMW_NS_PROPERTY] = 'SemanticProperty';
    $GLOBALS['wgExtraNamespaces'][SMW_NS_PROPERTY_TALK] = 'SemanticProperty_talk';
};
```

You can choose what to rename these namespaces to. They do not need to be `WikibaseProperty` and/or `SemanticProperty`.
As long as they are not the same, Semantic Wikibase will work.

### Property label language

The language used for translation of property labels defaults to the wiki language (`$wgLanguageCode`).

This means that if your wiki language is English, and you have a property P1 with Dutch label "lokatie" and
English label "location", the name of the property in Semantic MediaWiki will be "location".

You can specify a language different from your wiki language should be used. This is done with the
`$wgSemanticWikibaseLanguage` setting. With the below example, the label for P1 would be "lokatie":

```php
$wgSemanticWikibaseLanguage = 'nl';
```

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

### Version 0.1

Released on October 22, 2020

* Initial release with support for main snaks and most data types

[Semantic MediaWiki]: https://www.semantic-mediawiki.org
[Wikibase]: https://wikiba.se
[Wikibase Items and properties]: https://www.mediawiki.org/wiki/Wikibase/DataModel
[statement]: https://www.mediawiki.org/wiki/Wikibase/DataModel#Statements
[Wikibase property type]: https://www.mediawiki.org/wiki/Wikibase/DataModel#Datatypes_and_their_Values
[SMW property]: https://www.semantic-mediawiki.org/wiki/Help:Properties_and_types
[Wikibase Repository]: https://www.mediawiki.org/wiki/Extension:Wikibase_Repository
[LocalSettings.php]: https://www.mediawiki.org/wiki/Manual:LocalSettings.php
