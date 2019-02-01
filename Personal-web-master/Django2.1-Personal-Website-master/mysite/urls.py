from django.urls import path

from . import views

urlpatterns = [
    path('', views.Certificate_form, name='Certificate_form'),
    path('thanks', views.Thanks_page, name='Thanks_page'),
]
