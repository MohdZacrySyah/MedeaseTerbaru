<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
    <title>Login Tenaga Medis - Praktek Bersama</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 900px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            display: flex;
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        .login-illustration {
            flex-basis: 45%;
            background: linear-gradient(160deg, #169400 0%, #0f7300 100%);
            padding: 50px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .illustration-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .illustration-logo img { width: 50px; height: auto; }

        .illustration-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .illustration-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .login-form-container {
            flex-basis: 55%;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: #169400;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .form-group { margin-bottom: 20px; }
        
        .form-label { 
            font-size: 0.9rem; 
            font-weight: 600; 
            color: #374151; 
            margin-bottom: 8px; 
            display: block; 
        }

        .input-wrapper { position: relative; }
        
        .form-input {
            width: 100%; 
            padding: 14px 20px 14px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            background-color: #fafafa;
            transition: all 0.3s ease;
        }
        
        .form-input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s ease;
        }

        .input-wrapper:focus-within .form-input {
            border-color: #169400;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(22, 148, 0, 0.1);
        }
        
        .input-wrapper:focus-within .form-input-icon { 
            color: #169400; 
        }

        /* Error Input State */
        .form-input.input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2 !important;
        }

        /* Submit Button - DIUBAH (Hapus animasi hover & box shadow berlebihan) */
        .btn-submit {
            width: 100%; 
            padding: 16px;
            background: linear-gradient(135deg, #169400 0%, #1cc200 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 10px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-submit:hover { 
            background: linear-gradient(135deg, #1cc200 0%, #169400 100%);
        }

        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-submit.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: buttonSpinner 0.6s linear infinite;
        }

        @keyframes buttonSpinner {
            to { transform: rotate(360deg); }
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        
        .alert i {
            margin-top: 3px;
            font-size: 1.1rem;
        }

        .alert ul {
            margin-top: 5px;
            padding-left: 20px;
        }

        .alert-error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95);} 
            to { opacity: 1; transform: scale(1);} 
        }

        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; max-width: 450px; margin: 20px; }
            .login-illustration { padding: 40px 30px; }
            .login-form-container { padding: 40px 30px; }
            .form-title { font-size: 1.5rem; justify-content: center; }
            .form-subtitle { text-align: center; }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-illustration">
            <div class="illustration-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>
            <h1 class="illustration-title">Panel Tenaga Medis</h1>
            <p class="illustration-subtitle">
                Masuk untuk melihat jadwal pemeriksaan, mengelola pasien, dan melakukan pelayanan medis.
            </p>
        </div>

        <div class="login-form-container">

            <h2 class="form-title"><i class="fas fa-user-nurse"></i> Login Tenaga Medis</h2>
            <p class="form-subtitle">Silakan masukkan email dan password Anda.</p>

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Login Gagal</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div id="jsErrors"></div>

            <form method="POST" action="{{ route('tenaga-medis.login.post') }}" id="loginForm" novalidate>
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-input" placeholder="Masukkan email" required>
                        <i class="form-input-icon fas fa-envelope"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan password" required>
                        <i class="form-input-icon fas fa-lock"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Login</span>
                </button>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.querySelector('.btn-submit');
    const jsErrorsBox = document.getElementById('jsErrors');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Validasi Form JS saat submit
    loginForm.addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessages = [];

        // Hapus pesan error & style error sebelumnya
        jsErrorsBox.innerHTML = '';
        [emailInput, passwordInput].forEach(el => {
            el.classList.remove('input-error');
        });

        // 1. Validasi Email
        if (emailInput.value.trim() === '') {
            isValid = false;
            emailInput.classList.add('input-error');
            errorMessages.push('Email tidak boleh kosong.');
        } else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(emailInput.value)) {
            isValid = false;
            emailInput.classList.add('input-error');
            errorMessages.push('Format email tidak valid.');
        }

        // 2. Validasi Password
        if (passwordInput.value.trim() === '') {
            isValid = false;
            passwordInput.classList.add('input-error');
            errorMessages.push('Password tidak boleh kosong.');
        }

        // Jika tidak valid, hentikan pengiriman form dan tampilkan error
        if (!isValid) {
            e.preventDefault();
            
            let errorHtml = `
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Login Tertunda</strong>
                        <ul>
            `;
            errorMessages.forEach(msg => {
                errorHtml += `<li>${msg}</li>`;
            });
            errorHtml += `</ul></div></div>`;
            
            jsErrorsBox.innerHTML = errorHtml;
        } else {
            // Jika valid, izinkan form terkirim dan munculkan efek loading pada tombol
            submitBtn.classList.add('loading');
        }
    });

    // Menghilangkan efek error saat user mulai mengetik lagi
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('input-error');
            if (jsErrorsBox.innerHTML !== '') {
                jsErrorsBox.innerHTML = ''; 
            }
        });

        // Input Animation on Focus (Sedikit membesar)
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.3s ease';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Disable form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});
</script>
</body>
</html>