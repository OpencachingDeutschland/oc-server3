# Architecture Analysis: OKAPI vs Symfony Tree

Two paradigms coexist in this codebase. One works. One doesn't.

## OKAPI (htdocs/okapi/) — the working paradigm

```
Request  →  validate_input()  →  $db->query("SELECT ...")  →  return JSON
```

Three layers. Zero ceremony.

- **No Entity classes** — data comes from the database as arrays
- **No Repository classes** — each OKAPI service file has its own SQL
- **No ORM** — raw PDO or very thin wrapper
- **No proxies, no annotations, no `isNew()`**
- **One file, one concern** — `services/caches/create.php` does one thing

Every OKAPI file is self-contained. You can open any file, read top to bottom,
and understand exactly what it does in 60 seconds.

## Symfony Tree (htdocs_symfony/) — the abandoned paradigm  

```
Request  →  Controller  →  Repository(ServiceEntityRepository)  →  Entity(#[ORM\...])
                                        ↑                                  ↑
                            AbstractEntity(isNew/fromArray/toArray)
                                        ↑
                            DoctrineBundle proxy system
                                        ↑
                            ManagerRegistry / EntityManager
```

Six layers. Most of them unused.

- **Entity classes** — 30 files with `#[ORM\Entity]`, `#[ORM\Column]`, getters/setters
- **AbstractEntity** — `isNew()`, `fromArray()`, `toArray()` — never called outside tests
- **ServiceEntityRepository** — 20 repos extend this, but NOBODY calls `$entityManager->persist()`
- **DoctrineBundle proxy** — lazily wraps repos, crashes on prod (the bug we just debugged)
- **getEntityFromDatabaseArray()** — converts DB array to Entity object with typed properties
- **getDatabaseArrayFromEntity()** — reverses it. Called only by create/update methods nobody uses

**What's actually used from this stack:**
- `Connection::createQueryBuilder()` — Doctrine DBAL, the one working piece
- `Connection::insert/update/delete/executeStatement()` — same, just writes

**What's dead scaffolding causing real problems:**
- `ServiceEntityRepository` — crashes on oc3 prod because of the proxy system
- `#[ORM\Entity]` annotations — 30 files of annotations that no code reads
- `AbstractEntity` — 38 lines of abstract methods nobody calls
- `getEntityFromDatabaseArray()` — mapping layer between two identical representations

## The Proof

Our refactor of CachesController eliminated 57 raw SQL calls. Every single one was
replaced with `$this->connection->createQueryBuilder()` — pure DBAL, no ORM involved.
Not one of them needed an Entity or `$entityManager->persist()`.

When we created two new repositories (`CacheDescRepository`, `WaypointsRepository`) and
added `extends ServiceEntityRepository` + Entity classes to match the project pattern,
it **immediately broke the production system** with a `ServiceEntityRepositoryProxy`
error. Removing `extends ServiceEntityRepository` fixed it instantly.

The ORM stack isn't just unused — it's actively harmful.

## The Exception: UserEntity

One Entity must survive: `UserEntity`. It implements Symfony's `UserInterface` and
`LegacyPasswordAuthenticatedUserInterface` which the security firewall requires.
The password hasher, LoginFormAuthenticator, and UserProvider all depend on it.
This is a legitimate use of the Entity pattern — but it's the ONLY one.
