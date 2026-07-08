/* =============================================
   FILE: script.js
   FUNGSI: JavaScript untuk interaktivitas
   ============================================= */

// ===== ISLAMIC ANIMATED STARS =====
document.addEventListener('DOMContentLoaded', function() {
    // Buat bintang berkilau
    const starsContainer = document.querySelector('.stars');
    if (starsContainer) {
        for (let i = 0; i < 50; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.width = (Math.random() * 3 + 1) + 'px';
            star.style.height = star.style.width;
            star.style.animationDelay = Math.random() * 3 + 's';
            star.style.animationDuration = (Math.random() * 2 + 2) + 's';
            starsContainer.appendChild(star);
        }
    }

    // Buat shooting stars
    const body = document.body;
    for (let i = 0; i < 3; i++) {
        const shooting = document.createElement('div');
        shooting.className = 'shooting-star';
        shooting.style.top = (10 + Math.random() * 70) + '%';
        shooting.style.animationDelay = (i * 2) + 's';
        body.appendChild(shooting);
    }

    // Buat geometric pattern
    const pattern = document.createElement('div');
    pattern.className = 'geometric-pattern';
    body.appendChild(pattern);

    // Set active nav based on current page
    setActiveNav();
});

// ===== SET ACTIVE NAV =====
function setActiveNav() {
    const currentPage = window.location.pathname.split('/').pop();
    const navItems = document.querySelectorAll('.bottom-navbar .nav-item');
    
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href) {
            const page = href.split('/').pop();
            if (page === currentPage || (currentPage === '' && page === 'dashboard.php')) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        }
    });
}

// ===== VALIDASI FORM =====
(function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

// ===== KONFIRMASI HAPUS =====
function konfirmasiHapus(url, pesan) {
    if (confirm(pesan || 'Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = url;
    }
    return false;
}

// ===== SEARCH AUTO SUBMIT =====
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let timer;
        searchInput.addEventListener('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
});

// ===== FILTER AUTO SUBMIT =====
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('select[name^="filter_"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
    
    const dateInput = document.querySelector('input[name="filter_tanggal"]');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            this.form.submit();
        });
    }
});

// ===== NOTIFICATION AUTO CLOSE =====
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeBtn = alert.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.click();
            }
        }, 5000);
    });
});

// ===== ISLAMIC DATE DISPLAY =====
function getIslamicDate() {
    const now = new Date();
    const islamicMonths = [
        'Muharram', 'Safar', 'Rabi\'ul Awwal', 'Rabi\'ul Akhir',
        'Jumadil Ula', 'Jumadil Akhir', 'Rajab', 'Sya\'ban',
        'Ramadhan', 'Syawal', 'Dzulqa\'dah', 'Dzulhijjah'
    ];
    // Konversi sederhana (perkiraan)
    const hijriYear = 1445;
    const hijriMonth = Math.floor((now.getMonth() + 1) % 12);
    const hijriDay = now.getDate() % 30;
    return `${hijriDay} ${islamicMonths[hijriMonth]} ${hijriYear} H`;
}

// ===== COUNTER ANIMATION =====
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        if (target > 0) {
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.round(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target;
                }
            };
            
            updateCounter();
        }
    });
});

// ===== EXPORT TO CSV =====
function exportCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td, th');
        const rowData = [];
        cells.forEach(cell => {
            rowData.push(cell.textContent.trim());
        });
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'data.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// ===== PRINT TABLE =====
function printTable() {
    window.print();
}

// ===== TOGGLE SIDEBAR MOBILE =====
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('show');
    }
}