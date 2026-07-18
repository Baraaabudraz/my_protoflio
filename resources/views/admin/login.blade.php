<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CMS Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#080c14;color:#e0e6f0;min-height:100vh;display:flex;align-items:center;justify-content:center;}
        .login-box{width:100%;max-width:400px;padding:1.5rem;}
        .login-logo{text-align:center;margin-bottom:2rem;}
        .login-logo i{font-size:2.5rem;color:#00d4d4;margin-bottom:0.75rem;display:block;}
        .login-logo h1{font-size:1.5rem;font-weight:800;}
        .login-logo p{font-size:0.85rem;color:#5a6a84;margin-top:0.25rem;}
        .card{background:#0d1425;border:1px solid #1a2540;border-radius:16px;padding:2rem;}
        .form-group{margin-bottom:1.25rem;}
        .form-label{display:block;font-size:0.78rem;font-weight:600;color:#5a6a84;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:0.5rem;}
        .form-control{width:100%;padding:0.75rem 1rem;background:#080c14;border:1px solid #1a2540;border-radius:8px;color:#e0e6f0;font-family:'Inter',sans-serif;font-size:0.9rem;outline:none;transition:border-color .2s;}
        .form-control:focus{border-color:#00d4d4;box-shadow:0 0 0 3px rgba(0,212,212,0.08);}
        .btn-login{width:100%;padding:0.875rem;background:linear-gradient(135deg,#00d4d4,#0088cc);border:none;border-radius:8px;color:#080c14;font-weight:700;font-size:0.95rem;cursor:pointer;font-family:'Inter',sans-serif;transition:all .3s;box-shadow:0 0 25px rgba(0,212,212,0.25);}
        .btn-login:hover{transform:translateY(-1px);box-shadow:0 0 40px rgba(0,212,212,0.4);}
        .alert{padding:0.75rem 1rem;border-radius:8px;margin-bottom:1rem;font-size:0.85rem;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;}
        .hint{text-align:center;margin-top:1rem;font-size:0.78rem;color:#5a6a84;}
        .back-link{display:block;text-align:center;margin-top:1.25rem;font-size:0.82rem;color:#5a6a84;text-decoration:none;}
        .back-link:hover{color:#00d4d4;}
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-logo">
        <i class="fas fa-shield-halved"></i>
        <h1>Portfolio CMS</h1>
        <p>Admin access only</p>
    </div>
    <div class="card">
        @if(session('error'))
            <div class="alert"><i class="fas fa-circle-exclamation"></i> {{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Admin Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password" autofocus required>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-right-to-bracket"></i> &nbsp;Sign In</button>
        </form>
        <p class="hint">Default password: <code style="color:#00d4d4">admin123</code></p>
    </div>
    <a href="{{ url('/') }}" class="back-link"><i class="fas fa-arrow-left"></i> Back to portfolio</a>
</div>
</body>
</html>
