// admin_dashboard.js

document.addEventListener('DOMContentLoaded', function () {
    // عناصر الفلاتر
    const departmentFilter = document.getElementById('departmentFilter');
    const sectionFilter = document.getElementById('sectionFilter');
    const employeeFilter = document.getElementById('employeeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchBtn = document.getElementById('searchBtn');
    const cardsContainer = document.getElementById('cardsContainer');
    const statsBar = document.getElementById('statsBar');

    // تحميل الفلاتر (الإدارات، الأقسام، الموظفين)
    function loadFilters() {
        // جلب الإدارات
        fetch('get_departments.php')
            .then(res => res.json())
            .then(departments => {
                console.log(departments); // لمراقبة شكل البيانات
                departmentFilter.innerHTML = '<option value="">كل الإدارات</option>';
                departments.forEach(dep => {
                    departmentFilter.innerHTML += `<option value="${dep.department_id}">${dep.department_name}</option>`;
                });
            });
        // عند تغيير الإدارة، جلب الأقسام
        departmentFilter.addEventListener('change', function () {
            fetch('get_sections.php?department_id=' + departmentFilter.value)
                .then(res => res.json())
                .then(sections => {
                    sectionFilter.innerHTML = '<option value="">كل الأقسام</option>';
                    sections.forEach(sec => {
                        sectionFilter.innerHTML += `<option value="${sec.section_id}">${sec.section_name}</option>`;
                    });
                });
        });
        // عند تغيير القسم، جلب الموظفين
        sectionFilter.addEventListener('change', function () {
            fetch('get_employees.php?section_id=' + sectionFilter.value)
                .then(res => res.json())
                .then(emps => {
                    employeeFilter.innerHTML = '<option value="">كل الموظفين</option>';
                    emps.forEach(emp => {
                        employeeFilter.innerHTML += `<option value="${emp.employee_id}">${emp.name}</option>`;
                    });
                });
        });
    }

    // جلب البلاغات مع الفلاتر
    function loadReports() {
        const params = new URLSearchParams({
            department_id: departmentFilter.value,
            section_id: sectionFilter.value,
            employee_id: employeeFilter.value,
            status: statusFilter.value
        });
        fetch('get_reports_admin.php?' + params.toString())
            .then(res => res.json())
            .then(data => {
                renderStats(data.stats);
                renderCards(data.reports);
            });
    }

    // عرض الإحصائيات
    function renderStats(stats) {
        statsBar.innerHTML = '';
        if (!stats) return;
        statsBar.innerHTML += `<div class="stat-box">جديد: ${stats.new || 0}</div>`;
        statsBar.innerHTML += `<div class="stat-box">تحت الإجراء: ${stats.in_progress || 0}</div>`;
        statsBar.innerHTML += `<div class="stat-box">بانتظار التأكيد: ${stats.in_wating || 0}</div>`;
        statsBar.innerHTML += `<div class="stat-box">مكتمل: ${stats.completed || 0}</div>`;
        statsBar.innerHTML += `<div class="stat-box">مغلق: ${stats.closed || 0}</div>`;
    }

    // جلب الموظفين للقسم/الإدارة
    function fetchEmployees(sectionId, callback) {
        fetch('get_employees.php?section_id=' + sectionId)
            .then(res => res.json())
            .then(callback);
    }

    // عرض البطاقات
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
                <p><strong>الإدارة:</strong> ${report.department_name || report.department}</p>
                <p><strong>القسم:</strong> ${report.section_name || report.section}</p>
                <p><strong>الموظف المستلم:</strong> <span class="assigned-to">${report.assigned_to_name || '-'}</span></p>
                <p><strong>الوصف:</strong> ${report.description}</p>
                <p><strong>الحالة:</strong> ${getStatusText(report.status_name)}</p>
                <p><strong>تاريخ الإنشاء:</strong> ${formatDate(report.report_date)}</p>
                <p><strong>تعليقات:</strong> ${report.comments || '-'}</p>
                <div class="card-actions">
                    <button onclick="confirmReport(${report.report_id})">تأكيد حل البلاغ</button>
                    <button onclick="forceCompleteReport(${report.report_id})">إكمال البلاغ فورًا</button>
                    <span class="assign-employee-container"></span>
                </div>
            `;
            // إضافة قائمة الموظفين وزر التعيين
            const assignContainer = card.querySelector('.assign-employee-container');
            // فقط إذا كان البلاغ لم يتم تعيينه أو يمكن تغييره
            if (report.status_name !== 'completed' && report.status_name !== 'closed') {
                fetchEmployees(report.section_id || report.section, function (employees) {
                    const select = document.createElement('select');
                    select.innerHTML = '<option value="">اختر الموظف</option>';
                    employees.forEach(emp => {
                        select.innerHTML += `<option value="${emp.employee_id}">${emp.name}</option>`;
                    });
                    const assignBtn = document.createElement('button');
                    assignBtn.textContent = 'تسليم البلاغ';
                    assignBtn.onclick = function () {
                        if (!select.value) { alert('يرجى اختيار الموظف أولاً'); return; }
                        assignBtn.disabled = true;
                        assignBtn.textContent = '...جاري التسليم';
                        fetch('assign_report.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ report_id: report.report_id, employee_id: select.value })
                        })
                        .then(res => res.json())
                        .then(data => {
                            alert(data.message);
                            loadReports();
                        })
                        .catch(() => {
                            alert('حدث خطأ أثناء التسليم');
                            assignBtn.disabled = false;
                            assignBtn.textContent = 'تسليم البلاغ';
                        });
                    };
                    assignContainer.appendChild(select);
                    assignContainer.appendChild(assignBtn);
                });
            }
            cardsContainer.appendChild(card);
        });
    }

    // ترجمة الحالة
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

    // تنسيق التاريخ
    function formatDate(dateStr) {
        if (!dateStr) return '-';
        // معالجة التواريخ بتنسيق SQL Server (مثلاً: 2024-07-01 13:00:00.000)
        let d = null;
        if (typeof dateStr === 'string' && dateStr.includes(' ')) {
            // استبدل المسافة بين التاريخ والوقت بحرف T ليصبح ISO
            d = new Date(dateStr.replace(' ', 'T'));
        } else {
            d = new Date(dateStr);
        }
        if (isNaN(d.getTime())) return '-';
        return d.toLocaleString('ar-EG');
    }

    // زر تأكيد حل البلاغ (الطريقة التاكيدية)
    window.confirmReport = function (report_id) {
        if (!confirm('هل تريد تأكيد حل البلاغ بالطريقة التاكيدية؟')) return;
        fetch('update_report_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ report_id, action: 'confirm' })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadReports();
        });
    };

    // زر إكمال البلاغ فورًا (تخطي موافقة المرسل)
    window.forceCompleteReport = function (report_id) {
        if (!confirm('هل تريد إكمال البلاغ مباشرة دون انتظار موافقة المرسل؟')) return;
        fetch('update_report_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ report_id, action: 'force_complete' })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            loadReports();
        });
    };

    // تحميل الفلاتر عند بدء الصفحة
    loadFilters();
    // تحميل البلاغات عند بدء الصفحة أو عند الضغط على بحث
    searchBtn.addEventListener('click', loadReports);
    // تحميل البلاغات تلقائيًا عند تغيير الفلاتر
    [departmentFilter, sectionFilter, employeeFilter, statusFilter].forEach(f => f.addEventListener('change', loadReports));
    // تحميل أولي
    loadReports();
}); 