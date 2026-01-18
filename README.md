# Symfony Notes API

Simple REST API built with Symfony & Doctrine to manage notes.

## Endpoints

### Create note
POST /api/note

### Get note by id
GET /api/note/{id}

### List notes
GET /api/note?page=1&limit=10&sortBy=createdAt&order=DESC&search=note
