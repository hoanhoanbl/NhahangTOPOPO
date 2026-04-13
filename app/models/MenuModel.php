<?php
class MenuModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Lấy danh sách các danh mục có món ăn tại một cơ sở
     */
    public function findCategoriesByCoSo($maCoSo) {
        $sql = "SELECT DISTINCT dm.MaDM, dm.TenDM
                FROM menu_coso mc
                JOIN monan m ON mc.MaMon = m.MaMon
                JOIN danhmuc dm ON m.MaDM = dm.MaDM
                WHERE mc.MaCoSo = ?
                ORDER BY dm.MaDM";
                
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $maCoSo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
        return $categories;
    }

    /**
     * Lấy danh sách món ăn theo cơ sở và danh mục
     */
    public function findMenuItemsByCoSoAndCategory($maCoSo, $selectedCategory = 'all') {
        if ($selectedCategory === 'all') {
            $sql = "SELECT m.MaMon, m.TenMon, m.MoTa, m.HinhAnhURL, mc.Gia, dm.TenDM, dm.MaDM
                    FROM menu_coso mc
                    JOIN monan m ON mc.MaMon = m.MaMon
                    JOIN danhmuc dm ON m.MaDM = dm.MaDM
                    WHERE mc.MaCoSo = ? AND mc.TinhTrang = 'con_hang'
                    ORDER BY dm.MaDM, m.TenMon";
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "i", $maCoSo);
        } else {
            $sql = "SELECT m.MaMon, m.TenMon, m.MoTa, m.HinhAnhURL, mc.Gia, dm.TenDM, dm.MaDM
                    FROM menu_coso mc
                    JOIN monan m ON mc.MaMon = m.MaMon
                    JOIN danhmuc dm ON m.MaDM = dm.MaDM
                    WHERE mc.MaCoSo = ? AND dm.MaDM = ? AND mc.TinhTrang = 'con_hang'
                    ORDER BY m.TenMon";
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $maCoSo, $selectedCategory);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $menuItems = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $menuItems[] = $row;
        }
        return $menuItems;
    }

    /**
     * Lấy danh sách món ăn đã được nhóm theo danh mục
     */
    public function findMenuItemsGroupedByCategory($maCoSo) {
        $menuItems = $this->findMenuItemsByCoSoAndCategory($maCoSo, 'all');
        // Nhóm các món theo danh mục
        $groupedItems = [];
        foreach ($menuItems as $item) {
            $categoryName = $item['TenDM'];
            if (!isset($groupedItems[$categoryName])) {
                $groupedItems[$categoryName] = [];
            }
            $groupedItems[$categoryName][] = $item;
        }
        
        return $groupedItems;
    }

    /**
     * Tìm kiếm món ăn theo tên và mã cơ sở
     */
public function searchMenuItems($maCoSo, $tenMon = '') {
    error_log("Searching menu items for MaCoSo: $maCoSo with TenMon: $tenMon");
    $sql = "SELECT m.MaMon, m.TenMon, m.MoTa, m.HinhAnhURL, mc.Gia
            FROM menu_coso mc
            JOIN monan m ON mc.MaMon = m.MaMon
            WHERE mc.MaCoSo = ? AND mc.TinhTrang = 'con_hang'";
    
    if (!empty($tenMon)) {
        // $sql .= " AND  m.TenMon LIKE ? COLLATE utf8mb4_0900_as_ci";
        $sql .= " AND  m.TenMon LIKE ?";
        $sql .= " ORDER BY m.TenMon";
        
        $stmt = mysqli_prepare($this->db, $sql);
        $searchTerm = '%' . $tenMon . '%';
        mysqli_stmt_bind_param($stmt, "is", $maCoSo, $searchTerm);
    } else {
        $sql .= " ORDER BY m.TenMon";
        
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $maCoSo);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $menuItems = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $menuItems[] = $row;
    }
    return $menuItems;
}



}
?>