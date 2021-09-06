from django.contrib.auth.models import User
from django.utils.timezone import now
from django.db import models


class Note(models.Model):
    user = models.ForeignKey(User, on_delete=models.DO_NOTHING)
    head = models.CharField(max_length=128)
    content = models.TextField()
    created = models.DateTimeField(default=now)
