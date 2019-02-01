from django.db import models

class Certificate_Form(models.Model):
    name = models.CharField(max_length=120)
    email = models.EmailField()
    course_details = models.CharField(max_length=196)
    
    # def __init__(self,name,email,course_details):
    #     self.name = name
    #     self.email = email
    #     self.course_details = course_details

    def __str__(self):
        return self.name
