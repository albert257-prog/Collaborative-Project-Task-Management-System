@if(session('success') || session('error'))
    <div id="flash-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
        @if(session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif
    </div>

    <style>
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
        }
        .alert-success { background: #2ecc71; border-left: 5px solid #27ae60; }
        .alert-error { background: #e74c3c; border-left: 5px solid #c0392b; }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .fade-out { opacity: 0; transition: opacity 0.5s ease; }
    </style>

    <script>
        setTimeout(() => {
            const container = document.getElementById('flash-container');
            if (container) {
                container.classList.add('fade-out');
                setTimeout(() => container.remove(), 500);
            }
        }, 5000);
    </script>
@endif