<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Nhà hàng Tự Do</title>
    <link rel="stylesheet" href="/DatBanNH/public/css/style-menu.css">
    <link rel="stylesheet" href="/DatBanNH/public/css/constants.css">
    <link rel="stylesheet" href="/DatBanNH/public/css/layout/header.css">
    <link rel="stylesheet" href="/DatBanNH/public/css/layout/footer.css">
    <style>
        :root {
            /* Updated to match the main theme's primary green color */
            --primary-color: #1B4E30; 
            --secondary-color: #2c5e42; /* A deeper green for accents */
            --text-color: #34495e;
            --bg-color: #fdfaf6;
            --container-bg: #ffffff;
            --border-color: #e8e8e8;
            --font-main: 'Plus Jakarta Sans', sans-serif;
        }

        .contact-page-wrapper {
            font-family: var(--font-main);
            background-color: var(--bg-color);
            padding: 60px 20px;
            color: var(--text-color);
            width: 100%;
            box-sizing: border-box;
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            background: var(--container-bg);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
        }

        @media (max-width: 992px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }

        /* --- Contact Info Section --- */
        .contact-info {
            /* Changed from gradient to solid primary color */
            background: var(--primary-color);
            color: #fff;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .contact-info h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .contact-info .info-text {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .info-item i {
            font-size: 1.5rem;
            width: 40px;
            flex-shrink: 0;
            opacity: 0.9;
        }

        .info-item a {
            color: #fff;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        .info-item a:hover {
            opacity: 0.8;
        }


      

      

        /* --- Contact Form Section --- */
        .contact-form {
            padding: 50px 40px;
        }

        .contact-form h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--text-color);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px; /* Reduced padding */
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem; /* Slightly smaller font */
            font-family: var(--font-main);
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            /* Updated shadow color */
            box-shadow: 0 0 8px rgba(27, 78, 48, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
        }

        .submit-btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: #fff;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-3px);
        }

        /* Success message style */
        .success-message {
            background-color: #e8f5e9; /* Light green */
            color: #1b5e20; /* Dark green */
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            border: 1px solid #a5d6a7;
        }
    </style>
</head>
<body>

    <main>
        <div class="contact-page-wrapper">
            <div class="contact-container">
                <div class="contact-info">
                    <h2>Liên Hệ</h2>
                    <p class="info-text">
                        Chúng tôi rất mong được lắng nghe từ bạn! Dù bạn có câu hỏi về thực đơn, đặt chỗ hay bất cứ điều gì khác, đội ngũ của chúng tôi luôn sẵn sàng trả lời.
                    </p>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>19 Lê Đức Thọ, Mỹ Đình, Hà Nội</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <a href="tel:+84123456789">+84 123 456 789</a>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:lienhe@nhahang.com"> lienhe@nhahang.com</a>
                    </div>
                   
                </div>

                <div class="contact-form">
                    <h2>Gửi tin nhắn cho chúng tôi</h2>

                    <?php if (isset($data['message']) && $data['message']): ?>
                        <div class="success-message">
                            <?php echo htmlspecialchars($data['message']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="?page=contact&action=send" method="POST">
                        <div class="form-group">
                            <label for="name">Họ và Tên</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Địa chỉ Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Số điện thoại</label>
                            <input type="text" id="subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Nội dung tin nhắn</label>
                            <textarea id="message" name="message" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Gửi tin nhắn</button>
                    </form>
                </div>
            </div>
        </div>
    </main>


    <script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js" crossorigin="anonymous"></script>
</body>
</html>
