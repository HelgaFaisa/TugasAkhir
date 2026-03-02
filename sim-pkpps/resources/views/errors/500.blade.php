<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 14px;
        }
        
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 36px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .error-icon {
            font-size: 100px;
            color: #f44336;
            margin-bottom: 14px;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        h1 {
            font-size: 72px;
            color: #333;
            margin-bottom: 10px;
        }
        
        h2 {
            font-size: 21px;
            color: #666;
            margin-bottom: 14px;
        }
        
        .error-message {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
            border-radius: 5px;
        }
        
        .error-message strong {
            color: #c62828;
            display: block;
            margin-bottom: 5px;
        }
        
        .error-message p {
            color: #666;
            font-size: 11px;
            margin: 0;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        @media (max-width: 768px) {
            .error-container {
                padding: 22px 20px;
            }
            
            h1 {
                font-size: 48px;
            }
            
            h2 {
                font-size: 20px;
            }
            
            .error-icon {
                font-size: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h1>500</h1>
        <h2>Oops! Terjadi Kesalahan Server</h2>
        
        <p style="color: #999; margin-bottom: 14px;">
            Maaf, terjadi kesalahan pada server. Tim kami sudah diberitahu dan sedang menangani masalah ini.
        </p>
        
        @if(isset($error) && config('app.debug'))
        <div class="error-message">
            <strong><i class="fas fa-bug"></i> Detail Error (Debug Mode):</strong>
            <p>{{ $error }}</p>
        </div>
        @endif
        
        <a href="javascript:history.back()" class="btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        
        <a href="{{ route('santri.dashboard') }}" class="btn">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </div>
</body>
</html>