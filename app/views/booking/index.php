<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đặt bàn trực tuyến - Nhà hàng cao cấp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hero-section { 
            background: linear-gradient(135deg, rgba(44, 90, 160, 0.95), rgba(30, 61, 114, 0.95)), 
                        url('public/images/restaurant-bg.jpg'); 
            background-size: cover; 
            background-position: center; 
            color: white; 
            padding: 100px 0; 
            text-align: center; 
        }
        .feature-card { 
            background: white; 
            border-radius: 15px; 
            padding: 30px; 
            box-shadow: 0 5px 25px rgba(0,0,0,0.1); 
            text-align: center; 
            margin-bottom: 30px; 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
        }
        .feature-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 15px 35px rgba(0,0,0,0.15); 
        }
        .feature-icon { 
            font-size: 3rem; 
            color: #2c5aa0; 
            margin-bottom: 20px; 
        }
        .btn-booking { 
            background: linear-gradient(135deg, #ff6b35, #f7931e); 
            border: none; 
            border-radius: 25px; 
            padding: 15px 40px; 
            font-size: 1.2rem; 
            font-weight: bold; 
            color: white; 
            margin: 20px 10px; 
            transition: all 0.3s ease; 
        }
        .btn-booking:hover { 
            background: linear-gradient(135deg, #f7931e, #ff6b35); 
            transform: translateY(-2px); 
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4); 
            color: white; 
        }
        .stats-section { 
            background: #f8f9fa; 
            padding: 60px 0; 
        }
        .stat-item { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .stat-number { 
            font-size: 3rem; 
            font-weight: bold; 
            color: #2c5aa0; 
        }
        .branches-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin-top: 40px; 
        }
        .branch-card { 
            background: white; 
            border-radius: 15px; 
            padding: 25px; 
            box-shadow: 0 3px 15px rgba(0,0,0,0.1); 
        }
        .process-step { 
            display: flex; 
            align-items: center; 
            margin-bottom: 20px; 
        }
        .step-number { 
            background: #2c5aa0; 
            color: white; 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: bold; 
            margin-right: 15px; 
        }
    </style>
</head>
<body>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">Đặt bàn trực tuyến</h1>
            <p class="lead mb-5">Trải nghiệm ẩm thực đẳng cấp với dịch vụ đặt bàn tiện lợi, thanh toán an toàn</p>
            <a href="?page=booking&action=create" class="btn btn-booking">
                <i class="bi bi-calendar-plus me-2"></i>
                Đặt bàn ngay
            </a>
            <a href="#features" class="btn btn-outline-light btn-booking">
                <i class="bi bi-info-circle me-2"></i>
                Tìm hiểu thêm
            </a>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Tại sao chọn chúng tôi?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h4>Đặt bàn 24/7</h4>
                        <p class="text-muted">Đặt bàn mọi lúc mọi nơi với hệ thống trực tuyến tiện lợi, xử lý tự động nhanh chóng.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4>Thanh toán an toàn</h4>
                        <p class="text-muted">Tích hợp SePay với nhiều phương thức thanh toán, bảo mật cao, xử lý giao dịch tức thì.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4>Nhiều chi nhánh</h4>
                        <p class="text-muted">Hệ thống 5+ chi nhánh khắp thành phố, phục vụ đa dạng nhu cầu của khách hàng.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Process Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Quy trình đặt bàn</h2>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <div>
                            <h5>Điền thông tin đặt bàn</h5>
                            <p class="text-muted mb-0">Nhập thông tin cá nhân, chọn chi nhánh, thời gian và số người</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <div>
                            <h5>Thanh toán phí đặt bàn</h5>
                            <p class="text-muted mb-0">Thanh toán 50,000đ qua QR code hoặc chuyển khoản ngân hàng</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <div>
                            <h5>Xác nhận đặt bàn thành công</h5>
                            <p class="text-muted mb-0">Nhận thông tin xác nhận và đến nhà hàng đúng giờ</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="?page=booking&action=create" class="btn btn-booking">
                    <i class="bi bi-play-circle me-2"></i>
                    Bắt đầu đặt bàn
                </a>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">5+</div>
                        <p class="text-muted">Chi nhánh</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">10K+</div>
                        <p class="text-muted">Khách hàng hài lòng</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <p class="text-muted">Hỗ trợ khách hàng</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">100+</div>
                        <p class="text-muted">Món ăn đặc sắc</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Branches Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Hệ thống chi nhánh</h2>
            <div class="branches-grid">
                <div class="branch-card">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        10 Nguyễn Văn Huyên
                    </h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-house me-2"></i>
                        Địa chỉ: Thanh Khê, Hà Nội
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-telephone me-2"></i>
                        SĐT: 0922.782.387
                    </p>
                </div>
                <div class="branch-card">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        68 Láng Thượng
                    </h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-house me-2"></i>
                        Địa chỉ: Ngũ Hành Sơn, Hà Nội
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-telephone me-2"></i>
                        SĐT: 0922.782.387
                    </p>
                </div>
                <div class="branch-card">
                    <h5 class="text-primary mb-3">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        505 Minh Khai
                    </h5>
                    <p class="text-muted mb-2">
                        <i class="bi bi-house me-2"></i>
                        Địa chỉ: Sơn Trà, Hà Nội
                    </p>
                    <p class="text-muted mb-0">
                        <i class="bi bi-telephone me-2"></i>
                        SĐT: 0922.782.387
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #2c5aa0, #1e3d72); color: white;">
        <div class="container text-center">
            <h2 class="mb-4">Sẵn sàng trải nghiệm?</h2>
            <p class="lead mb-4">Đặt bàn ngay để thưởng thức những món ăn tuyệt vời tại nhà hàng của chúng tôi</p>
            <a href="?page=booking&action=create" class="btn btn-booking">
                <i class="bi bi-calendar-check me-2"></i>
                Đặt bàn ngay
            </a>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
