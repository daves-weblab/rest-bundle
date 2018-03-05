# Rest Bundle for Pimcore

> Please note that this bundle is currently in Version 0.8 and not ready for production yet
as some interfaces might change without notice. Version 1.0 is planned to be released 
on 30th of April 2018. 

Many modern Javascript Fronted Frameworks like Ember.js and Angular heavily 
depend on APIs to retrieve the data from a server. Ember.js for example supports
the response to be in JsonAPI or Rest-Json by default. Both of these standards are supported
by a variety of adapters available to most Javascript Frontend Frameworks.

This Bundle makes building such APIs based on Pimcore DataObjects extremely easy and automates
the process of transforming a data object into a given API Standard like JsonAPI and Rest-Json.

## Functional Overview
- Transforms data objects into JsonAPI or Rest-Json format.
- Build a rest API just by using a simple trait and a route.
- Configurable computed properties to hide business logic from the frontend.
- Extendable Normalizer / Denormalizer architecture. New data type? Just build a normalizer for it.
- Extendable filters for the API.
- Extendable context architecture. Building a custom API format is pretty easy.

## Working with the Rest Bundle
- [Architecture Overview](./doc/01_Architecture-Overview.md)
- [Installation](./doc/02_Installation.md) and [Configuration](./doc/03_Configuration.md)
- [Computed Properties](./doc/04_Computed-Properties.md)
- [API Filters](./doc/05_API-Filters.md)
- [Custom Normalizer / Denormalizer](./doc/06_Custom-Normalizer-Denormalizer.md)
- [Build a custom format](./doc/07_Build-a-custom-Format.md)