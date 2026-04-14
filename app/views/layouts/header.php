
<header>
    <div class="main-header">
        <div class="container">
            <a href="<?php echo url('/'); ?>" class="logo">
                <i class="fas fa-star" aria-hidden="true"></i>
                <div class="logo-text">
                    <span class="logo-top">TOPOPO </span>
                </div>
            </a>
            
            <div class="hotline">
                <p>HOTLINE</p>
                <strong>*1986</strong>
            </div>
            <nav class="main-nav" role="navigation" aria-label="Main navigation">
                <ul>
                    <li><a href="<?php echo url('?page=menu'); ?>" class="<?php echo isActivePage('menu'); ?>">THỰC ĐƠN</a></li>
                    <li><a href="<?php echo url('?page=branches'); ?>" class="<?php echo isActivePage('branches'); ?>">CƠ SỞ</a></li>
                    <li><a href="<?php echo url('?page=booking&action=lookup'); ?>" class="<?php echo (($_GET['page'] ?? '') === 'booking' && ($_GET['action'] ?? '') === 'lookup') ? 'active' : ''; ?>">TRA C&#7912;U &#272;&#7862;T B&#192;N</a></li>
                    <li><a href="<?php echo url('?page=admin'); ?>" class="<?php echo isActivePage('promotions'); ?>">QUẢN TRỊ</a></li>
                    <li><a href="<?php echo url('?page=contact'); ?>" class="<?php echo isActivePage('contact'); ?>">LIÊN HỆ</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">    
                <!-- Dropdown chọn cơ sở trong header -->
                <div class="header-branch-selector" aria-label="Chọn cơ sở">
                    <button id="headerBranchBtn" class="change-branch-btn">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        <span id="headerCurrentBranchName">Chọn cơ sở</span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div id="headerBranchDropdown" class="header-branch-dropdown" role="menu" aria-hidden="true"></div>
                </div>

                <a href="<?php echo url('?page=menu2'); ?>" class="btn-booking" role="button">ĐẶT BÀN</a>
            </div>
        </div>
    </div>
    
    <script>
    // JS: Load danh sách cơ sở và chọn cơ sở từ header
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('headerBranchBtn');
        const nameSpan = document.getElementById('headerCurrentBranchName');
        const dropdown = document.getElementById('headerBranchDropdown');

        // Hiển thị tên cơ sở đã chọn nếu có
        const saved = localStorage.getItem('selectedBranch');
        if (saved) {
            const data = JSON.parse(saved);
            nameSpan.textContent = data.name;
        }

        // Toggle dropdown
        btn && btn.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpen = dropdown.getAttribute('aria-hidden') === 'false';
            dropdown.setAttribute('aria-hidden', isOpen ? 'true' : 'false');
        });

        // Đóng khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.header-branch-selector')) {
                dropdown.setAttribute('aria-hidden', 'true');
            }
        });

        // Tải danh sách cơ sở
        fetch('<?php echo url('?page=menu&action=branches'); ?>')
            .then(r => r.json())
            .then(json => {
                if (!json.success || !Array.isArray(json.data)) return;
                dropdown.innerHTML = json.data.map(item => `
                    <div class="header-branch-item" data-id="${item.MaCoSo}">
                        <div class="hbi-name">${item.TenCoSo}</div>
                        <div class="hbi-addr">${item.DiaChi}</div>
                    </div>
                `).join('');

                dropdown.querySelectorAll('.header-branch-item').forEach(el => {
                    el.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const n = this.querySelector('.hbi-name').textContent;
                        const a = this.querySelector('.hbi-addr').textContent;
                        localStorage.setItem('selectedBranch', JSON.stringify({ id, name: n, address: a, timestamp: Date.now() }));
                        nameSpan.textContent = n;
                        dropdown.setAttribute('aria-hidden', 'true');
                        // Nếu đang ở trang menu thì chuyển thẳng sang menu2
                        const onMenuPage = window.location.search.includes('page=menu');
                        if (onMenuPage) {
                            window.location.href = '<?php echo url('?page=menu2'); ?>&coso=' + id;
                        }
                    });
                });
            })
            .catch(() => {});
    });
    </script>
