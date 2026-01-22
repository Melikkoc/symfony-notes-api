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

## Running the project

1. Install dependencies:

```bash
composer install
```

2. Configure environment variables:

```bash
cp .env .env.local
```

3. Run migrations:

```bash
php bin/console doctrine:migrations:migrate
```

4. Start the Symfony development server:

```bash
symfony server:start
```

5. The API will be available at:

```http
http://localhost:8000
```

## Tech Stack

- PHP
- Symfony
- Doctrine ORM
- PostgreSQL

## Project Status

- Create note: done
- Read single note: done
- List notes & pagination: done
- Sorting: done
- Filtering: done
- Update (PATCH): done
- Delete: done
- Response DTOs: done
- Authentication (JWT): done
- Ownership enforcement: done
- Authorization basics: done

## Purpose

This repository exists as a learning project to build a solid understanding of
backend development with Symfony.

The focus is on correctness, clarity, and long-term maintainability rather than
shortcuts or framework magic.

## Security Notes

- Stateless JWT authentication
- No Doctrine entities exposed through the API
- Ownership checks implemented at service level
- Consistent error responses to avoid information leakage

## Notes

This repository is actively evolving.  
API documentation, internal structure, and supporting files may change as new features are added and the architecture is refined.
