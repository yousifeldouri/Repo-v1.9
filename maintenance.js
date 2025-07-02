document.addEventListener('DOMContentLoaded', () => {
    // تحقق مركزي: إذا لم يوجد الجدول، لا تنفذ أي شيء
    const table = document.getElementById('reportsTable');
    if (!table) return;

    // الجزء الخاص بـ RFID
    fetch('get_rfid.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const rfid_tag = data.rfid_tag;

                document.getElementById('filterStatus').addEventListener('change', () => {
                    const selectedStatus = document.getElementById('filterStatus').value;
                    fetch(`get_reports.php?status_name=${selectedStatus}&rfid_tag=${rfid_tag}`)
                        .then(response => response.json())
                        .then(data => {
                            const tableBody = table.getElementsByTagName('tbody')[0];
                            tableBody.innerHTML = ''; // مسح الجدول الحالي
                            let currentDate = '';
                            data.forEach(report => {
                                const reportDate = report.report_date ? formatDate(report.report_date) : 'غير متوفر'; // تأكد من عدم كونها null
                                const reciveDate = report.recive_date ? formatDate(report.recive_date) : 'غير متوفر'; // تأكد من عدم كونها null

                                if (reportDate !== currentDate) {
                                    const dateRow = tableBody.insertRow();
                                    const dateCell = dateRow.insertCell(0);
                                    dateCell.colSpan = 9; // زيادة عدد الأعمدة في حالة وجود عمود جديد
                                    dateCell.innerText = reportDate;
                                    dateCell.style.fontWeight = 'bold';
                                    currentDate = reportDate;
                                }

                                const row = tableBody.insertRow();
                                row.insertCell(0).innerText = report.report_id;
                                row.insertCell(1).innerText = report.section;
                                row.insertCell(2).innerText = report.description;
                                row.insertCell(3).innerText = report.iso_values;
                                row.insertCell(4).innerText = report.status_name;
                                row.insertCell(5).innerText = report.comments;
                                row.insertCell(6).innerText = reportDate; // استخدم القيمة المنسقة
                                row.insertCell(7).innerText = reciveDate; // استخدم القيمة المنسقة
                                const actionsCell = row.insertCell(row.cells.length);



                                const updateButton = document.createElement('button');
                                updateButton.innerText = 'Update';
                                updateButton.addEventListener('click', () => {
                                    // تعريف خيارات الحالة
                                    const statusOptions = {
                                        '1': 'New',
                                        '2': 'In Progress',
                                        '3': 'Completed'
                                    };

                                    const modal = document.createElement('div');
                                    modal.style.position = 'fixed';
                                    modal.style.top = '50%';
                                    modal.style.left = '50%';
                                    modal.style.transform = 'translate(-50%, -50%)';
                                    modal.style.backgroundColor = 'white';
                                    modal.style.padding = '20px';
                                    modal.style.boxShadow = '0 2px 10px rgba(0,0,0,0.5)';
                                    modal.style.zIndex = '1000';

                                    const statusSelect = document.createElement('select');
                                    for (const [key, value] of Object.entries(statusOptions)) {
                                        const option = document.createElement('option');
                                        option.value = key;
                                        option.textContent = value;
                                        statusSelect.appendChild(option);
                                    }

                                    const newComment = document.createElement('textarea');
                                    newComment.placeholder = 'Enter your comment:';
                                    newComment.rows = 4;
                                    newComment.cols = 30;

                                    const confirmButton = document.createElement('button');
                                    confirmButton.innerText = 'Confirm';
                                    confirmButton.addEventListener('click', () => {
                                        const confirmation = confirm('Are you sure you want to change the status?');
                                        if (confirmation) {
                                            fetch('update_report.php', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json'
                                                },
                                                body: JSON.stringify({
                                                    reportID: report.report_id,
                                                    status_name: statusSelect.value,
                                                    comments: newComment.value,
                                                    reportDate: report.report_date
                                                })
                                            }).then(response => response.json())
                                                .then(data => {
                                                    row.className = statusSelect.value === '1' ? 'new' : (statusSelect.value === '2' ? 'in_progress' : 'completed');
                                                    row.cells[4].innerText = statusSelect.value === '1' ? 'New' : (statusSelect.value === '2' ? 'In Progress' : 'Completed');
                                                    row.cells[5].innerText = newComment.value;
                                                    document.body.removeChild(modal);
                                                })
                                                .catch(error => console.error('Error:', error));
                                        } else {
                                            document.body.removeChild(modal);
                                        }
                                    });

                                    modal.appendChild(statusSelect);
                                    modal.appendChild(newComment);
                                    modal.appendChild(confirmButton);
                                    document.body.appendChild(modal);
                                });

                                actionsCell.appendChild(updateButton);
                                row.className = report.status_name.toLowerCase();
                            });
                        })
                        .catch(error => console.error('Error:', error));
                });

                fetch(`get_reports.php?rfid_tag=${rfid_tag}`)
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = table.getElementsByTagName('tbody')[0];
                        let currentDate = '';
                        data.forEach(report => {
                            const reportDate = report.report_date ? formatDate(report.report_date) : 'غير متوفر'; // تأكد من عدم كونها null
                            const reciveDate = report.recive_date ? formatDate(report.recive_date) : 'غير متوفر'; // تأكد من عدم كونها null

                            if (reportDate !== currentDate) {
                                const dateRow = tableBody.insertRow();
                                const dateCell = dateRow.insertCell(0);
                                dateCell.colSpan = 9; // زيادة عدد الأعمدة في حالة وجود عمود جديد
                                dateCell.innerText = reportDate;
                                dateCell.style.fontWeight = 'bold';
                                currentDate = reportDate;
                            }

                            const row = tableBody.insertRow();
                            row.insertCell(0).innerText = report.report_id;
                            row.insertCell(1).innerText = report.section;
                            row.insertCell(2).innerText = report.description;
                            row.insertCell(3).innerText = report.iso_values;
                            row.insertCell(4).innerText = report.status_name;
                            row.insertCell(5).innerText = report.comments;
                            row.insertCell(6).innerText = reportDate; // استخدم القيمة المنسقة
                            row.insertCell(7).innerText = reciveDate; // استخدم القيمة المنسقة
                            const actionsCell = row.insertCell(row.cells.length);



                            const updateButton = document.createElement('button');
                            updateButton.innerText = 'Update';
                            updateButton.addEventListener('click', () => {
                                // تعريف خيارات الحالة
                                const statusOptions = {
                                    '1': 'New',
                                    '2': 'In Progress',
                                    '3': 'Completed'
                                };

                                const modal = document.createElement('div');
                                modal.style.position = 'fixed';
                                modal.style.top = '50%';
                                modal.style.left = '50%';
                                modal.style.transform = 'translate(-50%, -50%)';
                                modal.style.backgroundColor = 'white';
                                modal.style.padding = '20px';
                                modal.style.boxShadow = '0 2px 10px rgba(0,0,0,0.5)';
                                modal.style.zIndex = '1000';

                                const statusSelect = document.createElement('select');
                                for (const [key, value] of Object.entries(statusOptions)) {
                                    const option = document.createElement('option');
                                    option.value = key;
                                    option.textContent = value;
                                    statusSelect.appendChild(option);
                                }

                                const newComment = document.createElement('textarea');
                                newComment.placeholder = 'Enter your comment:';
                                newComment.rows = 4;
                                newComment.cols = 30;

                                const confirmButton = document.createElement('button');
                                confirmButton.innerText = 'Confirm';
                                confirmButton.addEventListener('click', () => {
                                    const confirmation = confirm('Are you sure you want to change the status?');
                                    if (confirmation) {
                                        fetch('update_report.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                reportID: report.report_id,
                                                status_name: statusSelect.value,
                                                comments: newComment.value,
                                                reportDate: report.report_date
                                            })
                                        }).then(response => response.json())
                                            .then(data => {
                                                row.className = statusSelect.value === '1' ? 'new' : (statusSelect.value === '2' ? 'in_progress' : 'completed');
                                                row.cells[4].innerText = statusSelect.value === '1' ? 'New' : (statusSelect.value === '2' ? 'In Progress' : 'Completed');
                                                row.cells[5].innerText = newComment.value;
                                                document.body.removeChild(modal);
                                            })
                                            .catch(error => console.error('Error:', error));
                                    } else {
                                        document.body.removeChild(modal);
                                    }
                                });

                                modal.appendChild(statusSelect);
                                modal.appendChild(newComment);
                                modal.appendChild(confirmButton);
                                document.body.appendChild(modal);
                            });

                            actionsCell.appendChild(updateButton);
                            row.className = report.status_name.toLowerCase();
                        });
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                alert('فشل في الحصول على rfid_tag');
                 if (!sessionStorage.getItem('isLoggedIn')) {
                    window.location.href = 'login.html';  // Redirect to login if not logged in
                } 
            }
        })
        .catch(error => console.error('Error:', error));
});

// دالة تنسيق التاريخ
function formatDate(dateObj) {
    // التحقق مما إذا كانت القيمة كائنًا وتحتوي على حقل التاريخ
    if (dateObj && typeof dateObj === 'object' && 'date' in dateObj) {
        const dateString = dateObj.date; // استخلاص التاريخ من الكائن

        // إزالة الميلي ثانية (إن وجدت) من التاريخ
        const cleanDateString = dateString.split('.')[0];

        // تقسيم التاريخ والوقت
        const [datePart, timePart] = cleanDateString.split(' '); // نأخذ الجزء الخاص بالتاريخ والوقت

        // تقسيم الجزء الخاص بالتاريخ إلى مكوناته
        const [year, month, day] = datePart.split('-'); // سنة، شهر، يوم

        // إعادة تجميع التاريخ والوقت بالتنسيق المطلوب
        const formattedDateString = `${year}/${month}/${day} ${timePart}`;

        return formattedDateString; // إرجاع التاريخ والوقت بالتنسيق المطلوب
    } else {
        console.error('Invalid date object:', dateObj);
        return 'Invalid Date';
    }
}
