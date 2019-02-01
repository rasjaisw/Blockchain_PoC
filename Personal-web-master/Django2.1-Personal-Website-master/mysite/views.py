import json
import datetime
import requests
from django.shortcuts import render
from django.http import HttpResponse
from django.views.generic import View
from .models import Certificate_Form

from .utils import render_to_pdf

def Thanks_page(request):
    return render(request,'mysite/thanks.html')

def Generatepdf(request,data):
    pdf = render_to_pdf('mysite/invoice.html', data)
    return HttpResponse(pdf, content_type='application/pdf')

def converter(Jdata):
    if isinstance(Jdata, datetime.datetime):
        return Jdata.__str__()
 
def Certificate_form(request):
    if request.method == 'POST':
        name_form = request.POST.get('name')
        email_form = request.POST.get('email')
        course_details_form = request.POST.get('course_details')
        Form_data = Certificate_Form(name=name_form,email=email_form,course_details=course_details_form)

        data = {
             'today': datetime.datetime.now(), 
             'name': name_form,
             'email': email_form,
             'course': course_details_form,
        }
        print(json.dumps(data, default = converter))
        # Generatepdf(request,data)
        pdf = render_to_pdf('mysite/invoice.html', data)    
        # return render(request, 'mysite/Certificate_form.html')
        return HttpResponse(pdf, content_type='application/pdf')     
    else:
        return render(request, 'mysite/Certificate_form.html')
