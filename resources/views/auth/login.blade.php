<style>
    /* Authentication Layout Styles 
       Using a modern centered card layout with soft shadows.
    */
    body {
        background-color: #f1f5f9;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        display: flex; /* Centers the card vertically and horizontally */
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }

    .auth-card {
        background: white;
        padding: 2.5rem;
        border-radius: 1.25rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        width: 100%;
        max-width: 420px;
    }

    .auth-card h1 {
        color: #1e293b;
        font-weight: 800;
        font-size: 1.75rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }

    /* Alert components for session feedback (Logout messages, errors) */
    .alert {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.25rem;
        font-size: 0.875rem;
    }
    .alert-info { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
    .alert-error { background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* Form Styling */
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
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

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
    <h1>Login</h1>

    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" 
                   placeholder="mail@example.com" value="{{ old('email') }}" required autofocus>
            
            @error('email')
                <span style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" 
                   placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-auth">Sign In</button>
    </form>

    <div class="auth-footer">
        Need an account? <a href="{{ route('register') }}">Register here</a>
    </div>
</div>