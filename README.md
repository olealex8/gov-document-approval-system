# Government Document Approval System

Full-stack system for managing government document permit applications, built with Laravel and Vue 3.

## Structure

- [`/backend`](./backend) — Laravel 11 REST API (PostgreSQL, Sanctum, Spatie Permission)
- [`/frontend`](./frontend) — Vue 3 SPA (Vite, Pinia, Chart.js)

## Getting Started

Setup instructions for each part live in their respective folders:
- [Backend setup](./backend/README.md)
- [Frontend setup](./frontend/README.md)

Run the backend first, then the frontend — the frontend expects the API to be available at the URL configured in its `.env`.

## Key Features

- Token-based authentication (Sanctum) with role-based access (pemohon / penguji)
- Full document approval workflow with audit history
- Dashboard with cached statistics and Chart.js visualizations
- Excel export, file upload validation, PostgreSQL performance indexing
