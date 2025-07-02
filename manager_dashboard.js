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
        if (!USER_RFID) return;
        const status = document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : 'all';
        const params = new URLSearchParams({
            status_name: status,
            rfid_tag: USER_RFID,
            role: USER_ROLE,
            department: USER_DEPARTMENT,
            section: USER_SECTION
        });
        fetch('get_reports.php?' + params.toString())
            .then(res => res.json())
            .then(reports => renderCards(reports));
    }

    function renderCards(reports) {
        cardsContainer.innerHTML = '';
        if (!reports || reports.length === 0) {
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
        if (!USER_RFID) return;
        const params = new URLSearchParams({
            rfid_tag: USER_RFID,
            from: fromDate.value,
            to: toDate.value
        });
        fetch('get_stats_manager.php?' + params.toString())
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

    searchBtn.addEventListener('click', loadDashboard);
    [fromDate, toDate].forEach(f => f.addEventListener('change', loadDashboard));
    loadDashboard();
}); 