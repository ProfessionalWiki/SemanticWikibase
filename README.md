# Semantic Wikibase

MediaWiki extension that makes Wikibase data available in Semantic MediaWiki.

## Translated data

Items and properties get translated.

Translated data in the form `Wikibase datamodel element => SMW property name (SMW property type)`

* EntityId => Wikibase ID (string)
* Labels => Wikibase label (monolingual text)
* Descriptions => Wikibase description (monolingual text)

Main snaks of statements also get translated. Deprecated statements are never translated.
Normal statements are not translated if there are preferred statements. The SMW property
name is the ID of the Wikibase property, for instance P42. The SMW property type is based
on the Wikibase property type. Only statements with a supported property type are translated.

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

## Enhancement ideas

Data translation:

* Ability to whitelist or blacklist entities from being translated
* Ability to whitelist or blacklist statements from being translated

Properties:

* Detection and possibly prevention of property name conflicts between Wikibase and SMW
* (Multilingual) descriptions of Wikibase properties on SMW property pages
* Grouping of Wikibase properties on Special:Browse
