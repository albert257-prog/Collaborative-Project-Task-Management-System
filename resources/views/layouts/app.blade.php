<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background-color: #f4f7f6; color: #333; }
        
        /* Navigation Bar Styling */
        nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 12px 40px; 
            background-color: #ffffff; 
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        /* Profile/User Name Styling */
        .user-profile {
            font-weight: 600;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-profile::before {
            content: "👤"; /* Simple profile icon */
            font-size: 1.1em;
        }

        /* Modern Logout Button */
        .logout-btn { 
            background: #fff; 
            border: 1px solid #e2e8f0; 
            padding: 6px 16px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 0.9em;
            font-weight: 500;
            color: #4a5568;
            transition: all 0.2s ease;
        }
        
        .logout-btn:hover { 
            background: #fff5f5; 
            color: #c53030; 
            border-color: #feb2b2;
        }

        .container { padding: 20px; }
    </style>
</head>
<body>
    {{-- Flash Messages --}}
    @include('components.flash-messages')

    {{-- Navigation Bar --}}
    <nav>
        {{-- Left Side: Logout --}}
        <div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

        {{-- Right Side: User Profile --}}
        <div class="user-profile">
            {{ auth()->user()->name }}
        </div>
    </nav>

    {{-- Main Content YIELD --}}
    <main class="container">
        @yield('content')
    </main>
</body>
</html>