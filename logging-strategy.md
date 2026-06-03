# Logging Strategy — cgeo / OKAPI / opencaching.de

> Internal context (not for the issue): the three-party scope, no-coordination reality, and GC constraint are in project memory `project_logging_strategy.md`. The OKAPI fieldnote import service will also need to handle the "needs maintenance" merging, but that detail is out of scope for this discussion.

---

I agree in full [with the prerequisite that c:geo must adapt to server-side API changes and can only act on its own side] — this is the reality we live in when consuming a geocaching platform. I have already stepped up to work on all three components: (1) c:geo, (2) OKAPI, (3) opencaching.de. To what success that will amount to, time will tell.

With that said, here is my reading of the logging flow, independent of platform.

---

## The state machine

We have two scenarios: (1) online logging and (2) offline logging. My goal is to model the two to be as close to identical as possible — identical when scoped at platform A, equally identical when scoped at platform B, and for whatever other platform c:geo already supports or may support in the future.

To determine which log types are valid at any point in time, we need a state machine. The complete picture is a 3-tuple:

```
{ cacheType, cacheState, submitterRole }
```

where:
- `cacheType` ∈ { traditional, mystery, event, … }
- `cacheState` ∈ { active, disabled, locked, archived, … }
- `submitterRole` ∈ { anyUser, owner, admin/reviewer }

The theoretically clean solution would be to drive this state machine from a platform-provided service — something like `logs/capabilities` — so that `cacheState` and the submitter's role are always authoritative at the moment the user picks a log type.

There are two structural problems with that approach that no implementation can solve:

1. **No reception in the field.** Offline logs are composed where there is often no mobile connectivity. A `logs/capabilities` query is simply not possible at the moment the user selects a log type.
2. **Async submission.** Fieldnotes are frequently processed long after composition — at home via a desktop fieldnote processor. By the time the fieldnote is submitted, the cache state on the authoritative platform may have changed. A log type that was valid when selected may be rejected on submission.

These are structural constraints inherent to offline logging and the fieldnote model, not implementation gaps. No service call resolves them.

The state machine must therefore be **static, modeled in the client, per platform**. `logs/capabilities` or any equivalent service cannot function as a gate. Rejection at submission time is always possible and must be communicated gracefully, not prevented.

This also disposes of the `admin/reviewer` value in `submitterRole`. Administrative log types — those that allow privileged accounts to transition cache states (e.g. Active → Locked) — require authoritative real-time state at composition time, which the above constraints make impossible. They are out of scope by design, not by omission. What remains of `submitterRole` is a simple binary `isOwner` flag — a modifier on a small subset of log types, not a full axis. The tuple collapses to:

```
{ cacheType, cacheState }  +  isOwner modifier where applicable
```

---

## The fieldnote contract

There is only one object that can be marshaled between the entities in both the online and offline path: **fieldnotes**. This is the contract all parties must honor.

The format was defined by Groundspeak and Garmin, with input from third-party tooling. Its technical realization is flawed in several ways — character encoding alone is a mess (UTF-8, UTF-16/LE, UTF-16/BE, with or without BOM). None of that is changeable, and critically, **the format cannot be extended**:

```
cacheCode, logDate, logType, logText
```

Four fields. No qualifiers, no properties, no additional dimensions.

---

## The GC path

On the GC side, the fieldnote contract is followed cleanly. The Problem qualifier in the c:geo UI is a convenience: the user composes one log with a problem flag, and c:geo sends two separate logs to GC — the primary log and a problem log. GC supports this natively. No emulation, no workaround.

---

## The OC path

opencaching.de does not have a native "needs maintenance" log type. The fieldnote contract cannot carry a qualifier. This creates a gap that is currently bridged by emulation on both paths:

- **Online (via OKAPI):** c:geo sends one log plus a cache property carrying the problem signal. opencaching.de accepts this via the properties mechanism.
- **Offline (via the OC fieldnote processor):** the processor receives two fieldnote records — the primary log and the problem record — and merges them server-side into a single log entry with a property.

Both paths converge on the same result. The emulation is symmetric. This can reasonably be considered working as intended. opencaching.de could make "needs maintenance" a first-class log type, which would be cleaner, but it is not a hard requirement given that both paths already handle it consistently.

---

## The one gap

There is exactly one thing broken in this picture. c:geo can upload fieldnotes directly to GC today — this is a standard, well-functioning path. For opencaching.de, that path does not exist: OKAPI has no fieldnote import endpoint, which means c:geo cannot treat OC the same way it treats GC in the offline path.

I have submitted a PR to OKAPI that adds exactly this endpoint. Once it lands, the online/offline symmetry for OC is complete.
