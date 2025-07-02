document.addEventListener('DOMContentLoaded', () => {
    // جلب rfid_tag من الجلسة فقط
    fetch('get_rfid.php')
        .then(response => response.json())
        .then(data => {
            let rfid_tag = null;
            if (data.success && data.rfid_tag) {
                rfid_tag = data.rfid_tag;
            }
            if (!rfid_tag) {
                alert('لم يتم التعرف على المستخدم. يرجى تسجيل الدخول مجدداً.');
                window.location.href = 'login.html';
                return;
            }
            // عرض شاشة البداية حتى يتم استرجاع بيانات RFID بنجاح
            const departmentForms = {
                HR: document.getElementById("HRForm"),
                IT: document.getElementById("ITForm"),
                OM: document.getElementById("OMForm"),
                MS: document.getElementById("MSForm"),
                FA: document.getElementById("FAForm"),
                IAID: document.getElementById("IAIDForm"),
                PB: document.getElementById("PBForm"),
                SM: document.getElementById("SMForm"),
                VR: document.getElementById("VRForm"),
                LA: document.getElementById("LAForm"),
            };

            // عند اختيار إدارة من القائمة
            const departmentSelect = document.getElementById("department");

            departmentSelect.addEventListener('change', function () {
                // إخفاء جميع الفورمات أولاً 
                Object.values(departmentForms).forEach(form => {
                    if (form) { 
                        form.style.display = "none"; } 
                    });

                // إظهار الفورمات الخاصة بالإدارة المحددة
                if (departmentForms[this.value]) {
                    departmentForms[this.value].style.display = "block";
                }
            });
            
            

            // تحديث الأقسام بناءً على الإدارة
            const sections = {
                HR: ["الموارد البشرية", "الشؤون الإدارية"],
                IT: ["البرمجة", "الشبكات"],
                OM: ["التشغيل", "الصيانة و الإنشاءات", "الأمن و السلامة"],
                MS: ["التسويق و خدمة العملاء", "المبيعات", "مبيعات المحطات", "الغاز"],
                FA: ["الإصول و المشروعات", "الحسابات العامة", "الموازنة و التكاليف", "حسابات المحطات"],
                IAID: ["المراجعة المالية و المستندية", "مراجعة النظم و العمليات"],
                PB: ["التخطيط و المتابعة", "التطوير و القياسات"],
                SM: ["المشتروات", "التوزيع", "الإستيراد"],
                VR: ["المكتب التنفيذي"],
                LA: ["المستشار القانوني"]
            };

            function updateSections() {
                const departmentSelect = document.getElementById("department");
                const sectionSelect = document.getElementById("section");
                if (!departmentSelect || !sectionSelect) {
                    console.warn('العنصر department أو section غير موجود في الصفحة');
                    return;
                }
                const selectedDepartment = departmentSelect.value;
                sectionSelect.innerHTML = '<option value="">اختر القسم</option>';
                if (selectedDepartment && sections[selectedDepartment]) {
                    sections[selectedDepartment].forEach(section => {
                        const option = document.createElement("option");
                        option.value = section;
                        option.textContent = section;
                        sectionSelect.appendChild(option);
                    });
                }
            }

            var departmentSelectEl = document.getElementById('department');
            if (departmentSelectEl) {
                departmentSelectEl.addEventListener('change', function() {
                    showSectionLoader();
                    setTimeout(() => {
                        updateSections();
                        hideSectionLoader();
                    }, 600); // محاكاة تحميل
                });
            } else {
                console.warn('العنصر department غير موجود في الصفحة');
            }

            // ========== رفع الملفات ========== 
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const browseFiles = document.getElementById('browseFiles');
            const fileList = document.getElementById('fileList');
            let selectedFiles = [];

            if (dropZone && fileInput && browseFiles && fileList) {
                // فتح نافذة اختيار الملفات عند الضغط على النص
                browseFiles.addEventListener('click', () => fileInput.click());
                // عند اختيار ملفات
                fileInput.addEventListener('change', (e) => {
                    addFiles(Array.from(e.target.files));
                    fileInput.value = '';
                });
                // دعم السحب والإفلات
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.add('dragover');
                });
                dropZone.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('dragover');
                });
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('dragover');
                    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                        addFiles(Array.from(e.dataTransfer.files));
                    }
                });
            }
            function addFiles(files) {
                for (const file of files) {
                    // منع التكرار
                    if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                        selectedFiles.push(file);
                    }
                }
                renderFileList();
            }
            function renderFileList() {
                fileList.innerHTML = '';
                selectedFiles.forEach((file, idx) => {
                    const li = document.createElement('li');
                    li.style.cssText = 'margin:0.3em 0;display:flex;align-items:center;gap:0.7em;';
                    li.innerHTML = `<i class='fas fa-file'></i> <span>${file.name}</span> <span style='color:#888;font-size:0.9em;'>(${(file.size/1024).toFixed(1)} KB)</span> <button type='button' style='background:none;border:none;color:#e74c3c;cursor:pointer;font-size:1.1em;' title='حذف'>&times;</button>`;
                    li.querySelector('button').onclick = () => { selectedFiles.splice(idx,1); renderFileList(); };
                    fileList.appendChild(li);
                });
                if (selectedFiles.length === 0) fileList.innerHTML = '<li style="color:#888;font-size:0.95em;">لا توجد ملفات مرفقة</li>';
            }

            // التحقق من صحة النموذج قبل الإرسال
            var submitButtonEl = document.getElementById('submitButton');
            if (submitButtonEl) {
                submitButtonEl.addEventListener('click', (e) => {
                    e.preventDefault();
                    var reportTypeEl = document.getElementById('reportType');
                    var departmentEl = document.getElementById('department');
                    var sectionEl = document.getElementById('section');
                    var descriptionEl = document.getElementById('description');
                    // إبراز الحقول المطلوبة
                    highlightRequiredFields([reportTypeEl, departmentEl, sectionEl, descriptionEl]);
                    if (!reportTypeEl.value || !departmentEl.value || !sectionEl.value || !descriptionEl.value) {
                        showToast('يرجى ملء جميع الحقول المطلوبة', 'error');
                        return;
                    }
                    // Loader على الزر
                    submitButtonEl.disabled = true;
                    submitButtonEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
                    // جمع بيانات الفورمات بناءً على الإدارة المختارة
                    const isoValues = {};
                    // جمع البيانات الخاصة بـ IT (يمكن اختيار أكثر من خيار)
                    if (departmentEl.value === "IT") {
                        const isoIT = [];
                        const itFields = [
                            'isoIT_computer_issues','isoIT_calculation_issues','isoIT_help_issues','isoIT_warehouse_issues','isoIT_customer_issues','isoIT_station_issues','isoIT_reader_issues','isoIT_otherpro_issues','isoIT_down_issues','isoIT_unprint_issues','isoIT_cable_issues','isoIT_router_issues','isoIT_machinenfc_issues','isoIT_well_issues','isoIT_machine_issues','isoIT_cctv_issues','isoIT_phone_issues','isoIT_cutphone_issues','isoIT_other_issues'
                        ];
                        const itLabels = [
                            'خلل في جهاز الحاسوب','برامج الحسابات','برامج مساعدة','برامج إدارة المستودعات','برامج العملاء','برامج إدارة المحطات','قارئ الكروت (Reader)','أنظمة اخرى','لا تعمل','الطابعة لا تطبع ملفات','كيبل الشبكة','جهاز الشبكة (Router)','قارئ المكينات','قارئ الابار','المكينات','كاميرات المراقبة','الهاتف لا يعمل','الهاتف لا يوجد حرارة','اخرى'
                        ];
                        itFields.forEach((id, idx) => {
                            var el = document.getElementById(id);
                            if (el && el.checked) isoIT.push(itLabels[idx]);
                        });
                        isoValues.isoIT = isoIT.join(',');
                    }
                    // جمع بيانات الفورمات الخاصة بالإدارات الأخرى
                    if (departmentEl.value === "HR") {
                        var isoHRInput = document.getElementById("isoHR");
                        if (isoHRInput) {
                            isoValues.isoHR = isoHRInput.value;
                        } else {
                            console.warn('العنصر isoHR غير موجود في الصفحة');
                        }
                    }
                    // إرسال البيانات والملفات عبر FormData
                    const formData = new FormData();
                    formData.append('rfid_tag', rfid_tag);
                    formData.append('reportType', reportTypeEl.value);
                    formData.append('description', descriptionEl.value);
                    formData.append('section', sectionEl.value);
                    formData.append('department', departmentEl.value);
                    formData.append('isoValues', JSON.stringify(isoValues));
                    // إضافة الملفات
                    selectedFiles.forEach((file, idx) => {
                        formData.append('attachments[]', file);
                    });
                    // إرسال الطلب
                    fetch('process_nfc.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            showToast(data.error, 'error');
                        } else {
                            showToast(data.message, 'success');
                            setTimeout(() => { window.location.reload(); }, 1200);
                        }
                    })
                    .catch(error => {
                        showToast('حدث خطأ في الاتصال بالخادم', 'error');
                    })
                    .finally(() => {
                        submitButtonEl.disabled = false;
                        submitButtonEl.innerHTML = '<i class="fas fa-paper-plane"></i> تقديم التقرير';
                    });
                });
            } else {
                console.warn('العنصر submitButton غير موجود في الصفحة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال بالخادم. يرجى إعادة تحميل الصفحة أو تسجيل الدخول مجدداً.');
            window.location.href = 'login.html';
        });

    // ========== تحسين تجربة المستخدم ==========
    // Toast Function
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast');
        if (!toast) return;
        toast.style.display = 'block';
        toast.style.background = type === 'success' ? '#4BB543' : '#e74c3c';
        toast.style.color = '#fff';
        toast.innerText = msg;
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }
    // عداد الأحرف للوصف
    const desc = document.getElementById('description');
    const descCharCount = document.getElementById('descCharCount');
    if (desc && descCharCount) {
        desc.addEventListener('input', function() {
            descCharCount.textContent = this.value.length;
        });
    }
    // إبراز الحقول المطلوبة عند محاولة الإرسال
    function highlightRequiredFields(fields) {
        fields.forEach(f => {
            if (f && !f.value) {
                f.style.borderColor = '#e74c3c';
                setTimeout(() => { f.style.borderColor = ''; }, 2000);
            }
        });
    }
    // Loader صغير بجانب قائمة الأقسام عند التحديث
    const sectionSelect = document.getElementById('section');
    let sectionLoader = null;
    function showSectionLoader() {
        if (!sectionLoader) {
            sectionLoader = document.createElement('span');
            sectionLoader.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            sectionLoader.style.marginRight = '8px';
            sectionSelect.parentNode.insertBefore(sectionLoader, sectionSelect.nextSibling);
        }
        sectionLoader.style.display = 'inline-block';
    }
    function hideSectionLoader() {
        if (sectionLoader) sectionLoader.style.display = 'none';
    }
    // كود الوضع الليلي يعمل دائماً
    const darkModeBtn = document.getElementById('toggleDarkMode');
    if (darkModeBtn) {
        darkModeBtn.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const icon = this.querySelector('i');
            if (document.body.classList.contains('dark-mode')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
                this.style.background = '#222';
                icon.style.color = '#ffe600';
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
                this.style.background = '#fff';
                icon.style.color = '#1c5fac';
            }
        });
        // الوضع الليلي CSS
        if (!document.getElementById('darkModeStyle')) {
            const style = document.createElement('style');
            style.id = 'darkModeStyle';
            style.innerHTML = `
                body.dark-mode { background: #181c23 !important; color: #e0e0e0 !important; }
                body.dark-mode .container#reportForm { background: #232a36 !important; color: #e0e0e0; }
                body.dark-mode .nav-card { background: linear-gradient(135deg, #232a36 60%, #1c5fac 100%) !important; color: #e0e0e0 !important; border-color: #222 !important; }
                body.dark-mode .nav-card:hover { background: linear-gradient(135deg, #ffe600 60%, #232a36 100%) !important; color: #232a36 !important; }
                body.dark-mode .department-form { background: #232a36 !important; color: #e0e0e0; }
                body.dark-mode select, body.dark-mode textarea { background: #232a36 !important; color: #e0e0e0 !important; border-color: #444 !important; }
                body.dark-mode #submitButton { background: linear-gradient(90deg, #ffe600 60%, #1c5fac 100%) !important; color: #232a36 !important; }
                body.dark-mode #submitButton:hover { background: linear-gradient(90deg, #1c5fac 60%, #ffe600 100%) !important; color: #ffe600 !important; }
                body.dark-mode .required-star { color: #ffe600 !important; }
            `;
            document.head.appendChild(style);
        }
    }
    // ========== نهاية التحسينات العامة ==========
});