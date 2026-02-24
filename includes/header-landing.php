<?php
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Kost Nyaman & Strategis' ?> - Rental Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
        
        .navbar-landing {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 24px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-link {
            color: #333;
            font-weight: 500;
            margin: 0 10px;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        .btn-login-admin {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 8px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        
        .btn-login-admin:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 80px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content h1 {
            color: white;
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero-content p {
            color: rgba(255,255,255,0.9);
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        .btn-hero {
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            margin: 10px 10px 10px 0;
            transition: all 0.3s;
        }
        
        .btn-hero-primary {
            background: white;
            color: var(--primary-color);
            border: none;
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-hero-outline:hover {
            background: white;
            color: var(--primary-color);
        }
        
        .hero-image {
            text-align: center;
        }
        
        .stats-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .stat-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
        }
        
        .stat-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
        }
        
        .rooms-section {
            padding: 100px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }
        
        .section-title p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .room-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            margin-bottom: 30px;
        }
        
        .room-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .room-image {
            height: 250px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }
        
        .room-body {
            padding: 25px;
        }
        
        .room-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .room-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .room-price small {
            font-size: 0.9rem;
            color: #666;
            font-weight: 400;
        }
        
        .room-features {
            list-style: none;
            margin-bottom: 20px;
        }
        
        .room-features li {
            padding: 5px 0;
            color: #666;
        }
        
        .room-features li i {
            color: #28a745;
            margin-right: 10px;
        }
        
        .btn-room {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.3s;
        }
        
        .btn-room:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .facilities-section {
            padding: 100px 0;
            background: #f8f9fa;
        }
        
        .facility-card {
            text-align: center;
            padding: 40px 30px;
            background: white;
            border-radius: 20px;
            margin-bottom: 30px;
            transition: all 0.3s;
        }
        
        .facility-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .facility-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
        
        .contact-section {
            padding: 100px 0;
        }
        
        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        
        .contact-info-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 20px;
            flex-shrink: 0;
        }
        
        .btn-whatsapp {
            background: #25D366;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.3s;
        }
        
        .btn-whatsapp:hover {
            transform: translateY(-3px);
            color: white;
        }
        
        .footer-landing {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px 0 20px;
            text-align: center;
        }
        
        .footer-landing p {
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
            
            .btn-hero {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-landing">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="fas fa-building me-2"></i>RentalKost
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
    <li class="nav-item">
        <a class="nav-link" href="home.php">Beranda</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="rooms-public.php">Kamar</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="home.php#fasilitas">Fasilitas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="home.php#kontak">Kontak</a>
    </li>
    <li class="nav-item ms-3">
        <a class="btn btn-login-admin" href="login.php">
            <i class="fas fa-user-lock me-2"></i>Admin
        </a>
    </li>
</ul>
            </div>
        </div>
    </nav>