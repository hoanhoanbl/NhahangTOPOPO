<?php
// File này được include từ index.php, nên đã có sẵn $selected_branch_id và $conn
// Lấy thông tin của cơ sở đang được chọn
$sql_current_branch = "SELECT TenCoSo FROM coso WHERE MaCoSo = $selected_branch_id";
$result_current_branch = mysqli_query($conn, $sql_current_branch);
$current_branch_name = mysqli_fetch_assoc($result_current_branch)['TenCoSo'];

// Include modal thêm và sửa
include __DIR__ . "/create.php";
include __DIR__ . "/update.php";

// Lấy danh sách món ăn đang có tại cơ sở này từ bảng `menu_coso`
$sql_menu_items = "
    SELECT 
        m.MaMon, 
        m.TenMon, 
        m.HinhAnhURL, 
        mc.Gia, 
        mc.TinhTrang
    FROM menu_coso mc
    JOIN monan m ON mc.MaMon = m.MaMon
    WHERE mc.MaCoSo = $selected_branch_id
    ORDER BY m.TenMon ASC
";
$result_menu_items = mysqli_query($conn, $sql_menu_items);
$menu_items_at_branch = [];
while ($row = mysqli_fetch_assoc($result_menu_items)) {
    $menu_items_at_branch[] = $row;
}

?>

<!-- Phần header của khu vực quản lý -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
        Thực đơn tại: <span class="text-primary"><?php echo htmlspecialchars($current_branch_name); ?></span>
    </h4>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDishToBranchModal">
        <i class="fas fa-plus"></i> Thêm món vào Menu
    </button>
</div>

<!-- Bảng hiển thị danh sách món ăn -->
<table class="table table-bordered align-middle text-center">
    <thead class="table-dark">
        <tr>
            <th width="5%">ID Món</th>
            <th>Tên món ăn</th>
            <th>Hình ảnh</th>
            <th>Giá bán (VNĐ)</th>
            <th>Tình trạng</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($menu_items_at_branch)) : ?>
            <?php foreach ($menu_items_at_branch as $item) : ?>
                <tr>
                    <td><?php echo $item['MaMon']; ?></td>
                    <td class="text-start"><?php echo htmlspecialchars($item['TenMon']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($item['HinhAnhURL']); ?>" width="70" height="70" class="img-fluid rounded"></td>
                    <td><?php echo number_format($item['Gia'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if ($item['TinhTrang'] == 'con_hang') : ?>
                            <span class="badge bg-success">Còn hàng</span>
                        <?php else : ?>
                            <span class="badge bg-danger">Hết hàng</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateDishInBranchModal"
                                    onclick="loadEditData(
                                        '<?php echo $item['MaMon']; ?>', 
                                        '<?php echo htmlspecialchars($item['TenMon'], ENT_QUOTES); ?>', 
                                        '<?php echo $item['Gia']; ?>', 
                                        '<?php echo $item['TinhTrang']; ?>'
                                    )">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <a href="?page=admin&section=menu_branch&action=delete&MaCoSo=<?php echo $selected_branch_id; ?>&MaMon=<?php echo $item['MaMon']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa món \'<?php echo htmlspecialchars($item['TenMon'], ENT_QUOTES); ?>\' khỏi thực đơn cơ sở này không?');">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <!-- Khi không có món ăn nào -->
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                    <br>
                    Cơ sở này chưa có món ăn nào trong thực đơn.
                    <br>
                    <small>Hãy nhấn nút "Thêm món vào Menu" để bắt đầu.</small>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
function loadEditData(maMon, tenMon, gia, tinhTrang) {
    // Điền dữ liệu vào form modal sửa
    document.getElementById('update_MaMon').value = maMon;
    document.getElementById('update_TenMon').getElementsByTagName('strong')[0].textContent = tenMon;
    document.getElementById('update_Gia').value = gia;
    
    // Chọn đúng option cho tình trạng
    const tinhTrangSelect = document.getElementById('update_TinhTrang');
    for (let i = 0; i < tinhTrangSelect.options.length; i++) {
        if (tinhTrangSelect.options[i].value === tinhTrang) {
            tinhTrangSelect.selectedIndex = i;
            break;
        }
    }
}
</script>
