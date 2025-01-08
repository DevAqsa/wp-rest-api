# WordPress Student REST API Plugin

A custom WordPress plugin that provides REST API endpoints for managing student data. The plugin creates a custom table to store student information and offers CRUD operations through REST API endpoints.

## Features

- Creates a custom `wp_students` table in WordPress database
- Provides REST API endpoints for:
  - Getting all students
  - Creating a new student
  - Updating student information
  - Deleting a student
- Automatic table creation on plugin activation
- Clean database cleanup on plugin deactivation

## Installation

1. Download the plugin files
2. Upload the plugin folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

## API Endpoints

All endpoints are prefixed with `wp-json/students/v1`

### Get All Students
- **Endpoint:** `/student`
- **Method:** GET
- **Response:** List of all students

### Create Student
- **Endpoint:** `/student`
- **Method:** POST
- **Required Parameters:**
  - name (string)
  - email (string)
- **Optional Parameters:**
  - phone_no (string)

### Update Student
- **Endpoint:** `/student/{id}`
- **Method:** PUT
- **Parameters:**
  - name (string)
  - email (string)
  - phone_no (string)
- **Note:** All parameters are optional for updates

### Delete Student
- **Endpoint:** `/student/{id}`
- **Method:** DELETE

## Database Structure

The plugin creates a table with the following structure:

```sql
CREATE TABLE `wp_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(80) NOT NULL,
  `phone_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
)
```

## Example API Responses

### Successful Response
```json
{
    "status": true,
    "message": "Success message",
    "data": []  // For GET requests
}
```

### Error Response
```json
{
    "status": false,
    "message": "Error message"
}
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher

## Author

Aqsa Mumtaz

## License

This project is licensed under the GPL v2 or later