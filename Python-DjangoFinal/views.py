from django.views.generic import ListView, DetailView, CreateView, UpdateView, DeleteView
from django.urls import reverse_lazy
from .models import School, Classroom, Student


class SchoolListView(ListView):
    model = School
    template_name = 'schools/school_list.html'
    context_object_name = 'schools'


class SchoolDetailView(DetailView):
    model = School
    template_name = 'schools/school_detail.html'
    context_object_name = 'school'


class SchoolCreateView(CreateView):
    model = School
    template_name = 'schools/school_form.html'
    fields = ['name', 'address', 'principal', 'founded']
    success_url = reverse_lazy('school-list')


class SchoolUpdateView(UpdateView):
    model = School
    template_name = 'schools/school_form.html'
    fields = ['name', 'address', 'principal', 'founded']
    success_url = reverse_lazy('school-list')


class SchoolDeleteView(DeleteView):
    model = School
    template_name = 'schools/school_confirm_delete.html'
    success_url = reverse_lazy('school-list')


class ClassroomDetailView(DetailView):
    model = Classroom
    template_name = 'schools/classroom_detail.html'
    context_object_name = 'classroom'


class ClassroomCreateView(CreateView):
    model = Classroom
    template_name = 'schools/classroom_form.html'
    fields = ['school', 'name', 'subject', 'teacher', 'room_number']

    def get_initial(self):
        initial = super().get_initial()
        if 'school_pk' in self.kwargs:
            initial['school'] = self.kwargs['school_pk']
        return initial

    def get_success_url(self):
        return reverse_lazy('school-detail', kwargs={'pk': self.object.school.pk})


class ClassroomUpdateView(UpdateView):
    model = Classroom
    template_name = 'schools/classroom_form.html'
    fields = ['school', 'name', 'subject', 'teacher', 'room_number']

    def get_success_url(self):
        return reverse_lazy('school-detail', kwargs={'pk': self.object.school.pk})


class ClassroomDeleteView(DeleteView):
    model = Classroom
    template_name = 'schools/classroom_confirm_delete.html'

    def get_success_url(self):
        return reverse_lazy('school-detail', kwargs={'pk': self.object.school.pk})


class StudentDetailView(DetailView):
    model = Student
    template_name = 'schools/student_detail.html'
    context_object_name = 'student'


class StudentCreateView(CreateView):
    model = Student
    template_name = 'schools/student_form.html'
    fields = ['classroom', 'first_name', 'last_name', 'student_id', 'email', 'enrolled']

    def get_initial(self):
        initial = super().get_initial()
        if 'classroom_pk' in self.kwargs:
            initial['classroom'] = self.kwargs['classroom_pk']
        return initial

    def get_success_url(self):
        return reverse_lazy('classroom-detail', kwargs={'pk': self.object.classroom.pk})


class StudentUpdateView(UpdateView):
    model = Student
    template_name = 'schools/student_form.html'
    fields = ['classroom', 'first_name', 'last_name', 'student_id', 'email', 'enrolled']

    def get_success_url(self):
        return reverse_lazy('classroom-detail', kwargs={'pk': self.object.classroom.pk})


class StudentDeleteView(DeleteView):
    model = Student
    template_name = 'schools/student_confirm_delete.html'

    def get_success_url(self):
        return reverse_lazy('classroom-detail', kwargs={'pk': self.object.classroom.pk})
