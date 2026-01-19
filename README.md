# Symfony Notes API

A RESTful API built with Symfony and Doctrine for managing notes.

This project is a backend learning playground focused on:

-   clean architecture
-   separation of concerns (Controller / Service / DTO)
-   validation
-   pagination, sorting and filtering
-   proper error handling
-   RESTful API design

The codebase is intentionally structured to reflect real-world backend practices.

## Features

-   Create notes
-   Read a single note by ID
-   List notes with pagination
-   Sorting (id, title, createdAt)
-   Filtering by title (search)
-   Partial updates using PATCH
-   Delete notes
-   Input validation with meaningful HTTP status codes
-   Business logic isolated in services

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

-   page (int, optional)
-   limit (int, optional)
-   sortBy (id | title | createdAt)
-   order (ASC | DESC)
-   search (optional, filters by title)

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

## HTTP Semantics

This API follows common REST conventions:

-   200 OK for successful reads and updates
-   201 Created for resource creation
-   400 Bad Request for malformed input
-   404 Not Found when a resource does not exist
-   422 Unprocessable Entity for validation errors

## Architecture Overview

-   Controllers handle HTTP concerns only (request/response)
-   DTOs define the API contract and input structure
-   Validation is handled via Symfony Validator
-   Services contain all business logic
-   Entities are never exposed directly to the API

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

-   PHP
-   Symfony
-   Doctrine ORM
-   PostgreSQL

## Project Status

-   Create note: done
-   Read single note: done
-   List notes & pagination: done
-   Sorting: done
-   Filtering: done
-   Update (PATCH): done
-   Delete: done
-   Response DTOs: pending
-   Authentication & authorization: pending

## Purpose

This repository exists as a learning project to build a solid understanding of
backend development with Symfony.

The focus is on correctness, clarity, and long-term maintainability rather than
shortcuts or framework magic.

## Notes

This repository is actively evolving.  
API documentation, internal structure, and supporting files may change as new features are added and the architecture is refined.
