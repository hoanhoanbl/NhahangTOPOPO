<?php
include dirname(__DIR__, 3) . "/config/connect.php";

// Auth controller cho RBAC sidebar
require_once dirname(__DIR__, 2) . '/controllers/AuthController.php';
$auth = isset($_SESSION['user']) ? new AuthController() : null;

// Lấy thống kê
if ($conn) {
    // Số lượng món ăn
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM monan");
    $totalMenuItems = mysqli_fetch_assoc($result)['total'];

    // Số lượng danh mục
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM danhmuc");
    $totalCategories = mysqli_fetch_assoc($result)['total'];

    // Số lượng nhân viên
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM nhanvien");
    $totalUsers = mysqli_fetch_assoc($result)['total'];

    // Số lượng cơ sở
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM coso");
    $totalBranches = mysqli_fetch_assoc($result)['total'];

    // Số admin
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM nhanvien WHERE ChucVu = 'admin'");
    $totalAdmins = mysqli_fetch_assoc($result)['total'];
} else {
    // Nếu lỗi kết nối database, đặt giá trị mặc định
    $totalMenuItems = 0;
    $totalCategories = 0;
    $totalUsers = 0;
    $totalBranches = 0;
    $totalAdmins = 0;
}

// Lấy tham số để quyết định hiển thị content nào
$section = $_GET['section'] ?? 'dashboard';
$requestedSection = $section;
$accessDeniedMessage = null;
$currentRole = $auth ? $auth->getCurrentRole() : '';
$isAdminRole = $auth ? $auth->isAdmin() : false;
$knownSections = ['dashboard', 'menu', 'menu_branch', 'categories', 'users', 'branches', 'table', 'booking', 'uudai'];
$roleSectionMap = [
    'admin' => $knownSections,
    'manager' => ['dashboard', 'menu', 'menu_branch', 'booking'],
    'receptionist' => ['dashboard', 'table', 'booking'],
];
$allowedSections = $roleSectionMap[$currentRole] ?? ['dashboard'];
$canAccessSection = static function (string $candidate, array $allowedSections): bool {
    return in_array($candidate, $allowedSections, true);
};

if ($section === 'menu' && !$isAdminRole && $canAccessSection('menu_branch', $allowedSections)) {
    $section = 'menu_branch';
}

if (!in_array($section, $knownSections, true)) {
    $section = 'dashboard';
} elseif (!$canAccessSection($section, $allowedSections)) {
    $accessDeniedMessage = 'B???n kh??ng c?? quy???n truy c???p m???c "' . htmlspecialchars($requestedSection) . '".';
    $section = 'dashboard';
}

$menuLinkSection = (!$isAdminRole && $canAccessSection('menu_branch', $allowedSections)) ? 'menu_branch' : 'menu';
$showMenu = $canAccessSection('menu', $allowedSections);
$showCategories = $canAccessSection('categories', $allowedSections);
$showUsers = $canAccessSection('users', $allowedSections);
$showBranches = $canAccessSection('branches', $allowedSections);
$showTable = $canAccessSection('table', $allowedSections);
$showBooking = $canAccessSection('booking', $allowedSections);
$showUudai = $canAccessSection('uudai', $allowedSections);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quản trị nhà hàng</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --colorPrimary: #1B4E30;
            --colorYellow: #FFA827;
            --colorLinkGreen: #1B4E30;
            --colorGrey: #D9D9D9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--colorPrimary) 0%, #2d6b47 100%);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar .brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            border: none;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border-right: 3px solid var(--colorYellow);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f8f9fa;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 0 2rem;
        }

        .content-area {
            padding: 2rem;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .quick-action {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1.5rem;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s ease;
        }

        .quick-action:hover {
            border-color: var(--colorLinkGreen);
            color: var(--colorLinkGreen);
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(33, 162, 86, 0.1);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }

            .sidebar.collapsed {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand">
            <h4 class="mb-0">
                <i class="fas fa-utensils me-2"></i>
                TOPOPO
            </h4>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $section === 'dashboard' ? 'active' : ''; ?>" href="?page=admin">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <?php if ($showMenu): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo in_array($section, ['menu', 'menu_branch'], true) ? 'active' : ''; ?>" href="?page=admin&section=<?php echo $menuLinkSection; ?>">
                        <i class="fas fa-utensils me-2"></i>
                        Quản lý Menu
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($showCategories): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'categories' ? 'active' : ''; ?>" href="?page=admin&section=categories">
                        <i class="fas fa-tags me-2"></i>
                        Quản lý Danh mục
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($showUsers): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'users' ? 'active' : ''; ?>" href="?page=admin&section=users">
                        <i class="fas fa-users me-2"></i>
                        Quản lý Nhân viên
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($showBranches): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'branches' ? 'active' : ''; ?>" href="?page=admin&section=branches">
                        <i class="fas fa-store me-2"></i>
                        Quản lý Cơ sở
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($showTable): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'table' ? 'active' : ''; ?>" href="?page=admin&section=table">
                        <i class="fas fa-chair me-2"></i>
                        Quản lý Bàn ăn
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($showBooking): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'booking' ? 'active' : ''; ?>" href="?page=admin&section=booking">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Quản lý Booking
                    </a>
                </li>
            <?php endif; ?>
            <?php if ($showUudai): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'uudai' ? 'active' : ''; ?>" href="?page=admin&section=uudai">
                        <i class="fas fa-percent me-2"></i>
                        Quản lý Ưu đãi
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item mt-3">
                <a class="nav-link" href="index.php" target="_blank">
                    <i class="fas fa-home me-2"></i>
                    Xem Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=auth&action=logout">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Đăng xuất
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn d-md-none me-2" id="sidebarToggleMobile" style="background-color: var(--colorLinkGreen); border-color: var(--colorLinkGreen); color: white;">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="btn d-none d-md-block me-3" id="sidebarToggleDesktop" style="background-color: var(--colorLinkGreen); border-color: var(--colorLinkGreen); color: white;" title="Ẩn/Hiện Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="h3 mb-0">
                    <?php
                    switch ($section) {
                        case 'menu':
                            echo 'Quản lý Menu';
                            break;
                        case 'menu_branch':
                            echo 'Quản lý menu theo cơ sở';
                            break;
                        case 'categories':
                            echo 'Quản lý Danh mục';
                            break;
                        case 'users':
                            echo 'Quản lý Nhân viên';
                            break;
                        case 'branches':
                            echo 'Quản lý Cơ sở';
                            break;
                        case 'table':
                            echo 'Quản lý Bàn ăn';
                            break;
                        case 'booking':
                            echo 'Quản lý Booking';
                            break;
                        case 'uudai':
                            echo 'Quản lý Ưu đãi';
                            break;
                        default:
                            echo 'Dashboard';
                            break;
                    }
                    ?>
                </h1>
            </div>

            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    Chào mừng, <?php echo htmlspecialchars($_SESSION['user']['TenNhanVien'] ?? 'Admin'); ?>!
                </span>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        style="background-color: var(--colorLinkGreen); border-color: var(--colorLinkGreen); color: white;">
                        <i class="fas fa-user-circle me-1"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?page=auth&action=profile"><i class="fas fa-user me-2" style="color: var(--colorPrimary);"></i>Thông tin cá nhân</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="?page=auth&action=logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            <?php if (!empty($accessDeniedMessage)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-lock me-2"></i><?php echo $accessDeniedMessage; ?>
                </div>
            <?php endif; ?>
            <?php
            switch ($section) {
                case 'menu':
                    include __DIR__ . '/menu/index.php';
                    break;

                case 'menu_branch':
                    include __DIR__ . '/menu_branch/index.php';
                    break;

                case 'categories':
                    include __DIR__ . '/categories/index.php';
                    break;

                case 'users':
                    include __DIR__ . '/user/index.php';
                    break;

                case 'branches':
                    include __DIR__ . '/branches/index.php';
                    break;

                case 'table':
                    include __DIR__ . '/table/index.php';
                    break;

                case 'booking':
                    include __DIR__ . '/booking/index.php';
                    break;

                case 'uudai':
                    include __DIR__ . '/uudai/index.php';
                    break;

                default: // dashboard
            ?>
                    <!-- Welcome Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-success border-0" style="background: linear-gradient(var(--colorLinkGreen) 0%, var(--colorLinkGreen) 100%);">
                                <div class="d-flex align-items-center text-white">
                                    <i class="fas fa-rocket fa-2x me-3"></i>
                                    <div>
                                        <h4 class="alert-heading mb-1">Chào mừng đến với Dashboard Quản trị!</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <?php if ($showMenu): ?>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon text-white me-3" style="background-color: var(--colorLinkGreen);">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Món ăn</h6>
                                                <h3 class="mb-0"><?php echo number_format($totalMenuItems); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($showCategories): ?>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon text-white me-3" style="background-color: var(--colorYellow);">
                                                <i class="fas fa-tags"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Danh mục</h6>
                                                <h3 class="mb-0"><?php echo number_format($totalCategories); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($showUsers): ?>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon text-white me-3" style="background-color: var(--colorPrimary);">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Nhân viên</h6>
                                                <h3 class="mb-0"><?php echo number_format($totalUsers); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($showBranches): ?>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card stat-card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="stat-icon text-white me-3" style="background-color: #E67E22;">
                                                <i class="fas fa-store"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Cơ sở</h6>
                                                <h3 class="mb-0"><?php echo number_format($totalBranches); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="mb-3">Thao tác nhanh</h4>
                        </div>

                        <?php if ($showMenu): ?>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?page=admin&section=<?php echo $menuLinkSection; ?>" class="quick-action d-block">
                                    <div class="text-center">
                                        <i class="fas fa-plus-circle fa-2x mb-2" style="color: var(--colorLinkGreen);"></i>
                                        <h6>Quản lý Menu</h6>
                                        <small class="text-muted">Quản lý thực đơn nhà hàng</small>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($showCategories): ?>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?page=admin&section=categories" class="quick-action d-block">
                                    <div class="text-center">
                                        <i class="fas fa-tag fa-2x mb-2" style="color: var(--colorYellow);"></i>
                                        <h6>Quản lý danh mục</h6>
                                        <small class="text-muted">Phân loại món ăn</small>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($showUsers): ?>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?page=admin&section=users" class="quick-action d-block">
                                    <div class="text-center">
                                        <i class="fas fa-user-plus fa-2x mb-2" style="color: var(--colorPrimary);"></i>
                                        <h6>Quản lý nhân viên</h6>
                                        <small class="text-muted">Quản lý tài khoản</small>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($showBranches): ?>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?page=admin&section=branches" class="quick-action d-block">
                                    <div class="text-center">
                                        <i class="fas fa-store fa-2x mb-2" style="color: var(--colorYellow);"></i>
                                        <h6>Quản lý cơ sở</h6>
                                        <small class="text-muted">Quản lý cơ sở</small>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($showTable): ?>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?page=admin&section=table" class="quick-action d-block">
                                    <div class="text-center">
                                        <i class="fas fa-table fa-2x mb-2" style="color: var(--colorYellow);"></i>
                                        <h6>Quản lý bàn ăn</h6>
                                        <small class="text-muted">Quản lý bàn ăn</small>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>


                        <?php if ($showUudai): ?>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <a href="?page=admin&section=uudai" class="quick-action d-block">
                                    <div class="text-center">
                                        <i class="fas fa-percent fa-2x mb-2" style="color: var(--colorYellow);"></i>
                                        <h6>Quản lý ưu đãi</h6>
                                        <small class="text-muted">Quản lý ưu đãi</small>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="index.php" target="_blank" class="quick-action d-block">
                                <div class="text-center">
                                    <i class="fas fa-eye fa-2x mb-2" style="color: #E67E22;"></i>
                                    <h6>Xem website</h6>
                                    <small class="text-muted">Giao diện khách hàng</small>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <!-- <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>
                                Phân bố theo danh mục
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Thống kê tổng quan
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="overviewChart"></canvas>
                        </div>
                    </div>
                </div>
            </div> -->
            <?php
                    break;
            }
            ?>
        </div>
    </main>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });

        <?php if ($section === 'dashboard'): ?>
            // Charts - chỉ hiển thị khi đang ở dashboard
            const categoryChart = document.getElementById('categoryChart');
            const overviewChart = document.getElementById('overviewChart');

            if (categoryChart) {
                const ctx1 = categoryChart.getContext('2d');
                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        labels: ['Món ăn', 'Danh mục', 'Nhân viên', 'Cơ sở'],
                        datasets: [{
                            data: [<?php echo $totalMenuItems; ?>, <?php echo $totalCategories; ?>, <?php echo $totalUsers; ?>, <?php echo $totalBranches; ?>],
                            backgroundColor: [
                                '#21A256',
                                '#FFA827',
                                '#1B4E30',
                                '#E67E22'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            if (overviewChart) {
                const ctx2 = overviewChart.getContext('2d');
                new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: ['Món ăn', 'Danh mục', 'Nhân viên', 'Cơ sở'],
                        datasets: [{
                            label: 'Số lượng',
                            data: [<?php echo $totalMenuItems; ?>, <?php echo $totalCategories; ?>, <?php echo $totalUsers; ?>, <?php echo $totalBranches; ?>],
                            backgroundColor: [
                                'rgba(33, 162, 86, 0.8)',
                                'rgba(255, 168, 39, 0.8)',
                                'rgba(27, 78, 48, 0.8)',
                                'rgba(230, 126, 34, 0.8)'
                            ],
                            borderColor: [
                                '#21A256',
                                '#FFA827',
                                '#1B4E30',
                                '#E67E22'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        <?php endif; ?>

        // Sidebar Toggle Functionality  
        const toggleDesktop = document.getElementById('sidebarToggleDesktop');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        if (toggleDesktop) {
            const toggleIcon = toggleDesktop.querySelector('i');

            toggleDesktop.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');

                // Đổi icon
                if (sidebar.classList.contains('collapsed')) {
                    toggleIcon.className = 'fas fa-chevron-right';
                } else {
                    toggleIcon.className = 'fas fa-bars';
                }

                // Lưu trạng thái
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });

            // Khôi phục trạng thái khi load trang
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                toggleIcon.className = 'fas fa-chevron-right';
            }
        }
    </script>
</body>

</html>