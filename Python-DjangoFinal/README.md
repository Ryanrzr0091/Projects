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

## Local Setup

```bash
python -m venv venv
source venv/bin/activate       # Windows: venv\Scripts\activate
pip install -r requirements.txt
python manage.py migrate
python manage.py createsuperuser
python manage.py runserver
```

Visit `http://127.0.0.1:8000/` in your browser.

## Deploy to Platform.sh

```bash
# Install the CLI
curl -fsSL https://cli.platform.sh/installer | bash

# Login and create project
platform login
platform project:create --title "School Manager" --region us-3.platform.sh

# Push
git init
git add .
git commit -m "Initial commit"
platform push
```

After deployment, create a superuser on the live server:

```bash
platform ssh -- python manage.py createsuperuser
```
