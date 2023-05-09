from django.views.decorators.http import require_POST, require_GET
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.shortcuts import render, redirect
from django.contrib.auth.models import User
from django.http import HttpResponse
from django.contrib import messages
from django.urls import reverse

from .models import Note

import json


@require_GET
def index(request):
    # Is the current request not authenticated (user not logged in)
    if not request.user.is_authenticated:
        # Render the login / registration page
        return render(request, 'login.html')
    # Get all Notes from the Database
    notes = Note.objects.all().order_by('-created')[:3]
    # Render those Notes into the content.html template
    return render(request, 'content.html', context={'notes': notes})


@require_GET
@login_required
def about(request):
    # Render about page
    return render(request, 'about.html')


@require_GET
@login_required
def logout_view(request):
    # Logout current user (invalidate cookie)
    logout(request)
    # Redirect to the start page (login)
    return redirect(reverse('index'))


@require_POST
def registration_view(request):
    # Try to parse POST data as JSON
    try:
        data = json.loads(request.body)
    except json.JSONDecodeError as e:
        messages.error(request, f'Received Malformed JSON: {str(e)}')
        return HttpResponse('', status=500)
    # Check parameters from JSON
    name = data['username']
    password = data['password']
    if name is None or password is None:
        messages.warning(request, 'Missing Parameters!')
        return HttpResponse('', status=400)
    # Try to create user (if fails it return None)
    user = User.objects.create_user(username=name, password=password)
    # If the user was successfully created ...
    if user is not None:
        # Save created user to the Database
        user.save()
        # Authenticate and login the freshly registered user
        user = authenticate(request=request, username=name, password=password)
        if user is not None and user.is_active:
            login(request, user)
        return HttpResponse('', status=200)
    else:
        messages.warning(request, 'Name is already chosen!')
        return HttpResponse('', status=400)


@require_POST
def login_view(request):
    # Try to parse POST data as JSON
    try:
        data = json.loads(request.body)
    except json.JSONDecodeError as e:
        messages.error(request, f'Received Malformed JSON: {str(e)}')
        return HttpResponse('', status=500)
    # Check parameters from JSON
    name = data['username']
    password = data['password']
    if name is None or password is None:
        messages.warning(request, 'Missing Parameters!')
        return HttpResponse('', status=400)
    # Try to authenticate user with credentials (None if fails)
    user = authenticate(request=request, username=name, password=password)
    # If the user was successfully authenticated ...
    if user is not None and user.is_active:
        # Login the user (create cookie session)
        login(request, user)
        return HttpResponse('', status=200)
    messages.warning(request, 'Username/Password mismatch')
    return HttpResponse('', status=400)


@require_POST
@login_required
def save_note(request):
    # Try to parse POST data as JSON
    try:
        data = json.loads(request.body)
    except json.JSONDecodeError as e:
        messages.error(request, f'Received Malformed JSON: {str(e)}')
        return HttpResponse('', status=500)
    # Check parameters from JSON
    head = data['head']
    content = data['content']
    if None in [head, content]:
        messages.warning(request, 'Missing Parameters!')
        return HttpResponse('', status=400)
    # Create and save Note to the Database
    Note(user=request.user, head=head, content=content).save()
    return HttpResponse('', status=200)
