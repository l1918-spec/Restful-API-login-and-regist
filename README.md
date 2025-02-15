# PHP Authentication API with JWT

A simple JWT-based authentication API built with PHP and MySQL. It allows users to log in securely, generates a JWT token, and has been tested using Postman.

## Features

- User login with JWT token generation
- Password hashing and validation
- MySQL database connection using PDO
- Cross-Origin Resource Sharing (CORS) enabled

## Prerequisites

- PHP 7.4 or higher
- MySQL Server
- Postman for testing

## Installation

### Clone the Repository
```bash
git clone https://github.com/your-username/php-auth-jwt.git
cd php-auth-jwt
```

### Set Up the Database
```sql
CREATE DATABASE php_auth_api;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
);
```

### Insert a Test User
```sql
INSERT INTO users (name, email, password)
VALUES ('apiuser', 'apiuser@gmail.com', '<hashed_password>');
```

### Generate a Hashed Password in PHP
```php
echo password_hash('apiuser@1234', PASSWORD_BCRYPT);
```

### Configure the Database in `db.php`
```php
private $db_host = 'localhost';
private $db_name = 'php_auth_api';
private $db_username = 'root';
private $db_password = '';
```

### Start the PHP Server
```bash
php -S localhost:8000
```

## API Endpoint

### Login User
- **URL:** `/login.php`
- **Method:** `POST`
- **Request Body (JSON):**
```json
{
  "email": "apiuser@gmail.com",
  "password": "apiuser@1234"
}
```
- **Response:**
```json
{
  "success": 1,
  "message": "You have successfully logged in",
  "token": "your_jwt_token_here"
}
```

## Testing with Postman

1. Open Postman and create a `POST` request to `http://localhost:8000/login.php`.
2. Set the headers:
   - `Content-Type: application/json`
3. Add the JSON body:
```json
{
  "email": "apiuser@gmail.com",
  "password": "apiuser@1234"
}
```
4. Send the request and check the response for the token.

## Contact

Feel free to reach out if you have any questions:

**Email:** lydiacharif02@gmail.com


