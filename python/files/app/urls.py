from django.urls import path

from . import views

urlpatterns = [
    path('', views.index, name='index'),
    path('login', views.login_view, name='login'),
    path('logout', views.logout_view, name='logout'),
    path('register', views.registration_view, name='register'),
    path('create', views.save_note, name='new_note'),
    path('about', views.about, name='about'),
]
