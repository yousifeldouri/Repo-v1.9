document.addEventListener('DOMContentLoaded', function () {
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    const searchBtn = document.getElementById('searchBtn');
    const cardsContainer = document.getElementById('cardsContainer');
    let statsChart;

    // تعريف المتغيرات من PHP أو من الجلسة
    const USER_ROLE = window.USER_ROLE || '';
    const USER_DEPARTMENT = window.USER_DEPARTMENT || '';
    const USER_SECTION = window.USER_SECTION || '';
    let USER_RFID = null;

    // جلب rfid_tag أولاً
    fetch('get_rfid.php')
      .then(res => res.json())
      .then(data => {
        if (data.success && data.rfid_tag) {
          USER_RFID = data.rfid_tag;
          loadReports();
        } else {
          alert('لم يتم التعرف على المستخدم. يرجى تسجيل الدخول مجددًا.');
        }
      });

    function loadDashboard() {
        loadCards();
        loadChart();
    }

    function loadCards() {
        // جلب بيانات البطاقات (البلاغات)
        let head_id = sessionStorage.getItem('user_id') || localStorage.getItem('user_id');
        if (!head_id) {
            cardsContainer.innerHTML = '<p>لم يتم التعرف على المستخدم. يرجى تسجيل الدخول مجددًا.</p>';
            return;
        }
        const params = new URLSearchParams({
            head_id: head_id,
            from: fromDate.value,
            to: toDate.value
        });
        fetch('get_reports_head.php?' + params.toString())
            .then(res => res.json())
            .then(reports => renderCards(reports));
    }

    function renderCards(reports) {
        cardsContainer.innerHTML = '';
        // Debug log to help trace issues
        console.log('renderCards received:', reports, 'Is array:', Array.isArray(reports));
        if (!Array.isArray(reports)) {
            if (reports && typeof reports === 'object' && reports.error) {
                cardsContainer.innerHTML = `<p style="color:red;">${reports.error}</p>`;
            } else if (reports === null || reports === undefined) {
                cardsContainer.innerHTML = '<p style="color:red;">حدث خطأ في جلب البيانات من الخادم.</p>';
            } else {
                cardsContainer.innerHTML = '<p style="color:red;">البيانات المستلمة غير متوقعة. يرجى التواصل مع الدعم.</p>';
            }
            return;
        }
        if (reports.length === 0) {
            cardsContainer.innerHTML = '<p>لا توجد بلاغات مطابقة للفلتر.</p>';
            return;
        }
        reports.forEach(report => {
            const card = document.createElement('div');
            card.className = 'report-card';
            card.innerHTML = `
                <div class="status ${report.status_name}">${getStatusText(report.status_name)}</div>
                <h3>بلاغ رقم: ${report.report_id}</h3>
                <p><strong>القسم:</strong> ${report.section}</p>
                <p><strong>الوصف:</strong> ${report.description}</p>
                <p><strong>الحالة:</strong> ${getStatusText(report.status_name)}</p>
                <p><strong>تاريخ الإنشاء:</strong> ${formatDate(report.report_date)}</p>
                <p><strong>الموظف المستلم:</strong> ${report.assigned_to_name || '-'}</p>
                <p><strong>تعليقات:</strong> ${report.comments || '-'}</p>
            `;
            cardsContainer.appendChild(card);
        });
    }

    function loadChart() {
        // جلب بيانات الرسم البياني
        let head_id = sessionStorage.getItem('user_id') || localStorage.getItem('user_id');
        if (!head_id) return;
        const params = new URLSearchParams({
            head_id: head_id,
            from: fromDate.value,
            to: toDate.value
        });
        fetch('get_stats_head.php?' + params.toString())
            .then(res => res.json())
            .then(data => renderChart(data));
    }

    function renderChart(data) {
        const ctx = document.getElementById('statsChart').getContext('2d');
        if (statsChart) statsChart.destroy();
        statsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'عدد البلاغات المنجزة',
                    data: data.counts,
                    backgroundColor: '#1c5fac',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'عدد البلاغات المنجزة حسب الموظف' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function getStatusText(status) {
        switch (status) {
            case 'new': return 'جديد';
            case 'in_progress': return 'تحت الإجراء';
            case 'in_wating': return 'بانتظار التأكيد';
            case 'completed': return 'مكتمل';
            case 'closed': return 'مغلق';
            default: return status;
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleString('ar-EG');
    }

    function loadReports() {
        if (!USER_RFID) return;
        const status = document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : 'all';
        fetch('get_reports.php?status_name=' + status +
              '&rfid_tag=' + USER_RFID +
              '&role=' + USER_ROLE +
              '&department=' + USER_DEPARTMENT +
              '&section=' + USER_SECTION)
            .then(res => res.json())
            .then(reports => renderCards(reports));
    }

    searchBtn.addEventListener('click', loadDashboard);
    [fromDate, toDate].forEach(f => f.addEventListener('change', loadDashboard));
    loadDashboard();
}); 