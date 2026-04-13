<?php
/**
 * EmailService
 * Sử dụng PHPMailer
 */

// Load PHPMailer
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Gửi email thông báo thanh toán thành công
 * @param string $email Email khách hàng
 * @param string $tenKH Tên khách hàng  
 * @param int $maDon Mã đơn đặt bàn
 * @param string $tenCoSo Tên cơ sở
 * @param string $thoiGian Thời gian đặt bàn (đã format)
 * @param int $soNguoi Số người
 * @param string $danhSachBan Danh sách bàn
 * @param array $danhSachMon Danh sách món ăn
 * @param int $tongTien Tổng tiền
 * @param string $ghiChu Ghi chú (optional)
 * @return bool True nếu gửi thành công
 */
function gui_email_thanh_toan_thanh_cong($email, $tenKH, $maDon, $tenCoSo, $thoiGian, $soNguoi, $danhSachBan, $danhSachMon, $tongTien, $ghiChu = '') 
{
    try {
        // Tạo đối tượng PHPMailer
        $mail = new PHPMailer(true);

        // Cấu hình SMTP đơn giản
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->Port = SMTP_PORT;
        
        // Chọn mã hóa dựa trên port
        if (SMTP_PORT == 465) {
            $mail->SMTPSecure = 'ssl';
        } else {
            $mail->SMTPSecure = 'tls';
        }
        
        // Tắt xác minh SSL (cho localhost)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Thiết lập email
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(SMTP_USERNAME, 'Nhà Hàng');
        $mail->addAddress($email, $tenKH);
        $mail->isHTML(true);
        
        // Tiêu đề email
        $mail->Subject = "Xác nhận thanh toán #DH{$maDon} - {$tenCoSo}";
        
        // Nội dung email đơn giản
        $noiDung = tao_noi_dung_email($tenKH, $maDon, $tenCoSo, $thoiGian, $soNguoi, $danhSachBan, $danhSachMon, $tongTien, $ghiChu);
        $mail->Body = $noiDung;
        
        // Gửi email
        $ketQua = $mail->send();
        
        if ($ketQua) {
            error_log("Gửi email thành công đến: {$email} - Đơn #{$maDon}");
            return true;
        } else {
            error_log("Gửi email thất bại đến: {$email} - Đơn #{$maDon}");
            return false;
        }

    } catch (Exception $e) {
        error_log("Lỗi email: " . $e->getMessage());
        return false;
    }
}

/**
 * Tạo nội dung email HTML đơn giản
 */
function tao_noi_dung_email($tenKH, $maDon, $tenCoSo, $thoiGian, $soNguoi, $danhSachBan, $danhSachMon, $tongTien, $ghiChu) 
{
    // Tạo HTML danh sách món ăn
    $htmlMon = '';
    $tongTienMon = 0;
    
    if (!empty($danhSachMon)) {
        $htmlMon = '<h3>Chi tiết món ăn</h3>';
        $htmlMon .= '<table border="1" style="border-collapse: collapse; width: 100%;">';
        $htmlMon .= '<tr style="background: #4CAF50; color: white;">';
        $htmlMon .= '<th style="padding: 8px;">Tên món</th>';
        $htmlMon .= '<th style="padding: 8px;">Số lượng</th>';
        $htmlMon .= '<th style="padding: 8px;">Đơn giá</th>';
        $htmlMon .= '<th style="padding: 8px;">Thành tiền</th>';
        $htmlMon .= '</tr>';
        
        foreach ($danhSachMon as $mon) {
            $thanhTien = $mon['SoLuong'] * $mon['DonGia'];
            $tongTienMon += $thanhTien;
            
            $htmlMon .= '<tr>';
            $htmlMon .= '<td style="padding: 8px;">' . $mon['TenMon'] . '</td>';
            $htmlMon .= '<td style="padding: 8px; text-align: center;">' . $mon['SoLuong'] . '</td>';
            $htmlMon .= '<td style="padding: 8px; text-align: right;">' . number_format($mon['DonGia']) . 'đ</td>';
            $htmlMon .= '<td style="padding: 8px; text-align: right;"><b>' . number_format($thanhTien) . 'đ</b></td>';
            $htmlMon .= '</tr>';
        }
        $htmlMon .= '</table><br>';
    }
    
    // Link xem hóa đơn
    $linkHoaDon = BASE_URL . "/sepay/invoice.php?booking_id={$maDon}";
    
    // HTML email đơn giản
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Xác nhận thanh toán</title>
    </head>
    <body style='font-family: Arial; margin: 20px; background: #f5f5f5;'>
        
        <div style='max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px;'>
            
            <!-- Header -->
            <div style='background: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px; margin-bottom: 20px;'>
                <h1>THANH TOÁN THÀNH CÔNG!</h1>
                <p>Cảm ơn bạn đã đặt bàn tại {$tenCoSo}</p>
            </div>
            
            <!-- Thông tin khách hàng -->
            <p>Xin chào <b>{$tenKH}</b>,</p>
            <p>Chúng tôi đã nhận được thanh toán cho đơn đặt bàn của bạn.</p>
            
            <!-- Thông tin đặt bàn -->
            <div style='background: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0;'>
                <h3>Thông tin đặt bàn</h3>
                <p><b>Mã đặt bàn:</b> #DH{$maDon}</p>
                <p><b>Chi nhánh:</b> {$tenCoSo}</p>
                <p><b>Thời gian:</b> {$thoiGian}</p>
                <p><b>Số người:</b> {$soNguoi} người</p>
                <p><b>Bàn:</b> {$danhSachBan}</p>";
                
    if (!empty($ghiChu)) {
        $html .= "<p><b>Ghi chú:</b> {$ghiChu}</p>";
    }
    
    $html .= "
            </div>
            
            <!-- Danh sách món -->
            {$htmlMon}
            
            <!-- Tổng tiền -->
            <div style='background: #e8f5e9; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;'>
                <h3>Tổng thanh toán: " . number_format($tongTien) . "đ</h3>
                <p style='color: #4CAF50; font-weight: bold;'>Đã thanh toán thành công</p>
            </div>
            
            <!-- Nút xem hóa đơn -->
            <div style='text-align: center; margin: 30px 0;'>
                <a href='{$linkHoaDon}' style='background: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                    Xem hóa đơn chi tiết
                </a>
            </div>
            
            <!-- Liên hệ -->
            <div style='background: #fff3cd; padding: 10px; border-radius: 5px; text-align: center;'>
                <p><b>Hỗ trợ:</b> 0987.654.321 | Email: support@restaurant.com</p>
            </div>
            
            <p>Cảm ơn bạn đã tin tưởng chúng tôi. Hẹn gặp lại!</p>
            
            <p style='color: #666;'>
                Trân trọng,<br>
                <b>Đội ngũ {$tenCoSo}</b>
            </p>
            
            <!-- Footer -->
            <div style='border-top: 1px solid #ddd; padding-top: 15px; margin-top: 30px; text-align: center; color: #666; font-size: 12px;'>
                <p>Email này được gửi tự động. Vui lòng không reply.<br>
                © 2024 Hệ thống đặt bàn nhà hàng</p>
            </div>
            
        </div>
    </body>
    </html>";
    
    return $html;
}

/**
 * Hàm đơn giản để test gửi email
 * @param string $email Email test
 * @return bool
 */
function test_gui_email($email) 
{
    return gui_email_thanh_toan_thanh_cong(
        $email,
        'Khách Hàng Test',
        123,
        'Chi nhánh Test',
        '16/10/2024 19:30',
        2,
        'Bàn A1 (4 chỗ)',
        [
            ['TenMon' => 'Phở Bò', 'SoLuong' => 2, 'DonGia' => 50000],
            ['TenMon' => 'Cơm Gà', 'SoLuong' => 1, 'DonGia' => 45000]
        ],
        145000,
        'Không cay'
    );
}

?>
