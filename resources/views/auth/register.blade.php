<style>
    /* Global Page Layout */
    body {
        background-color: #f1f5f9;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }

    /* Centered Registration Card */
    .auth-card {
        background: white;
        padding: 2.5rem;
        border-radius: 1.25rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 420px;
        margin: 1rem; 
    }

    .auth-card h1 {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
    }

    /* Feedback Alerts (Success/Info) */
    .alert {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .alert-success { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-info { background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }

    /* Form Layout & Groups */
    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
    }

    /* Standard & Error State Inputs */
    .form-group input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.625rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        outline: none;
        box-sizing: border-box;
    }

    .form-group input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-group input.is-invalid {
        border-color: #ef4444;
    }

    /* Validation Error Text */
    .error-msg {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* Primary Action Button */
    .btn-auth {
        width: 100%;
        background-color: #2563eb;
        color: white;
        font-weight: 700;
        padding: 0.875rem;
        border: none;
        border-radius: 0.625rem;
        cursor: pointer;
        transition: background-color 0.2s;
        font-size: 1rem;
        margin-top: 0.5rem;
    }

    .btn-auth:hover { background-color: #1d4ed8; }

    .auth-footer {
        margin-top: 1.5rem;
        text-align: center;
        font-size: 0.875rem;
        color: #64748b;
    }

    .auth-footer a { color: #2563eb; text-decoration: none; font-weight: 600; }
    .auth-footer a:hover { text-decoration: underline; }
</style>

<div class="auth-card">
    {{-- Display Success or Info messages from the Session --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <h1>Register Account</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" placeholder="John Doe" 
                   value="{{ old('name') }}" class="@error('name') is-invalid @enderror" required>
            @error('name') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="mail@example.com" 
                   value="{{ old('email') }}" class="@error('email') is-invalid @enderror" required>
            @error('email') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="••••••••" 
                   class="@error('password') is-invalid @enderror" required>
            @error('password') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                   placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-auth">Create Account</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Login here</a>
    </div>
</div>