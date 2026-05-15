# Building Insurance Comparison API

A REST API for comparing building insurance tariffs across providers. Built as a senior PHP developer interview project over one week.

---

## Tech Stack

- **PHP 8.4** with readonly classes, constructor promotion, attributes
- **Symfony 7** — routing, validation, event subscribers, cache contracts
- **Doctrine ORM** — entities, repositories, migrations
- **MySQL** — primary data store
- **PHPUnit** — unit and integration tests

---

## Local Setup

```bash
# Install dependencies
composer install

# Copy and configure environment
cp .env .env.local
# Set DATABASE_URL in .env.local

# Create database and run migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Load fixtures (providers, products, tariffs)
php bin/console doctrine:fixtures:load

# Start local server
symfony server:start
```

---

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/tariffs` | List active tariffs. Supports `limit`, `page`, `sort`, `direction`, `product_type`, `provider` query params |
| `GET` | `/api/tariffs/{id}` | Get a single tariff by ID |
| `GET` | `/api/products` | List available insurance products |
| `GET` | `/api/products/types` | List available product types |
| `POST` | `/api/comparisons` | Run a comparison. Returns ranked tariff results for the given building profile |

**Comparison request body:**
```json
{
  "zipcode": "80331",
  "building_year": 2020,
  "living_area": 120,
  "building_type": "house",
  "has_garage": true,
  "has_solar_panels": false
}
```

---

## Architecture

The project follows a **layered architecture**: Controllers → Application Services → Repositories → Entities.

**Why this structure:**

- **DTOs** keep the HTTP layer decoupled from the domain. Controllers never touch entities directly; entities never leak into responses.
- **Service layer** owns business logic and transaction boundaries. Controllers are thin — validate input, call service, return response.
- **Repository layer** encapsulates all query logic. Services depend on repository interfaces, not Doctrine internals.
- **Cache** (`ProductCatalogService`) wraps expensive product/type queries with a 24h TTL. Product catalog changes rarely; no reason to hit the DB on every request.
- **Custom validator** (`NotFutureYear`) keeps the `buildingYear` constraint dynamic rather than hardcoding the current year.
- **`ApiExceptionSubscriber`** centralises error formatting. All exceptions — HTTP or otherwise — produce consistent JSON responses. Production hides internals; non-production exposes the exception class and message for easier debugging.

---

## Known Limitations

These are honest gaps, not oversights — this was built in one week as a scoped interview project.

- **`productType` is hardcoded to `building`** in `ComparisonService`. The DTO accepts it from the request but the service ignores it. Straightforward to wire up, intentionally left as a next step.
- **Risk level is a placeholder.** Every result gets `medium`. Real risk calculation would factor in building age, area, type, and location data.
- **`yearlyPrice` is never calculated.** The field exists on the entity and DTO but is always `null`. Would be `monthlyPrice * 12` with a potential annual discount applied.
- **`recommendationReason` is always `null`.** Intended to hold an AI-generated explanation for why a tariff is recommended.
- **Ranking is based on the tariff's static `score` field.** No dynamic scoring based on the user's building profile happens yet.
- **No authentication.** Endpoints are open. Production would need at minimum API key validation.
- **No pagination metadata** in list responses — `totalCount`, `currentPage` etc. are missing.

---

## What's Next

- **Dynamic scoring engine** — weight tariff attributes against the user's building profile (area, year, type, garage, solar) to produce a personalised ranking score per comparison.
- **Real risk calculation** — classify risk level based on building age and type, not a hardcoded default.
- **AI-generated recommendation reasons** — integrate an LLM to produce a plain-language explanation for each result. I have a working RAG pipeline at [github.com/shzaki/rag](https://github.com/shzaki/rag) that could back this.
- **`productType` from request** — pass it through from the comparison request DTO into the service query.
- **Annual pricing** — calculate `yearlyPrice` with configurable discount rules.
- **Pagination metadata** in list responses.
- **Provider filtering on comparisons** — currently only tariff listing supports provider filtering.
