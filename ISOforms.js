document.addEventListener('DOMContentLoaded', () => {
    const departmentForms = {
        IAID: document.getElementById("IAIDForm"),
        HR: document.getElementById("HRForm"),
        IT: document.getElementById("ITForm"),
        OM: document.getElementById("OMForm"),
        MS: document.getElementById("MSForm"),
        FA: document.getElementById("FAForm"),
        PB: document.getElementById("PBForm"),
        SM: document.getElementById("SMForm"),
        VR: document.getElementById("VRForm"),
        LA: document.getElementById("LAForm"),
    };

    const departmentSelect = document.getElementById("department");

    departmentSelect.addEventListener('change', function () {
        // إخفاء جميع الفورمات أولاً
        Object.values(departmentForms).forEach(form => {
            if (form) {
                form.style.display = "none";
            }
        });

        // إظهار الفورمات الخاصة بالإدارة المحددة
        if (departmentForms[this.value]) {
            departmentForms[this.value].style.display = "block";
        }
    });

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

    document.getElementById('department').addEventListener('change', updateSections);

    document.getElementById('submitButton').addEventListener('click', () => {
        const reportType = document.getElementById('reportType').value;
        const department = document.getElementById('department').value;
        const section = document.getElementById('section').value;
        const description = document.getElementById('description').value;

        if (!reportType || !department || !section || !description) {
            alert('يرجى ملء جميع الحقول');
            return;
        }

        const isoValues = {};

        if (department === "IT") {
            const isoIT = [];
            if (document.getElementById('isoIT_computer_issue').checked) {
                isoIT.push('خلل في جهاز الحاسوب');
            }
            // ... (بقية الكود لجمع البيانات الخاصة بـ IT)
            isoValues.isoIT = isoIT.join(', ');
        }

        // ... (بقية الكود لجمع بيانات الفورمات الخاصة بالإدارات الأخرى)

        const currentDateTime = new Date().toISOString();

        // إرسال الطلب
    });
});
