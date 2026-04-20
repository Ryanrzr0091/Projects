# School Manager

A Django web application for managing a three-level school hierarchy: **School → Classroom → Student**.

## Live App

**[Link to running server here]** — replace this with your Platform.sh URL after deployment.

## Features

- Full CRUD for Schools, Classrooms, and Students
- Breadcrumb navigation across all three levels
- Django admin interface at `/admin/`
- Cascading deletes (deleting a school removes its classrooms and students)

## Models

| Model | Key Fields |
|-------|-----------|
| School | name, address, principal, founded |
| Classroom | name, subject, teacher, room_number, FK→School |
| Student | first_name, last_name, student_id, email, enrolled, FK→Classroom |
