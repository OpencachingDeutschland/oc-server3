# This is the result of me reviewing the code base, an unsorted list of topics to address

- js/gpx.js: strip all non OC definition, make sure the remaining definitions resemble OC's assignments

  - GPX_CACHE_TYPES

  - GPX_CONTAINER_TYPES

- convertGC() this function is GC specific, it can be deleted

- referenceCode2Id() GC specific, can be deleted

- helpers.js

  - traceLog() are we using this anywhere? Should we consider using it and thus keep it?

- iconPaths.js: Are there paths in for "types" that are NEVER used in opencaching, stuff that came from GC and AL in GCxM?

- in general when doing fetch()

  - should we use a wrapper? In gcxm we do

  - always retrieve data = await result.text()

  - then check any error, we could have received a non-json payload

  - only when all is good do newData = JSON.parse(data)

  - or can we depend on ALWAYS getting JSON back

- uniCache.js

  - OKAPI shaped? That is not true in this environment. Instead, the backend delivers data extracted from
    the database. In theory we could already transform to uniCache in the backend and always deliver a proper
    uniCache object to the frontend, pls investigate and advice.

  - for instance here:  #[Route("/api/caches/search", name: "api_caches_search")] ... couldn't we create a proper uniCache object here? and do the same
    everywhere where the backend ships a cache object to the front end? We would not need yet another transformation in the front end js. The reason
    why we do this in GCxM is twofold (1) we have no control what the providers (GC, OC, AL) delivers and (2) we must unify it such that the front end can operate
    on a precisely shaped cache object

  - ocToGCCacheTypes ... does this match OC's definitions? Should we really call this ocToGC? we don't map anything from OC to GC, we map an id to text

  - ocToGCSizeTypes ... same as above

  - statusMap ... do we want to use a mapping or move towards native OC states? This may be a relic from having a multi platform app that translates
    everyting to GC definitions.
