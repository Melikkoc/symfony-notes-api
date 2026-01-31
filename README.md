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

- dev – development environment
- prod – production-like environment

Both profiles run independently and use separate databases and volumes.

⸻

1. Clone the repository

```bash
git clone https://github.com/Melikkoc/symfony-notes-api.git
```

⸻

2. Environment files

This project does not ship ready-to-use secrets or environment files.
You must create the environment configuration yourself.

Required environment files:

- .env
- .env.dev

    2.1 Create .env (production defaults)

Copy the distributed template and fill in all required values:

```bash
cp .env.dist .env
```

You must set the following variables in .env:

- APP_SECRET
- POSTGRES_DB
- POSTGRES_USER
- POSTGRES_PASSWORD
- DATABASE_URL (must point to db-prod)
- JWT_PASSPHRASE

JWT values are not generated automatically.
JWT_PASSPHRASE must be explicitly defined by you.

2.2 Create .env.dev (development)

Create a minimal development environment file:

- env.dev must contain only:
- POSTGRES_DB
- POSTGRES_USER
- POSTGRES_PASSWORD
- DATABASE_URL (must point to db-dev)

No APP_SECRET or JWT_PASSPHRASE is required for development startup.

The PostgreSQL container uses the POSTGRES\_\* variables.
The Symfony application itself relies only on DATABASE_URL.

⸻

3. First start (development)

For the first run, build and start the dev environment:

```bash
docker compose --profile dev up --build
```

⸻

4. Install PHP dependencies (development)

In the development environment, dependencies are not installed automatically.

This is intentional:

- the source code is mounted as a volume
- rebuilding the image on every change is avoided

Run Composer once inside the container:

```bash
docker compose exec php-dev composer install
```

In production, dependencies are installed automatically during the Docker image build.

This installs the vendor/ directory and prepares the application.

⸻

5. Generate JWT keys (development)

Create the JWT directory if it does not exist:

```bash
mkdir -p config/jwt
```

Generate the key pair inside the running container:

```bash
docker compose exec php-dev php bin/console lexik:jwt:generate-keypair
```

This creates:

- config/jwt/private.pem
- config/jwt/public.pem

⸻

6. Run database migrations (development)

Initialize the database schema:

```bash
docker compose exec php-dev php bin/console doctrine:migrations:migrate
```

This step is required once per database.

⸻

7. Access the API

The API is available at:

```http
http://localhost:8080
```

⸻

8. Subsequent starts (development)

After the initial setup, start the dev environment with:

```bash
docker compose --profile dev up -d
```

⸻

9. Switching between dev and prod

Before switching profiles, always stop the current stack:

```bash
docker compose -p symfony-notes-api down
```

Containers are removed, but database volumes remain intact.

⸻

10. Production startup

Build and start the production stack:

```bash
docker compose --profile prod up --build
```

Run migrations once:

```bash
docker compose exec php php bin/console doctrine:migrations:migrate
```

Subsequent starts:

```bash
docker compose --profile prod up -d
```

⸻

11. Makefile usage

A Makefile is provided to standardize common workflows
(dev, prod, migrations, logs, database access, queries).

Refer to the Makefile itself for available commands and usage.

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
