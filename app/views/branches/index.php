<link rel="stylesheet" href="public/css/pages/branches.css">


<main class="branches-page">

    <section class="tab-section-branches">
        <div class="container">
            <div class="tab-navigation">
                <button class="tab-btn active" data-district="all">
                    <span class="tab-text">TẤT CẢ</span>
                </button>
                <?php foreach($branch_districts as $district): ?>
                <button class="tab-btn" data-district="<?php echo $district; ?>">
                    <span class="tab-text"><?php echo strtoupper($district); ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<section class="branches-section">
  <div class="container">
    <div class="branches-grid" id="branchesGrid">
      <?php foreach ($branches as $branch): ?>
        <div class="branch-card" data-district="<?php echo strtolower(str_replace(' ', '-', $branch['district'])); ?>">
         
          <!-- Nội dung bên trái -->
          <div class="branch-content">
            <div class="branch-header">
              <h3 class="branch-name"><?php echo $branch['name']; ?></h3>
            </div>

            <div class="branch-actions">
              <a href="https://maps.google.com/?q=<?php echo urlencode($branch['district']); ?>" target="_blank" class="btn btn-outline">
                <i class="fas fa-map-marker-alt"></i>
                Xem bản đồ
              </a>

              <a href="?page=menu2&action=menu2&coso=<?php echo $branch['id']; ?>" class="btn btn-outline">
                <i class="fas fa-utensils"></i>
                Xem menu
              </a>
              <button class="btn btn-outline" onclick="openBookingModal(<?php echo $branch['id']; ?>)">
                <i class="fas fa-calendar-check"></i>
                Đặt bàn ngay
              </button>
            </div>
             <div class="info-item">
                <i class="fas fa-phone"></i>
                <span><?php echo $branch['hotline']; ?></span>
              </div>
          </div> <!-- end branch-content -->

          <!-- Ảnh bên phải -->
          <div class="branch-image">
            <img src="<?php echo $branch['image']; ?>" alt="<?php echo $branch['name']; ?>" loading="lazy">
          </div>

        </div> <!-- end branch-card -->
        
      <?php endforeach; ?>
    </div>

    <?php if (empty($branches)): ?>
      <div class="no-results">
        <i class="fas fa-search"></i>
        <h3>Không tìm thấy cơ sở nào</h3>
        <p>Vui lòng thử lại với bộ lọc khác</p>
      </div>
    <?php endif; ?>
  </div>
</section>
</main>

<script>
// Data and tab management
let currentAddress = 'all';
let allDistricts = <?php echo json_encode($branch_districts); ?>;

// Initialize functionality
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
});

function initializeTabs() {
    // Add tab click listeners
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            const address = this.dataset.district || 'all';
            switchTab(address);
        });
        
        // Add hover effects
        button.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(-2px)';
            }
        });
        button.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Set initial state
    switchTab('all');
}

function switchTab(address) {
    currentAddress = address;
    
    // Update active tab
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    const targetTab = address === 'all' 
        ? document.querySelector('[data-district="all"]') 
        : document.querySelector(`[data-district="${address}"]`);
    
    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    // Load data from server
    loadBranchData(address);
    
    // Update URL without reload
    const url = new URL(window.location);
    if (address === 'all') {
        url.searchParams.delete('district');
    } else {
        url.searchParams.set('district', address);
    }
    window.history.pushState({}, '', url);
}

function loadBranchData(address) {
    const grid = document.getElementById('branchesGrid');
    
    // Show loading state
    grid.innerHTML = '<div class="loading-state"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</div>';
    
    // Call API
    const apiUrl = `?page=branches&action=api&address=${encodeURIComponent(address)}`;
    
    fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderBranches(data.data);
            } else {
                showError('Không thể tải dữ liệu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Lỗi kết nối');
        });
}

function renderBranches(branches) {
    const grid = document.getElementById('branchesGrid');
    
    if (branches.length === 0) {
        grid.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>Không tìm thấy cơ sở nào</h3>
                <p>Chưa có cơ sở nào tại địa chỉ này</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    branches.forEach(branch => {
        html += `
            <div class="branch-card" data-district="${branch.district.toLowerCase()}">
                
             

            <div class="branch-content">
                    <div class="branch-header">
                        <h3 class="branch-name">${branch.name}</h3>
                    </div>
                               
                    <div class="branch-actions">
                            <a href="https://maps.google.com/?q=${encodeURIComponent(branch.district)}" target="_blank" class="btn btn-outline">
                                <i class="fas fa-map-marker-alt"></i>
                                Xem bản đồ
                            </a>
                                      <a href="?page=menu2&action=menu2&coso=${branch.id}" class="btn btn-outline">
                            <i class="fas fa-utensils"></i>
                            Xem menu
                        </a>
                        <button class="btn btn-outline" onclick="openBookingModal(${branch.id})">
                            <i class="fas fa-calendar-check"></i>
                            Đặt bàn ngay
                        </button>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <span>${branch.hotline}</span>
                    </div>
                </div>
                <div class="branch-image">
                    <img src="${branch.image}" alt="${branch.name}" loading="lazy">
                </div>
               
            </div>
        `;
    });
    
    grid.innerHTML = html;
    
    // Add animation
    const cards = grid.querySelectorAll('.branch-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

function showError(message) {
    const grid = document.getElementById('branchesGrid');
    grid.innerHTML = `
        <div class="error-state">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Có lỗi xảy ra</h3>
            <p>${message}</p>
            <button onclick="loadBranchData(currentAddress)" class="btn btn-primary">Thử lại</button>
        </div>
    `;
}

// Modal functions (nếu cần)
function openBookingModal(branchId) {
    // Implement booking modal logic
    console.log('Open booking modal for branch:', branchId);
}

// Add CSS for loading and error states
const style = document.createElement('style');
style.textContent = `
    .loading-state, .error-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    .loading-state i, .error-state i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #ddd;
    }
    .fa-spin {
        animation: fa-spin 2s infinite linear;
    }
    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>


