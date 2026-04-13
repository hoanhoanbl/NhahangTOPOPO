<?php
/**
 * Helper functions cho module nhân viên
 */

class NhanVienHelper
{
    /**
     * Hàm hiển thị trạng thái đơn đặt bàn
     */
    public static function getStatusBadge($status) {
        switch($status) {
            case 'cho_xac_nhan':
                return '<span class="status-badge pending"><i class="fas fa-clock"></i> Chờ xác nhận</span>';
            case 'da_xac_nhan':
                return '<span class="status-badge confirmed"><i class="fas fa-check-circle"></i> Đã xác nhận</span>';
            case 'da_huy':
                return '<span class="status-badge cancelled"><i class="fas fa-times-circle"></i> Đã hủy</span>';
            case 'hoan_thanh':
                return '<span class="status-badge completed"><i class="fas fa-check-double"></i> Hoàn thành</span>';
            default:
                return '<span class="status-badge">' . htmlspecialchars($status) . '</span>';
        }
    }

    /**
     * Hàm định dạng ngày giờ
     */
    public static function formatDateTime($dateTime) {
        if (empty($dateTime)) return '';
        return date('d/m/Y H:i', strtotime($dateTime));
    }

    /**
     * Hàm định dạng tiền tệ
     */
    public static function formatCurrency($amount) {
        if (empty($amount)) return '0 đ';
        return number_format($amount, 0, ',', '.') . ' đ';
    }

    /**
     * Hàm định dạng số điện thoại
     */
    public static function formatPhoneNumber($phone) {
        if (empty($phone)) return '';
        // Format: 0xxx xxx xxx
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) == 10 && $phone[0] == '0') {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
        }
        return $phone;
    }

    /**
     * Hàm cắt ngắn văn bản
     */
    public static function truncateText($text, $length = 50) {
        if (empty($text)) return '';
        if (strlen($text) <= $length) return $text;
        return substr($text, 0, $length) . '...';
    }

    /**
     * Hàm kiểm tra quyền truy cập của nhân viên
     */
    public static function hasPermission($permission) {
        $currentRole = strtolower(trim($_SESSION['user']['ChucVu'] ?? ''));
        if (!isset($_SESSION['user']) || !in_array($currentRole, ['nhan_vien', 'receptionist', 'le_tan', 'letan'], true)) {
            return false;
        }
        
        // Tạm thời cho phép tất cả permissions cho nhân viên
        // Có thể mở rộng thêm logic phân quyền chi tiết hơn
        return true;
    }

    /**
     * Hàm tạo URL với các tham số
     */
    public static function buildUrl($page, $action = '', $params = []) {
        $url = "index.php?page=$page";
        
        if (!empty($action)) {
            $url .= "&action=$action";
        }
        
        foreach ($params as $key => $value) {
            $url .= "&" . urlencode($key) . "=" . urlencode($value);
        }
        
        return $url;
    }

    /**
     * Hàm tạo pagination links
     */
    public static function buildPaginationLinks($currentPage, $totalPages, $baseUrl, $params = []) {
        $links = [];
        
        // Previous link
        if ($currentPage > 1) {
            $prevParams = array_merge($params, ['booking_page' => $currentPage - 1]);
            $links['prev'] = $baseUrl . '&' . http_build_query($prevParams);
        }
        
        // Page links
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            $pageParams = array_merge($params, ['booking_page' => $i]);
            $links['pages'][$i] = [
                'url' => $baseUrl . '&' . http_build_query($pageParams),
                'active' => ($i == $currentPage)
            ];
        }
        
        // Next link
        if ($currentPage < $totalPages) {
            $nextParams = array_merge($params, ['booking_page' => $currentPage + 1]);
            $links['next'] = $baseUrl . '&' . http_build_query($nextParams);
        }
        
        return $links;
    }

    /**
     * Hàm validate input
     */
    public static function validateInput($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? '';
            
            // Required check
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = $rule['message'] ?? "Trường $field là bắt buộc.";
                continue;
            }
            
            // Min length check
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = $rule['message'] ?? "Trường $field phải có ít nhất {$rule['min_length']} ký tự.";
                continue;
            }
            
            // Max length check
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = $rule['message'] ?? "Trường $field không được vượt quá {$rule['max_length']} ký tự.";
                continue;
            }
            
            // Email validation
            if (isset($rule['email']) && $rule['email'] && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = $rule['message'] ?? "Trường $field phải là email hợp lệ.";
                continue;
            }
        }
        
        return $errors;
    }
}
?>
