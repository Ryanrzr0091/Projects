from django.urls import path
from . import views

urlpatterns = [
    path('', views.SchoolListView.as_view(), name='school-list'),

    path('schools/new/', views.SchoolCreateView.as_view(), name='school-create'),
    path('schools/<int:pk>/', views.SchoolDetailView.as_view(), name='school-detail'),
    path('schools/<int:pk>/edit/', views.SchoolUpdateView.as_view(), name='school-update'),
    path('schools/<int:pk>/delete/', views.SchoolDeleteView.as_view(), name='school-delete'),

    path('schools/<int:school_pk>/classrooms/new/', views.ClassroomCreateView.as_view(), name='classroom-create'),
    path('classrooms/<int:pk>/', views.ClassroomDetailView.as_view(), name='classroom-detail'),
    path('classrooms/<int:pk>/edit/', views.ClassroomUpdateView.as_view(), name='classroom-update'),
    path('classrooms/<int:pk>/delete/', views.ClassroomDeleteView.as_view(), name='classroom-delete'),

    path('classrooms/<int:classroom_pk>/students/new/', views.StudentCreateView.as_view(), name='student-create'),
    path('students/<int:pk>/', views.StudentDetailView.as_view(), name='student-detail'),
    path('students/<int:pk>/edit/', views.StudentUpdateView.as_view(), name='student-update'),
    path('students/<int:pk>/delete/', views.StudentDeleteView.as_view(), name='student-delete'),
]
