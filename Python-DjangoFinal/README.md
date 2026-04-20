# School Manager

A Django web application for managing a three-level school hierarchy: **School → Classroom → Student**.

## Live App

**[Your Render URL here]** — replace this with your Render URL after deployment (e.g. `https://school-manager-xxxx.onrender.com`)

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

## Project Structure

```
school_manager/          ← top-level Django config package
    __init__.py
    settings.py
    urls.py
    wsgi.py
schools/                 ← app package with all models, views, and templates
    __init__.py
    admin.py
    apps.py
    models.py
    urls.py
    views.py
    migrations/
    templates/schools/
manage.py
requirements.txt
build.sh
```

## Deploy to Render (free)

1. Push this repo to GitHub
2. Sign up at [render.com](https://render.com) with your GitHub account
3. Click **New → Web Service** and connect this repository
4. Set **Build command** to `./build.sh` and **Start command** to `gunicorn school_manager.wsgi:application`
5. Add environment variables: `SECRET_KEY` (any long random string), `DEBUG` = `False`
6. Add a free **PostgreSQL** database from Render and set its URL as `DATABASE_URL` in your env vars
7. Click **Deploy** — Render gives you a permanent URL to put in your README

After first deploy, open the Render **Shell** tab and run:

```bash
python manage.py createsuperuser
```

Every `git push` to main triggers an automatic redeploy.

## Upload to GitHub

```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
git push -u origin main
```

Make the repo **public**, or go to Settings → Collaborators to invite your instructor and grader.
