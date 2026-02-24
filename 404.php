<?php
// filepath: c:\laragon\www\admin-tb5\404.php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | TB5 Training Center</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --royal-blue: #4169E1;
            --royal-dark: #2e51b8;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .error-container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: var(--royal-blue);
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 3px 3px 0px rgba(65, 105, 225, 0.2);
        }
        
        .error-icon {
            font-size: 5rem;
            color: var(--royal-blue);
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }
        
        .btn-royal {
            background-color: var(--royal-blue);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-royal:hover {
            background-color: var(--royal-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: white;
        }
        
        .btn-outline-royal {
            border: 2px solid var(--royal-blue);
            color: var(--royal-blue);
            background: transparent;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-outline-royal:hover {
            background-color: var(--royal-blue);
            color: white;
        }
        
        .error-links {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        @media (max-width: 576px) {
            .error-code {
                font-size: 5rem;
            }
            
            .error-icon {
                font-size: 3rem;
            }
            
            .error-container {
                margin: 1rem;
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <i class="fas fa-exclamation-triangle error-icon"></i>
        <div class="error-code">404</div>
        <h2 class="mb-3">Page Not Found</h2>
        <p class="text-muted mb-4">
            Oops! The page you're looking for doesn't exist or has been moved.
            <br>Let's get you back on track!
        </p>
        
        <div class="error-links">
            <a href="javascript:history.back()" class="btn-outline-royal">
                <i class="fas fa-arrow-left me-2"></i>Go Back
            </a>
        </div>
        
        <hr class="my-4">
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>