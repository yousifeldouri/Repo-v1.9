document.addEventListener('DOMContentLoaded', function () {
    const statusFilter = document.getElementById('statusFilter');
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    const searchBtn = document.getElementById('searchBtn');
    const cardsContainer = document.getElementById('cardsContainer');

    function loadReports() {
        // جلب rfid_tag من الجلسة أو localStorage (حسب النظام)
        let rfid_tag = sessionStorage.getItem('rfid_tag') || localStorage.getItem('rfid_tag');
        if (!rfid_tag) {
            cardsContainer.innerHTML = '<p>لم يتم التعرف على المستخدم. يرجى تسجيل الدخول مجددًا.</p>';
            return;
        }
        const params = new URLSearchParams({
            rfid_tag: rfid_tag,
            status: statusFilter.value,
            from: fromDate.value,
            to: toDate.value
        });
        fetch('get_my_reports.php?' + params.toString())
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
                <p><strong>الإدارة:</strong> ${report.department}</p>
                <p><strong>القسم:</strong> ${report.section}</p>
                <p><strong>الوصف:</strong> ${report.description}</p>
                <p><strong>الحالة:</strong> ${getStatusText(report.status_name)}</p>
                <p><strong>تاريخ الإنشاء:</strong> ${formatDate(report.report_date)}</p>
                <p><strong>الموظف المستلم:</strong> ${report.assigned_to_name || '-'}</p>
                <p><strong>تعليقات:</strong> ${report.comments || '-'}</p>
                <div class="card-actions">
                    ${report.status_name === 'in_wating' ? `<button onclick="confirmSolved(${report.report_id})">تأكيد حل البلاغ</button>` : ''}
                </div>
            `;
            cardsContainer.appendChild(card);
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

    window.confirmSolved = function (report_id) {
        if (!confirm('هل تريد تأكيد حل البلاغ؟')) return;
        fetch('update_report_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ report_id, action: 'owner_confirm' })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadReports();
        });
    };

    searchBtn.addEventListener('click', loadReports);
    [statusFilter, fromDate, toDate].forEach(f => f.addEventListener('change', loadReports));
    loadReports();
}); 