# Symfony Notes API

A RESTful API built with Symfony and Doctrine for managing notes.

This project is a backend learning playground focused on:

- clean architecture
- separation of concerns (Controller / Service / DTO)
- validation
- pagination, sorting and filtering
- proper error handling
- RESTful API design
- Response DTOs for all API outputs
- Explicit response mapping (Entity → DTO)
- Stable and predictable API response schema
- User registration
- JWT-based authentication
- Stateless API security
- Ownership-based access control
- Notes are scoped to the authenticated user

The codebase is intentionally structured to reflect real-world backend practices.

## Features

- Create notes
- Read a single note by ID
- List notes with pagination
- Sorting (id, title, createdAt)
- Filtering by title (search)
- Partial updates using PATCH
- Delete notes
- Input validation with meaningful HTTP status codes
- Business logic isolated in services
- Docker-based development and production environments
- Docker Compose profiles for strict dev / prod separation
- Multi-stage Docker builds for optimized production images
- Separate PostgreSQL databases and volumes for dev and prod
- Zero local runtime dependencies (PHP, Composer, PostgreSQL)
- Makefile for standardized and repeatable workflows
- Environment-based configuration via .env and .env.dev

## API Endpoints

### Create note

POST /api/note

Request body:

```json
{
    "title": "My note",
    "content": "Some longer note content"
}
```

Responses:
201 Created  
400 Bad Request  
422 Unprocessable Entity

---

### Get note by ID

GET /api/note/{id}

Responses:
200 OK  
404 Not Found

---

### List notes

GET /api/note?page=1&limit=10&sortBy=createdAt&order=DESC&search=note

Query parameters:

- page (int, optional)
- limit (int, optional)
- sortBy (id | title | createdAt)
- order (ASC | DESC)
- search (optional, filters by title)

Response:

```json
{
    "notes": {
        "meta": {
            "page": 1,
            "limit": 10,
            "total": 47
        },
        "items": [
            {
                "id": 1,
                "title": "Example note",
                "content": "Some content",
                "createdAt": "2026-01-18 13:58:36"
            }
        ]
    }
}
```

---

### Update note (partial)

PATCH /api/note/{id}

Request body (at least one field required):

```json
{
    "title": "Updated title",
    "content": "Updated content"
}
```

Responses:
200 OK  
400 Bad Request  
404 Not Found  
422 Unprocessable Entity

### Delete note

DELETE /api/note/{id}

Responses:
204 No Content  
404 Not Found

### Register user

POST /api/register

Request body:

```json
{
    "email": "user@example.com",
    "password": "secret-password"
}
```

Responses:
201 Created
409 Conflict
422 Unprocessable Entity

### Login (JWT)

POST /api/login_check

Request body:

```json
{
    "email": "user@example.com",
    "password": "secret-password"
}
```

Response:

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

## Health Check

GET /health

Used for container liveness checks.

Returns 200 OK if the application is running.

Responses:
200 OK
401 Unauthorized

## Authentication & Authorization

This API uses JWT (JSON Web Tokens) for authentication.

- Users authenticate via `/api/login_check`
- A valid JWT token is required for all `/api/*` endpoints
- Authentication is stateless (no sessions, no cookies)
- Tokens must be sent via the `Authorization` header:

```http
Authorization: Bearer <token>
```

- Only `/api/register` and `/api/login_check` are publicly accessible

## HTTP Semantics

This API follows common REST conventions:

- 200 OK for successful reads and updates
- 201 Created for resource creation
- 400 Bad Request for malformed input
- 404 Not Found when a resource does not exist
- 422 Unprocessable Entity for validation errors

## Ownership Model

- Each Note belongs to exactly one User
- Notes are always created with the authenticated user as owner
- List endpoints return only notes owned by the current user
- Accessing another user’s note returns 404 Not Found
- Update and delete operations are restricted to the note owner

## Architecture Overview

- Controllers handle HTTP concerns only (request/response)
- DTOs define the API contract and input structure
- Validation is handled via Symfony Validator
- Services contain all business logic
- Entities are never exposed directly to the API
- Response DTOs define the public API output format
- Dedicated mappers convert Entities to Response DTOs
- Controllers never expose Doctrine entities directly
- Authentication handled via Symfony Security and JWT
- Authorization and ownership enforced in the service layer
- Security context accessed via Symfony Security service
- Controllers contain no authentication or authorization logic

## Container & Environment Design

- The application runs fully inside Docker containers
- No services run directly on the host machine
- Development and production are isolated using Docker Compose profiles
- Each profile has:
    - its own PHP container
    - its own PostgreSQL container
    - its own persistent Docker volume
- Switching environments does not affect data integrity

This setup mirrors real-world deployment and staging workflows.

## Requirements

You need the following tools installed:

- Docker Desktop (includes Docker Compose v2)
- GNU Make
- Git

No local Symfony, PHP, Composer or PostgreSQL installation is required.

## Project Setup

This project is fully containerized using Docker and Docker Compose.
A Makefile is included to simplify and standardize commonly used commands.
You are encouraged to use it once the project is running.

The setup is based on two Docker profiles:
• dev – development environment
• prod – production-like environment

Both profiles run independently and use separate databases and volumes.

⸻

1. Clone the repository

```bash
git clone https://github.com/Melikkoc/symfony-notes-api.git
```

```bash
cd symfony-notes-api/backend
```

⸻

2. Environment files

This project does not ship ready-to-use secrets or environment files.
You must create the environment configuration yourself.

Required environment files:
• .env
• .env.dev

JWT keys are not included in the repository and are generated locally after the first startup.

The environment files only define configuration values.

.env (production defaults):
• POSTGRES_DB
• POSTGRES_USER
• POSTGRES_PASSWORD
• DATABASE_URL pointing to db-prod
• JWT_PASSPHRASE

.env.dev (development):
• POSTGRES_DB
• POSTGRES_USER
• POSTGRES_PASSWORD
• DATABASE_URL pointing to db-dev
• No JWT passphrase required

In development, JWT key generation is optional until authentication endpoints are used.
JWT keys are generated inside the running PHP container after the initial startup.

POSTGRES\_\* variables are used by the PostgreSQL container.
The application itself only relies on DATABASE_URL.

Example difference:
• .env uses db-prod
• .env.dev uses db-dev

All other values can stay identical.

⸻

3. First start (development)

For the first run, build and start the dev environment:

```bash
docker compose --profile dev up --build
```

Once the containers are running, execute migrations:

```bash
docker compose exec php-dev php bin/console doctrine:migrations:migrate
```

This step is required only once per database.

⸻

4. The API will be available at:

```http
http://localhost:8080
```

⸻

5. Subsequent starts (development)

After the initial setup, start the dev environment with:

```bash
docker compose --profile dev up -d
```

⸻

6. Switching between dev and prod

When switching profiles, always stop the currently running stack first:

```bash
docker compose -p backend down
```

This removes containers but keeps database data intact.

You can then start the other profile safely.

⸻

7. First start (production)

The production profile simulates a real deployment environment.

It uses:
• a separate PHP image
• optimized Composer install (no dev dependencies)
• a dedicated PostgreSQL database
• its own persistent Docker volume
• production environment variables from .env

For the first production run, build and start the stack:

```bash
docker compose --profile prod up --build
```

Once the containers are running, execute database migrations:

```bash
docker compose exec php php bin/console doctrine:migrations:migrate
```

This initializes the production database schema.

⸻

8. Subsequent starts (production)

After the first setup, start production with:

```bash
docker compose --profile prod up -d
```

No rebuild or migration is required unless:
• dependencies changed
• migrations were added
• Docker configuration changed

⸻

9. Makefile usage

A Makefile is provided to shorten and standardize all common commands
(dev, prod, migrations, logs, database access, queries).

Refer to the Makefile itself for the available shortcuts and usage.

## Tech Stack

- PHP 8.2
- Symfony 7
- Doctrine ORM & Migrations
- PostgreSQL 16
- JWT Authentication (LexikJWTAuthenticationBundle)
- Docker
- Docker Compose (profiles, multi-container setup)
- Nginx
- GNU Make

## Purpose

This repository exists as a learning project to build a solid understanding of
backend development with Symfony.

The focus is on correctness, clarity, and long-term maintainability rather than
shortcuts or framework magic.

In addition to application-level architecture, this project also focuses on
professional containerization, environment separation, and production-ready workflows
using Docker, Docker Compose profiles, and multi-stage builds.

## Security Notes

- Stateless JWT authentication
- No Doctrine entities exposed through the API
- Ownership checks implemented at service level
- Consistent error responses to avoid information leakage
- Secrets are never committed to the repository
- JWT private keys are generated locally per environment
- Production configuration differs explicitly from development

## Notes

This repository is actively evolving.  
API documentation, internal structure, and supporting files may change as new features are added and the architecture is refined.
