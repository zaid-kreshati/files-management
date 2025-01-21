# File Management System

## Overview
The File Management System is a powerful application built using the Laravel framework with a three-tier architecture. This structure separates the repository, service, and controller layers for clear and maintainable code. The application enables users to manage files, collaborate in groups, and perform advanced file operations such as version comparison and backup management.

## Features

### File Management
- **File Upload/Download**: Seamlessly upload and download files.
- **Check-in and Editing**: Check files in and make changes with version tracking.
- **File Backup**: Create backups to ensure data safety.
- **Version Comparison**: Compare different versions of files to track changes.

### Group Collaboration
- **Group Creation**: Users can create groups and invite others to join.
- **Shared Ownership**: Collaborate on files within groups with shared access.
- **Access Control**: Define permissions for group members.

## Architecture

### Three-Tier Structure
1. **Repository Layer**: Handles direct interactions with the database.
2. **Service Layer**: Contains business logic and processes user requests.
3. **Controller Layer**: Manages API endpoints and user interactions.

## Technologies Used

### Backend
- **Laravel Framework**: For backend logic and API development.
- **MySQL**: Database management for structured data storage.

### Development Tools
- **Postman**: For API testing and documentation.
- **Git**: Version control for managing codebase.


## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/yourusername/file-management-system.git
    cd file-management-system
    ```

2. Install dependencies:
    ```bash
    composer install
    npm install
    ```

3. Configure environment:
    - Copy `.env.example` to `.env`:
      ```bash
      cp .env.example .env
      ```
    - Update database and notification service credentials in `.env` file.

4. Run database migrations:
    ```bash
    php artisan migrate
    ```

5. Generate application key:
    ```bash
    php artisan key:generate
    ```

6. Start the development server:
    ```bash
    php artisan serve
    ```

## Contributing

We welcome contributions! If you'd like to contribute to this project:

1. Fork the repository.
2. Create a feature branch:
    ```bash
    git checkout -b feature-name
    ```
3. Commit your changes:
    ```bash
    git commit -m "Add feature-name"
    ```
4. Push the branch:
    ```bash
    git push origin feature-name
    ```
5. Open a pull request.



