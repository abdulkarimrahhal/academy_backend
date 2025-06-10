# README

## Installation

1. Clone the repository:
```
git clone https://github.com/your-username/your-project.git
```
2. Install the dependencies:
```
composer install
```
3. Set up the environment:
```
cp .env.example .env
php artisan key:generate
```
4. Configure the database connection in the `.env` file.
5. Run the migrations:
```
php artisan migrate
```
6. (Optional) Seed the database:
```
php artisan db:seed
```
7. Start the development server:
```
php artisan serve
```

## Usage

The API provides the following endpoints:

### Authentication
- `POST /api/register`: Register a new user.
- `POST /api/login`: Log in a user.
- `POST /api/logout`: Log out a user.

### Students
- `GET /api/student/show/{id}`: Get a student's profile.
- `POST /api/student/create`: Create a new student.
- `PUT /api/student/update/{id}`: Update a student's profile.
- `DELETE /api/student/delete/{id}`: Soft delete a student.
- `GET /api/student/course/{id}`: Get a student's enrolled courses.
- `POST /api/enrolled-courses`: Get a student's enrolled courses.
- `POST /api/enroll-student`: Enroll a student in a course.
- `POST /api/withdraw-student`: Withdraw a student from a course.

### Instructors
- `GET /api/instructor/show/{id}`: Get an instructor's profile.
- `POST /api/instructor/create`: Create a new instructor.
- `PUT /api/instructor/update/{id}`: Update an instructor's profile.
- `DELETE /api/instructor/delete/{id}`: Soft delete an instructor.
- `GET /api/instructor/courses`: Get all courses.
- `GET /api/instructor/show/course/{id}`: Get a course's details.
- `POST /api/instructor/create/course`: Create a new course.
- `PUT /api/instructor/update/course/{id}`: Update a course.
- `DELETE /api/instructor/delete/course/{id}`: Soft delete a course.
- `DELETE /api/instructor/forcedelete/course/{id}`: Permanently delete a course.
- `POST /api/instructor/retrive/course/{id}`: Restore a deleted course.

### Admins
- `GET /api/admin/show/users`: Get all users.
- `GET /api/admin/show/trashed/users`: Get all trashed users.
- `GET /api/admin/show/admins`: Get all admins.
- `POST /api/admin/create/admin`: Create a new admin.
- `DELETE /api/admin/delete/admin/{id}`: Delete an admin.
- `GET /api/admin/create/courses`: Create a new course.
- `GET /api/admin/show/trashed/courses`: Get all trashed courses.
- `GET /api/admin/show/students`: Get all students.
- `GET /api/admin/show/trashed/students`: Get all trashed students.
- `POST /api/admin/create/student`: Create a new student.
- `PUT /api/admin/update/student/{id}`: Update a student's profile.
- `GET /api/admin/show/student/{id}`: Get a student's profile.
- `DELETE /api/admin/delete/student/{id}`: Soft delete a student.
- `DELETE /api/admin/forcedelete/student/{id}`: Permanently delete a student.
- `POST /api/admin/retrieve/student/{id}`: Restore a deleted student.
- `GET /api/admin/show/trashed/instructors`: Get all trashed instructors.
- `GET /api/admin/show/instructors`: Get all instructors.
- `POST /api/admin/create/instructor`: Create a new instructor.
- `PUT /api/admin/update/instructor/{id}`: Update an instructor's profile.
- `GET /api/admin/show/instructor/{id}`: Get an instructor's profile.
- `DELETE /api/admin/delete/instructor/{id}`: Soft delete an instructor.
- `DELETE /api/admin/forcedelete/instructor/{id}`: Permanently delete an instructor.
- `POST /api/admin/retrieve/instructor/{id}`: Restore a deleted instructor.

## API

The API uses the following response format:

```json
{
    "success": true,
    "data": {},
    "message": "Success message"
}
```

In case of an error, the response will be:

```json
{
    "success": false,
    "data": {},
    "message": "Error message"
}
```

## Contributing

To contribute to this project, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them.
4. Push your changes to your forked repository.
5. Create a pull request to the original repository.

## License

This project is licensed under the [MIT License](LICENSE).

## Testing

To run the tests, execute the following command:

```
php artisan test
```

This will run the available tests for the project.