<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include dirname(__DIR__, 4) . "/config/connect.php";
require_once dirname(__DIR__) . "/common/branch-auth.php";
$canTableUpdate = isset($auth) && $auth->can('table', 'update');
$canTableDelete = isset($auth) && $auth->can('table', 'delete');
$authBranch = adminAuth();
$isGlobalAdmin = $authBranch->isAdmin();
$sessionBranchId = $authBranch->getCurrentBranchId();
if (!$isGlobalAdmin && $sessionBranchId <= 0) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Khong tim thay co so duoc phan quyen cho tai khoan nay.');
}

if ($canTableUpdate) {
    include __DIR__ . "/update.php";
}

$sqlCoSo = "SELECT * FROM coso";
if (!$isGlobalAdmin) {
    $sqlCoSo .= " WHERE MaCoSo = " . (int)$sessionBranchId;
}
$sqlCoSo .= " ORDER BY TenCoSo";
$resultCoSo = mysqli_query($conn, $sqlCoSo);
$listCoSo = [];
while ($row = mysqli_fetch_assoc($resultCoSo)) {
    $listCoSo[] = $row;
}

$requestedMaCoSo = isset($_GET['maCoSo']) ? (int)$_GET['maCoSo'] : 0;
$maCoSo = $isGlobalAdmin ? $requestedMaCoSo : $sessionBranchId;
if (!$isGlobalAdmin && $requestedMaCoSo > 0 && $requestedMaCoSo !== $sessionBranchId) {
    adminDenyAndRedirect('?page=admin&section=table&action=view', 'Ban khong co quyen xem ban cua co so khac.');
}
$zoneBan = trim((string)($_GET['zoneBan'] ?? ''));
$trangThai = trim((string)($_GET['trangThai'] ?? ''));
$keyword = trim((string)($_GET['q'] ?? ''));
$tenCoSo = '';

if ($maCoSo > 0) {
    $stmtTenCoSo = mysqli_prepare($conn, "SELECT TenCoSo FROM coso WHERE MaCoSo = ?");
    mysqli_stmt_bind_param($stmtTenCoSo, 'i', $maCoSo);
    mysqli_stmt_execute($stmtTenCoSo);
    $resultTenCoSo = mysqli_stmt_get_result($stmtTenCoSo);
    $coSo = mysqli_fetch_assoc($resultTenCoSo);
    $tenCoSo = $coSo['TenCoSo'] ?? '';
    mysqli_stmt_close($stmtTenCoSo);
}

$zoneSql = "SELECT DISTINCT TRIM(ZoneBan) AS ZoneBan FROM ban WHERE ZoneBan IS NOT NULL AND TRIM(ZoneBan) <> ''";
$zoneParams = [];
$zoneTypes = '';
if ($maCoSo > 0) {
    $zoneSql .= " AND MaCoSo = ?";
    $zoneParams[] = $maCoSo;
    $zoneTypes .= 'i';
}
$zoneSql .= " ORDER BY ZoneBan";
$stmtZone = mysqli_prepare($conn, $zoneSql);
if (!empty($zoneParams)) {
    mysqli_stmt_bind_param($stmtZone, $zoneTypes, ...$zoneParams);
}
mysqli_stmt_execute($stmtZone);
$resultZone = mysqli_stmt_get_result($stmtZone);
$listZone = [];
while ($row = mysqli_fetch_assoc($resultZone)) {
    $listZone[] = trim((string)$row['ZoneBan']);
}
mysqli_stmt_close($stmtZone);

$recordsPerPage = 10;
$pageNumber = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$offset = max(0, ($pageNumber - 1) * $recordsPerPage);

$whereClauses = [];
$params = [];
$types = '';

if ($maCoSo > 0) {
    $whereClauses[] = 'b.MaCoSo = ?';
    $params[] = $maCoSo;
    $types .= 'i';
}
if ($zoneBan !== '') {
    $whereClauses[] = 'TRIM(b.ZoneBan) = TRIM(?)';
    $params[] = $zoneBan;
    $types .= 's';
}
if ($trangThai !== '') {
    $whereClauses[] = 'b.TrangThai = ?';
    $params[] = $trangThai;
    $types .= 's';
}
if ($keyword !== '') {
    $whereClauses[] = '(b.MaBanCode LIKE ? OR b.TenBan LIKE ?)';
    $likeKeyword = '%' . $keyword . '%';
    $params[] = $likeKeyword;
    $params[] = $likeKeyword;
    $types .= 'ss';
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

$countSql = "SELECT COUNT(*) AS total FROM ban b $whereSql";
$stmtCount = mysqli_prepare($conn, $countSql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmtCount, $types, ...$params);
}
mysqli_stmt_execute($stmtCount);
$countResult = mysqli_stmt_get_result($stmtCount);
$totalRecords = (int)(mysqli_fetch_assoc($countResult)['total'] ?? 0);
mysqli_stmt_close($stmtCount);

$totalPages = max(1, (int)ceil($totalRecords / $recordsPerPage));
if ($pageNumber > $totalPages) {
    $pageNumber = $totalPages;
    $offset = max(0, ($pageNumber - 1) * $recordsPerPage);
}

$sql = "SELECT b.*, c.TenCoSo
        FROM ban b
        LEFT JOIN coso c ON b.MaCoSo = c.MaCoSo
        $whereSql
        ORDER BY b.MaCoSo, b.ZoneBan, b.MaBan
        LIMIT ?, ?";

$stmt = mysqli_prepare($conn, $sql);
$queryParams = $params;
$queryTypes = $types . 'ii';
$queryParams[] = $offset;
$queryParams[] = $recordsPerPage;
mysqli_stmt_bind_param($stmt, $queryTypes, ...$queryParams);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tableItems = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tableItems[] = $row;
}
mysqli_stmt_close($stmt);

$baseParams = [
    'page' => 'admin',
    'section' => 'table',
    'action' => 'view',
    'maCoSo' => $maCoSo > 0 ? $maCoSo : '',
    'zoneBan' => $zoneBan,
    'trangThai' => $trangThai,
    'q' => $keyword,
];

function buildQuery(array $base, array $override = []): string
{
    $params = array_merge($base, $override);
    foreach ($params as $k => $v) {
        if ($v === '' || $v === null) {
            unset($params[$k]);
        }
    }
    return '?' . http_build_query($params);
}
?>

<div class="card shadow p-4">
  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success_message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error_message']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>
      Danh sách bàn ăn
      <?php if ($maCoSo > 0): ?>
        - <?php echo htmlspecialchars($tenCoSo); ?> (<?php echo $totalRecords; ?> bàn)
      <?php else: ?>
        (<?php echo $totalRecords; ?> bàn)
      <?php endif; ?>
    </h4>
  </div>

  <form method="GET" class="mb-4" id="tableFilterForm">
    <input type="hidden" name="page" value="admin">
    <input type="hidden" name="section" value="table">
    <input type="hidden" name="action" value="view">

    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Cơ sở</label>
        <select name="maCoSo" class="form-select js-table-filter-auto" <?php echo $isGlobalAdmin ? '' : 'disabled'; ?>>
          <option value="">-- Tất cả cơ sở --</option>
          <?php foreach ($listCoSo as $coSo): ?>
            <option value="<?php echo (int)$coSo['MaCoSo']; ?>" <?php echo ($maCoSo === (int)$coSo['MaCoSo']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($coSo['TenCoSo']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (!$isGlobalAdmin): ?>
          <input type="hidden" name="maCoSo" value="<?php echo (int)$maCoSo; ?>">
        <?php endif; ?>
      </div>

      <div class="col-md-3">
        <label class="form-label">Zone</label>
        <select name="zoneBan" class="form-select js-table-filter-auto">
          <option value="">-- Tất cả zone --</option>
          <?php foreach ($listZone as $zone): ?>
            <option value="<?php echo htmlspecialchars($zone); ?>" <?php echo (trim($zoneBan) === trim((string)$zone)) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($zone); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">Trạng thái</label>
        <select name="trangThai" class="form-select js-table-filter-auto">
          <option value="">-- Tất cả --</option>
          <option value="Active" <?php echo ($trangThai === 'Active') ? 'selected' : ''; ?>>Active</option>
          <option value="Inactive" <?php echo ($trangThai === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Tìm kiếm mã/tên bàn</label>
        <input type="text" class="form-control" name="q" id="tableKeywordInput" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="VD: TBL-0201 hoặc Bàn A01">
      </div>

      <div class="col-md-4">
        <label class="form-label d-block">&nbsp;</label>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
        <a class="btn btn-secondary" href="?page=admin&section=table&action=view"><i class="fas fa-undo"></i> Xóa lọc</a>
      </div>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered align-middle text-center" id="tableTable">
      <thead class="table-dark">
      <tr>
        <th>Mã bàn</th>
        <th>Mã code</th>
        <th>Cơ sở</th>
        <th>Tên bàn</th>
        <th>Zone</th>
        <th>Sức chứa</th>
        <th>Sức chứa tối đa</th>
        <th>Online</th>
        <th>Ghép</th>
        <th>Trạng thái</th>
        <th>Ghi chú</th>
        <th>Hành động</th>
      </tr>
      </thead>
      <tbody>
      <?php if (!empty($tableItems)): ?>
        <?php foreach ($tableItems as $row): ?>
          <?php
            $json = htmlspecialchars(json_encode([
                'MaBan' => (int)$row['MaBan'],
                'MaBanCode' => $row['MaBanCode'] ?? '',
                'MaCoSo' => (int)$row['MaCoSo'],
                'TenBan' => $row['TenBan'] ?? '',
                'ZoneBan' => $row['ZoneBan'] ?? '',
                'SucChua' => (int)($row['SucChua'] ?? 0),
                'SucChuaToiDa' => (int)($row['SucChuaToiDa'] ?? 0),
                'OnlineBookable' => (int)($row['OnlineBookable'] ?? 0),
                'GhepBanDuoc' => (int)($row['GhepBanDuoc'] ?? 0),
                'TrangThai' => $row['TrangThai'] ?? 'Active',
                'GhiChu' => $row['GhiChu'] ?? '',
            ]), ENT_QUOTES);
          ?>
          <tr>
            <td><?php echo (int)$row['MaBan']; ?></td>
            <td><?php echo htmlspecialchars($row['MaBanCode'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['TenCoSo'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['TenBan'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($row['ZoneBan'] ?? ''); ?></td>
            <td><span class="badge bg-danger"><?php echo (int)($row['SucChua'] ?? 0); ?></span></td>
            <td><span class="badge bg-secondary"><?php echo (int)($row['SucChuaToiDa'] ?? 0); ?></span></td>
            <td><span class="badge <?php echo ((int)($row['OnlineBookable'] ?? 0) === 1) ? 'bg-success' : 'bg-light text-dark'; ?>"><?php echo ((int)($row['OnlineBookable'] ?? 0) === 1) ? 'Có' : 'Không'; ?></span></td>
            <td><span class="badge <?php echo ((int)($row['GhepBanDuoc'] ?? 0) === 1) ? 'bg-primary' : 'bg-light text-dark'; ?>"><?php echo ((int)($row['GhepBanDuoc'] ?? 0) === 1) ? 'Có' : 'Không'; ?></span></td>
            <td>
              <span class="badge <?php echo (($row['TrangThai'] ?? '') === 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                <?php echo htmlspecialchars($row['TrangThai'] ?? ''); ?>
              </span>
            </td>
            <td class="text-start"><?php echo htmlspecialchars((string)($row['GhiChu'] ?? '')); ?></td>
            <td>
              <div class="d-flex justify-content-center gap-2" role="group">
                <?php if ($canTableUpdate): ?>
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateTableModal" data-table="<?php echo $json; ?>" onclick="loadTableData(this)">
                    <i class="fas fa-edit"></i> Sửa
                  </button>
                <?php endif; ?>

                <?php if ($canTableDelete): ?>
                  <a class="btn btn-danger btn-sm" href="?page=admin&section=table&action=delete&MaBan=<?php echo (int)$row['MaBan']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bàn <?php echo htmlspecialchars($row['TenBan'] ?? '', ENT_QUOTES); ?> không?');">
                    <i class="fas fa-trash"></i> Xóa
                  </a>
                <?php endif; ?>

                <?php if (!$canTableUpdate && !$canTableDelete): ?>
                  <span class="badge bg-secondary">Chỉ xem</span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="12" class="text-center text-muted py-4">
            <i class="fas fa-utensils fa-3x mb-3"></i><br>
            Chưa có bàn nào theo bộ lọc hiện tại
          </td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($totalPages > 1): ?>
    <nav>
      <ul class="pagination justify-content-center">
        <li class="page-item <?php echo ($pageNumber <= 1) ? 'disabled' : ''; ?>">
          <a class="page-link" href="<?php echo ($pageNumber <= 1) ? '#' : buildQuery($baseParams, ['p' => $pageNumber - 1]); ?>">Trước</a>
        </li>

        <?php
        $startPage = max(1, $pageNumber - 2);
        $endPage = min($totalPages, $pageNumber + 2);

        if ($startPage > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . buildQuery($baseParams, ['p' => 1]) . '">1</a></li>';
            if ($startPage > 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = ($i === $pageNumber) ? 'active' : '';
            echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="' . buildQuery($baseParams, ['p' => $i]) . '">' . $i . '</a></li>';
        }

        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="' . buildQuery($baseParams, ['p' => $totalPages]) . '">' . $totalPages . '</a></li>';
        }
        ?>

        <li class="page-item <?php echo ($pageNumber >= $totalPages) ? 'disabled' : ''; ?>">
          <a class="page-link" href="<?php echo ($pageNumber >= $totalPages) ? '#' : buildQuery($baseParams, ['p' => $pageNumber + 1]); ?>">Sau</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<script>
(function () {
  const form = document.getElementById('tableFilterForm');
  if (!form) return;

  const autoFields = form.querySelectorAll('.js-table-filter-auto');
  autoFields.forEach(function (field) {
    field.addEventListener('change', function () {
      form.submit();
    });
  });

  const keywordInput = document.getElementById('tableKeywordInput');
  if (keywordInput) {
    keywordInput.addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        form.submit();
      }
    });
  }
})();
</script>








