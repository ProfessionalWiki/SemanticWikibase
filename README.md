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
        <th>Wikibase type</th>
        <th>SMW type</th>
        <th></th>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
