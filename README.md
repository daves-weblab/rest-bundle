# Rest Bundle for Pimcore
Many modern Javascript Fronted Frameworks like Ember.js and Angular heavily 
depend on Apis to retrieve the data from a server. Ember.js for example supports
the response to be in JsonApi or Rest-Json by default. Both of these standards are supported
by a variety of adapters available to most Javascript Frontend Frameworks.

This Bundle makes building such Apis based on Pimcore DataObjects extremely easy and automates
the process of transforming a data object into a given Api Standard like JsonApi and Rest-Json.

## Functional Overview
- Transforms data objects into JsonApi or Rest-Json format.
- Build a rest api just by using a simple trait and a route.
- Configurable computed properties to hide business logic from the frontend.
- Extendable Normalizer / Denormalizer architecture. New data type? Just build a normalizer for it.
- Extendable filters for the Api.
- Extendable context architecture. Build a custom Api format is pretty easy.

## Working with the Rest Bundle
- [Architecture Overview](./doc/01_Architecture-Overview.md)
- [Installation](./doc/02_Installation.md) and [Configuration](./doc/03_Configuration.md)
- [Computed Properties](./doc/04_Computed-Properties.md)
- [Api Filters](./doc/05_Api-Filters.md)
- [Custom Normalizer / Denormalizer](./doc/06_Custom-Normalizer-Denormalizer.md)
- [Build a custom format](./doc/07_Build-a-custom-Format.md)