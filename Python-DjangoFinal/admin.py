from django.contrib import admin
from .models import School, Classroom, Student


class ClassroomInline(admin.TabularInline):
    model = Classroom
    extra = 1


class StudentInline(admin.TabularInline):
    model = Student
    extra = 1


@admin.register(School)
class SchoolAdmin(admin.ModelAdmin):
    list_display = ['name', 'principal', 'founded']
    inlines = [ClassroomInline]


@admin.register(Classroom)
class ClassroomAdmin(admin.ModelAdmin):
    list_display = ['name', 'subject', 'teacher', 'school', 'room_number']
    list_filter = ['school']
    inlines = [StudentInline]


@admin.register(Student)
class StudentAdmin(admin.ModelAdmin):
    list_display = ['last_name', 'first_name', 'student_id', 'classroom', 'enrolled']
    list_filter = ['classroom__school', 'classroom']
    search_fields = ['first_name', 'last_name', 'student_id']
