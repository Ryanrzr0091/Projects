from django.db import models


class School(models.Model):
    name = models.CharField(max_length=200)
    address = models.TextField()
    principal = models.CharField(max_length=100)
    founded = models.IntegerField()

    def __str__(self):
        return self.name

    class Meta:
        ordering = ['name']


class Classroom(models.Model):
    school = models.ForeignKey(School, on_delete=models.CASCADE, related_name='classrooms')
    name = models.CharField(max_length=100)
    subject = models.CharField(max_length=100)
    teacher = models.CharField(max_length=100)
    room_number = models.CharField(max_length=10)

    def __str__(self):
        return f"{self.name} — {self.school.name}"

    class Meta:
        ordering = ['school', 'name']


class Student(models.Model):
    classroom = models.ForeignKey(Classroom, on_delete=models.CASCADE, related_name='students')
    first_name = models.CharField(max_length=100)
    last_name = models.CharField(max_length=100)
    student_id = models.CharField(max_length=20, unique=True)
    email = models.EmailField(blank=True)
    enrolled = models.DateField()

    def __str__(self):
        return f"{self.first_name} {self.last_name}"

    class Meta:
        ordering = ['last_name', 'first_name']
