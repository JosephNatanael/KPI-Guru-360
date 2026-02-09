<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sekolah Kristen Permata Hati Manado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4ade80;
            --error-color: #f87171;
            --gradient-primary: linear-gradient(135deg, #4361ee, #3a0ca3);
            --gradient-secondary: linear-gradient(135deg, #4cc9f0, #4361ee);
            --shadow-sm: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        body {
            background-color: #f0f4f8;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(67, 97, 238, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(76, 201, 240, 0.05) 0%, transparent 20%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Background decorative elements */
        .bg-element {
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            opacity: 0.1;
        }
        
        .bg-1 {
            width: 300px;
            height: 300px;
            background: var(--gradient-primary);
            top: -150px;
            right: -150px;
        }
        
        .bg-2 {
            width: 200px;
            height: 200px;
            background: var(--gradient-secondary);
            bottom: -100px;
            left: -100px;
        }
        
        .bg-3 {
            width: 150px;
            height: 150px;
            background: #3a0ca3;
            top: 50%;
            left: 10%;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-card {
            background-color: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            border: none;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .login-header {
            background: var(--gradient-primary);
            color: white;
            padding: 25px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-20px, -20px) rotate(360deg); }
        }
        
        .logo-container {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .logo-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 10px;
            background-color: white;
            padding: 5px;
        }
        
        .logo-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
        }
        
        .logo-placeholder i {
            font-size: 2.8rem;
            color: white;
        }
        
        .app-name {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .app-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 300;
            line-height: 1.4;
        }
        
        .login-body {
            padding: 35px 30px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 8px;
            color: var(--primary-color);
        }
        
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        
        .form-control {
            padding: 14px 16px 14px 45px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            font-size: 1rem;
            transition: var(--transition);
            background-color: #f8fafc;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            background-color: white;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            z-index: 5;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            z-index: 5;
            transition: var(--transition);
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .login-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .login-btn:hover {
            background: var(--gradient-secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .login-btn i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            border: none;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-danger {
            background-color: rgba(248, 113, 113, 0.1);
            color: #dc2626;
            border-left: 4px solid var(--error-color);
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .copyright {
            text-align: center;
            margin-top: 25px;
            color: #64748b;
            font-size: 0.85rem;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-body {
                padding: 25px 20px;
            }
            
            .login-header {
                padding: 20px 15px;
            }
            
            .logo-placeholder, .logo-image {
                width: 85px;
                height: 85px;
            }
            
            .logo-placeholder i {
                font-size: 2.4rem;
            }
            
            .app-name {
                font-size: 1.6rem;
            }
            
            .app-subtitle {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background decorative elements -->
    <div class="bg-element bg-1"></div>
    <div class="bg-element bg-2"></div>
    <div class="bg-element bg-3"></div>
    
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="logo-container">
                    <!-- Jika Anda memiliki gambar logo, gunakan tag img di bawah ini -->
                    <!-- <img src="download (1).jpg" alt="Logo SEKOLATEN PERMATA HATI MANADO" class="logo-image"> -->
                    
                    <!-- Placeholder jika belum ada gambar -->
                    <div class="logo-placeholder">
                        <i class="fas fa-school"></i>
                    </div>
                    
                    <h1 class="app-name">PENILAIAN KINERJA GURU KPI 360</h1>
                    <h5 class="app-subtitle" style="color: #dfe5e9ff;">SEKOLAH KRISTEN PERMATA HATI MANADO</h5>
        
                </div>
            </div>
            
            <!-- Body -->
            <div class="login-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login.process') }}" id="loginForm">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="form-group">
                        <label class="form-label" for="email">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <div class="input-group">
                            <i class="input-icon fas fa-user"></i>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="form-control" 
                                placeholder="nama@email.com" 
                                required 
                                autofocus
                            >
                        </div>
                    </div>
                    
                    <!-- Password Input -->
                    <div class="form-group">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <i class="input-icon fas fa-key"></i>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="form-control" 
                                placeholder="Masukkan password" 
                                required
                            >
                            <i class="password-toggle fas fa-eye" id="togglePassword"></i>
                        </div>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i> Masuk ke Aplikasi
                    </button>
                </form>
                
                <!-- Copyright -->
                <div class="copyright">
                    &copy; 2026 SEKOLAH KRISTEN PERMATA HATI MANADO. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Logo handling
    const logoPlaceholder = document.querySelector('.logo-placeholder');
    const logoContainer = document.querySelector('.logo-container');

    if (logoPlaceholder) {
        logoPlaceholder.style.display = 'none';
    }

    const logoImg = document.createElement('img');
    logoImg.src = "{{ asset('images/download.jpg') }}";
    logoImg.alt = 'Logo SEKOLAH KRISTEN PERMATA HATI MANADO';
    logoImg.className = 'logo-image';

    logoContainer.insertBefore(logoImg, logoContainer.firstChild);

});
</script>
</body>
</html>