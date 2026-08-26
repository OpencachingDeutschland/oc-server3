/***************************************************************************
 * uniCache.js — OC-only frontend cooker
 *
 * Transforms a raw OKAPI-shaped OC
 * object (as returned by /api/cache/{wp}) into a uniCacheWP suitable for
 * rendering by cache.js and the map module.
 *
 * Country/region come from the backend; getCountryInfo() (npm package
 * geojson-places) is not available in the browser, so countryCode is
 * returned as null unless supplied directly by the backend.
 ***************************************************************************/

// -----------------------------------------------------------------
// Lookup tables — bidirectional (id ↔ name) for OC cache and size types

export const OC_CACHE_TYPES = {
  1:  'Unknown',
  2:  'Traditional',
  3:  'Multi',
  4:  'Virtual',
  5:  'Webcam',
  6:  'Event',
  7:  'Quiz',
  8:  'Math/Physics',
  9:  'Moving',
  10: 'Drive-in',

  'Unknown':      1,
  'Traditional':  2,
  'Multi':        3,
  'Virtual':      4,
  'Webcam':       5,
  'Event':        6,
  'Quiz':         7,
  'Math/Physics': 8,
  'Moving':       9,
  'Drive-in':     10,
};

export const OC_SIZE_TYPES = {
  1: 'Unknown',
  2: 'Micro',
  3: 'Regular',
  4: 'Large',
  5: 'Virtual',
  6: 'Other',
  8: 'Small',

  'none':    1,
  'nano':    6,
  'micro':   2,
  'small':   8,
  'regular': 3,
  'large':   4,
  'xlarge':  4,
  'unknown': 1,
  'other':   1,
};

// -----------------------------------------------------------------
// Helpers

function calcShortName(rawName, sequence) {
  const sequencePrefix = typeof sequence === 'number' ? `(${sequence + 1}) ` : '';
  const nameWithSequence = `${sequencePrefix}${rawName}`;
  return nameWithSequence.length > 25
    ? nameWithSequence.substring(0, 25) + ' ...'
    : nameWithSequence;
}

// Backend sends 'Active' | 'Disabled' | 'Archived' directly — no OKAPI translation needed.
const VALID_STATUSES = new Set(['Active', 'Disabled', 'Archived']);

function normalizeStatus(status) {
  return VALID_STATUSES.has(status) ? status : 'Unknown';
}

// -----------------------------------------------------------------
// ocToUniCache()
//
// Create a unified cache object from a raw OKAPI-shaped OC object.
// `session` is an object that may contain `platforms.oc.username` for
// owner detection. Pass null for anonymous.

export function ocToUniCache(oc, session) {
  let postedCoordinates = { latitude: 0, longitude: 0 };
  if (oc.location && typeof oc.location === 'string' && oc.location.includes('|')) {
    const [postedLat, postedLon] = oc.location.split('|');
    postedCoordinates = {
      latitude: parseFloat(postedLat) || 0,
      longitude: parseFloat(postedLon) || 0,
    };
  }

  let correctedCoordinates = null;
  if (oc.alt_wpts) {
    for (const wp of oc.alt_wpts) {
      if (wp.type === 'user-coords' && wp.location) {
        const [lat, lon] = wp.location.split('|');
        correctedCoordinates = {
          latitude: parseFloat(lat) || 0,
          longitude: parseFloat(lon) || 0,
        };
        break;
      }
    }
  }
  const hasCC = !!correctedCoordinates;
  const coords = hasCC ? correctedCoordinates : postedCoordinates;

  let foundDate = null;
  let dnfDate = null;
  if (oc.latest_logs) {
    for (const log of oc.latest_logs) {
      if (oc.is_found && !foundDate && log.type === 'Found it') {
        foundDate = log.date?.substring(0, 10) ?? null;
      }
      if (oc.is_not_found && !dnfDate && log.type === "Didn't find it") {
        dnfDate = log.date?.substring(0, 10) ?? null;
      }
      if (foundDate && dnfDate) break;
    }
  }
  if (oc.is_not_found && !dnfDate) dnfDate = 'DNF';

  const rawName = oc.name?.replace(/\u0027/g, '`') || 'Unnamed';

  const typeId = OC_CACHE_TYPES[oc.type] ?? 8;
  const sizeId = OC_SIZE_TYPES[oc.size2] ?? 1;

  const ownerUsername = session?.platforms?.oc?.username ?? null;

  return {
    _id: oc.code,
    platform: 'OC',

    isOwned:    ownerUsername ? oc.owner?.username === ownerUsername : false,
    isArchived: normalizeStatus(oc.status) === 'Archived',
    isDisabled: normalizeStatus(oc.status) === 'Disabled',
    isFound:    !!foundDate,
    isDNF:      !foundDate && !!oc.is_not_found,
    hasPCN:     !!oc.my_notes?.length,
    hasCC,
    isCached:    oc.isCached    ?? false,
    isPartial:   oc.isPartial   ?? false,
    isGuessable: oc.isGuessable ?? false,
    isFavorited: oc.is_recommended ?? false,
    isIgnored:   false,
    isWatched:   oc.is_watched ?? false,
    hasDraft:    false,

    name:          rawName,
    referenceCode: oc.code,
    geocacheType: {
      id:   typeId,
      name: OC_CACHE_TYPES[typeId] ?? oc.type,
    },
    geocacheSize: {
      id:   sizeId,
      name: OC_SIZE_TYPES[sizeId] ?? oc.size2,
    },
    foundDate,
    dnfDate,
    difficulty:    oc.difficulty ?? 1,
    terrain:       oc.terrain    ?? 1,
    status:        normalizeStatus(oc.status),
    publishedDate: oc.date_created?.substring(0, 10) || 'unpublished',
    ownerAlias:    oc.owner?.username?.replace(/\u0027/g, '`') || 'Unknown',
    ownerName:     null,
    ownerCode:     oc.owner?.username ?? null,
    lat:           coords.latitude  ?? 0,
    lon:           coords.longitude ?? 0,
    favoritePoints: oc.recommendations ?? null,
    findCount:      oc.founds          ?? null,
    pcn:            oc.my_notes        ?? null,
    postedCoordinates,
    correctedCoordinates,
    location: {
      country:     oc.country2     ?? null,
      state:       oc.region       ?? null,
      countryCode: oc.country_code ?? null,
    },
    ianaTimezoneId: null,

    requiresPasswd: oc.req_passwd ?? false,
    logPasswd:      null,

    ucx: {
      importDate:       new Date().toISOString(),
      sourceCollection: 'caches',
    },
  };
}

// -----------------------------------------------------------------
// ocToUniCacheWP()
//
// Add transient fields (shortName, isSelected) for frontend use.

export function ocToUniCacheWP(oc, session, sequenceNumber) {
  const uniCache = ocToUniCache(oc, session);
  return {
    ...uniCache,
    isSelected: oc.isSelected ?? false,
    shortName:  calcShortName(uniCache.name, sequenceNumber),
  };
}
