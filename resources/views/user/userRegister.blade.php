@extends('layouts.app')

@section('content')

@include('components.loadingAction')

<script defer src="https://cdn.jsdelivr.net/npm/face-api.js"></script>

<div class="form_container">   
    {{-- Display Validation Errors 
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif--}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Registration Form --}}
    <form method="POST" action="{{ route('userRegister') }}" enctype="multipart/form-data" class="forms">
        @csrf
        <p>Student Registration</p>
        {{-- Name Field --}}
        <div class="form-group">
            <label for="name">Name:</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email Field --}}
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                name="email" value="{{ old('email') }}" required autocomplete="email">

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Image for verification --}}
        <div>
            <label for="images">Your beautiful Face:</label>
            @php
                $hasImage = session('images');
                $imageSrc = $hasImage ? 'data:image/jpeg;base64,' . session('images') : '';
            @endphp

            <div class="mb-2">
                <img id="previewImage"
                    src="{{ $imageSrc }}"
                    alt="Preview"
                    class="img-preview"
                    style="width:100px; height:150px; @if (!$hasImage) display: none; @endif">
            </div>
            <input type="file" name="images" id="images" accept="image/*" class="form-control" required>

            @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password Field --}}
        <div class="form-group">
            <label for="password">Password:</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                name="password" required autocomplete="new-password">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm Password Field --}}
        <div class="form-group">
            <label for="password-confirm">Confirm Password:</label>
            <input id="password-confirm" type="password" class="form-control" 
                name="password_confirmation" required autocomplete="new-password">
        </div>

        {{-- Alert message --}}
        <div class="alert alert-warning">
            <p><strong>Alert!</strong></p>
            <p>**The image is to let administrator to know who you are, please make sure your face is clearly viewed</p>
            <p>**Your application will be submitted to our administrator for approval.</p> 
            <p>**Only approved students are able to log into our system.</p>

            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="agreeCheckbox" name="agreeCheckbox" required>
                <label class="form-check-label" for="agreeCheckbox">
                    I understand and agree to the terms above.
                </label>
            </div>

            @error('agreeCheckbox')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Submit & Cancel Buttons --}}
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<script>
    const emailInput = document.getElementById('email');
    const feedback = document.getElementById('email-feedback');

    emailInput.addEventListener('input', function () {
        if (emailInput.value.trim() !== '') {
            feedback.style.display = 'block';
        } else {
            feedback.style.display = 'none';
        }
    });

    async function loadModels() {
        await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
    }

    document.getElementById('images').addEventListener('change', async function (event) {
        const input = event.target;
        const preview = document.getElementById('previewImage');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = async function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block';

                // Wait for image to load in DOM
                preview.onload = async () => {
                    const detection = await faceapi.detectSingleFace(preview, new faceapi.TinyFaceDetectorOptions());

                    if (!detection) {
                        alert('No face detected. Please upload a clear image of your face.');
                        preview.src = '';
                        input.value = '';
                        preview.style.display = 'none';
                    }
                };
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    loadModels();
</script>
@endsection
