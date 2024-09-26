# Library Management System

This is a simple RESTful API for a Library Management System built with Laravel 10. The application allows for managing authors and books, supporting CRUD operations and testing unit.

## Table of Contents
- [Installation](#installation)
- [Running the Application](#running-the-application)
- [Unit Tests](#unit-tests)
- [Design Choices](#design-choices)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/riz081/libraryManagement.git
   cd libraryManagement
   ```
   
2. Install dependencies:
   composer install

3. Set up the environment file:
   cp .env.example .env
   php artisan key:generate
   
5. Run migrations:
   php artisan migrate

7. Run service:
   php artisan serve

9. Run Unit Testing:
    php artisan test

    
### Write-up (One Page)
You can create a document titled `design_and_performance_tuning.md` that includes:

**Design Choices:**
- **MVC Architecture**: Followed the MVC pattern for separation of concerns, ensuring that the application is maintainable and scalable.
- **Eloquent ORM**: Utilized Eloquent for interacting with the database, allowing for an expressive syntax and simplified queries.
- **RESTful API**: Designed the API endpoints to follow REST conventions, promoting a stateless and resource-oriented architecture.

**Performance Tuning Techniques:**
1. **Query Optimization**: Employed eager loading to reduce the number of database queries, fetching related data in a single query.
2. **Caching**: Used Laravel’s built-in caching mechanisms to store frequently accessed data (like lists of books and authors) in memory, significantly reducing load times for repeated requests.
3. **Unit Tests/Using Pest**: Wrote unit tests to ensure the correctness of the application, which also helps identify performance issues during development.

