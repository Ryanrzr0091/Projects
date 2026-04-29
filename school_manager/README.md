# School Manager

A Django web application for managing a three-level school hierarchy: **School → Classroom → Student**.

## Live App

**[Your Platform.sh URL here]** — replace this after deployment.

## Project Structure

```
school_manager/          <- top-level Django config package
    __init__.py
    settings.py
    urls.py
    wsgi.py
schools/                 <- app package with all models, views, and templates
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
.platform.app.yaml
.platform/
    services.yaml
    routes.yaml
```

## Models

| Model | Key Fields |
|-------|-----------|
| School | name, address, principal, founded |
| Classroom | name, subject, teacher, room_number, FK→School |
| Student | first_name, last_name, student_id, email, enrolled, FK→Classroom |

## Local Setup

```bash
python -m venv venv
source venv/bin/activate        # Windows: venv\Scripts\activate
pip install -r requirements.txt
python manage.py migrate
python manage.py createsuperuser
python manage.py runserver
```

Visit `http://127.0.0.1:8000/` in your browser.

## Deploy to Platform.sh

Install the Platform.sh CLI:

```bash
curl -fsSL https://cli.platform.sh/installer | bash
```

Log in and deploy:

```bash
platform login
platform project:create --title "School Manager" --region us-3.platform.sh
git init
git add .
git commit -m "Initial commit"
platform push
```

After the first deploy, create a superuser on the live server:

```bash
platform ssh -- python manage.py createsuperuser
```

## GitHub

```bash
git remote add origin https://github.com/YOUR_USERNAME/school-manager.git
git push -u origin main
```
