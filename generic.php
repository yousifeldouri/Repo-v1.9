<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>كتابة بلاغ</title>
  <link rel="stylesheet" href="assets/css/main.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
  <style>
    .nav-cards-container {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 2rem;
      margin: 2rem 0;
      flex-wrap: wrap;
    }
    .nav-card {
      background: linear-gradient(135deg, #f8fafc 60%, #e0e7ef 100%);
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(44,62,80,0.10);
      padding: 2.2rem 2.7rem;
      text-align: center;
      min-width: 190px;
      transition: box-shadow 0.25s, transform 0.22s, background 0.22s, color 0.22s;
      font-size: 1.13rem;
      font-weight: 600;
      color: #1c5fac;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      border: 1.5px solid #e3e8f0;
      letter-spacing: 0.01em;
    }
    .nav-card:hover {
      box-shadow: 0 8px 32px rgba(44,62,80,0.18);
      background: linear-gradient(135deg, #e0e242 60%, #f8fafc 100%);
      color: #222;
      transform: translateY(-6px) scale(1.045);
      text-decoration: none;
      border-color: #e0e242;
    }
    .nav-card i {
      font-size: 2.4rem;
      margin-bottom: 0.8rem;
      color: #1c5fac;
      transition: color 0.22s;
    }
    .nav-card:hover i {
      color: #222;
    }
    .container#reportForm {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 24px rgba(44,62,80,0.10);
      max-width: 1520px;
      margin: 2.5rem auto 2rem auto;
      padding: 2.5rem 2.2rem 2rem 2.2rem;
      display: flex;
      flex-direction: column;
      gap: 1.1rem;
    }
    .container#reportForm label {
      font-weight: 600;
      color: #1c5fac;
      margin-bottom: 0.3rem;
      font-size: 1.08rem;
    }
    .container#reportForm select,
    .container#reportForm textarea {
      border-radius: 8px;
      border: 1.5px solid #e3e8f0;
      padding: 0.7rem 1rem;
      font-size: 1.05rem;
      margin-top: 0.2rem;
      margin-bottom: 0.7rem;
      background: #f8fafc;
      transition: border 0.2s;
      outline: none;
      width: 100%;
      box-sizing: border-box;
    }
    .container#reportForm select:focus,
    .container#reportForm textarea:focus {
      border-color: #1c5fac;
      background: #fff;
    }
    .department-form {
      background: #f8fafc;
      border-radius: 12px;
      padding: 1.1rem 1rem 0.7rem 1rem;
      margin-bottom: 1.1rem;
      box-shadow: 0 2px 8px rgba(44,62,80,0.06);
    }
    .department-form h3 {
      color: #1c5fac;
      font-size: 1.13rem;
      margin-bottom: 0.7rem;
      font-weight: 700;
    }
    .department-form label {
      color: #222;
      font-weight: 500;
      font-size: 1.01rem;
    }
    .department-form input[type="checkbox"] {
      accent-color: #1c5fac;
      margin-inline-end: 0.4em;
      transform: scale(1.15);
    }
    #submitButton {
      background: linear-gradient(90deg, #1c5fac 60%, #3a7bd5 100%);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 0.85rem 2.2rem;
      font-size: 1.13rem;
      font-weight: 700;
      margin-top: 1.2rem;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(44,62,80,0.10);
      transition: background 0.22s, transform 0.18s;
      letter-spacing: 0.01em;
    }
    #submitButton:hover {
      background: linear-gradient(90deg, #e0e242 60%, #f8fafc 100%);
      color: #1c5fac;
      transform: translateY(-2px) scale(1.03);
    }
    @media (max-width: 700px) {
      .nav-cards-container {
        flex-direction: column;
        gap: 1.2rem;
      }
      .container#reportForm {
        padding: 1.2rem 0.7rem 1.2rem 0.7rem;
        max-width: 98vw;
      }
      .nav-card {
        min-width: 0;
        width: 100%;
        padding: 1.2rem 0.7rem;
      }
    }
  </style>
</head>
<body>
<?php include 'assets/nav.php'; ?>
<!-- Nav Cards -->
<div class="nav-cards-container">
  <a href="index.php" class="nav-card"><i class="fas fa-home"></i>الرئيسية</a>
  <a href="generic.php" class="nav-card"><i class="fas fa-pen"></i>كتابة بلاغ</a>
  <a href="elements.html" class="nav-card"><i class="fas fa-list"></i>عرض البلاغات</a>
  <a href="assigned_reports.html" class="nav-card"><i class="fas fa-tasks"></i>بلاغاتي المستلمة</a>
  <a href="my_reports.html" class="nav-card"><i class="fas fa-user"></i>بلاغاتي</a>
  <a href="login.html" class="nav-card"><i class="fas fa-sign-out-alt"></i>تسجيل خروج</a>
</div> 
<!-- زر تبديل الوضع الليلي -->
<button id="toggleDarkMode" style="position:fixed;top:18px;left:18px;z-index:1000;background:#fff;border-radius:50%;border:none;width:44px;height:44px;box-shadow:0 2px 8px rgba(44,62,80,0.10);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.3rem;"><i class="fas fa-moon"></i></button>
<!-- Toast للرسائل المنبثقة -->
<div id="toast" style="display:none;position:fixed;top:20px;right:20px;z-index:9999;padding:1em 2em;border-radius:8px;font-weight:bold;"></div>
<!-- Send Report Interface -->
<div class="container" id="reportForm">
					<label for="reportType"><strong>🔹نوع البلآغ:<span class="required-star">*</span></strong></label>
					<select id="reportType" name="reportType">
						<option value="">اختر النوع</option>
						<option value="financial">إداري</option>
						<option value="emergence">عاجل</option>
						<option value="maintenance">صيانه</option>
					</select>
					<br><br>
					<label for="department"><strong>🔹إختر الإدارة:<span class="required-star">*</span></strong></label>
					<select id="department" name="department">
						<option value="">اختر الإدارة</option>
						<option value="HR">الموارد البشرية و الشؤون الإدارية</option>
						<option value="IT">تقنية المعلومات و الإتصالات</option>
						<option value="OM">التشغيل والصيانة</option>
						<option value="MS">التسويق والمبيعات</option>
						<option value="FA">المالية</option>
						<option value="IAID">المراجعة الداخلية و التفتيش الإداري</option>
						<option value="PB">التخطيط و تطوير الأعمال</option>
						<option value="SM">الإمداد</option>
						<option value="VR">المكتب التنفيذي</option>
						<option value="LA">المستشار القانوني</option>
					</select>
					<br><br>
					<label for="section"><strong>🔹إختر القسم:<span class="required-star">*</span></strong></label>
					<select id="section" name="section">
						<option value="">إختر القسم</option>
						<!-- سيتم ملء هذه القائمة ديناميكيًا -->
					</select>
					<br><br>
					
					<!--Fromats-->
						
							
							
							<!--<div id="ITForm" style="display:none;">
								<h3>حدد المشاكل المراد الابلاغ عنها</h3>
								<label>1.أجهزة الكمبيوتر(PC):-</label><br>
								<label><input type="checkbox" id="isoIT_computer_issue" value="خلل في جهاز الحاسوب">جهاز الكمبيوتر لا يعمل</label><br>
								<label><input type="checkbox" id="isoIT_calculation_issue" value="برامج الحسابات">برامج الحسابات</label><br>
								<label><input type="checkbox" id="isoIT_help_issue" value="برامج مساعدة">برامج مساعدة</label><br>
								<label><input type="checkbox" id="isoIT_warehouse_issue" value="برامج إدارة المستودعات">برامج إدارة المستودعات</label><br>
								<label><input type="checkbox" id="isoIT_customer_issue" value="برامج العملاء">برامج العملاء</label><br>
								<label><input type="checkbox" id="isoIT_station_issue" value="برامج إدارة المحطات">برامج إدارة المحطات</label><br>
								<label><input type="checkbox" id="isoIT_reader_issue" value="قارئ الكروت (Reader)">قارئ الكروت (Reader)</label><br>
								<label><input type="checkbox" id="isoIT_otherpro_issue" value="أنظمة اخرى">أنظمة اخرى</label><br>
								<label>2. الطابعة(scanner & Printer):-</label><br>
								<label><input type="checkbox" id="isoIT_down_issue" value="لا تعمل">لا تعمل</label><br>
								<label><input type="checkbox" id="isoIT_unprint_issue" value="الطابعة لا تطبع ملفات">الطابعة لا تطبع ملفات</label><br>
								<label>3. الإجهزة و الإتصال:-</label><br>
								<label><input type="checkbox" id="isoIT_cable_issue" value="كيبل الشبكة">كيبل الشبكة</label><br>
								<label><input type="checkbox" id="isoIT_router_issue" value="جهاز الشبكة (Router)">جهاز الشبكة (Router)</label><br>
								<label><input type="checkbox" id="isoIT_machinenfc_issue" value="قارئ المكينات">قارئ المكينات</label><br>
								<label><input type="checkbox" id="isoIT_well_issue" value="قارئ الابار">قارئ الابار</label><br>
								<label><input type="checkbox" id="isoIT_machine_issue" value="المكينات">المكينات</label><br>
								<label><input type="checkbox" id="isoIT_cctv_issue" value="كاميرات المراقبة">كاميرات المراقبة</label><br>
								<label><input type="checkbox" id="isoIT_phone_issue" value="الهاتف لا يعمل">الهاتف لا يعمل</label><br>
								<label><input type="checkbox" id="isoIT_cutphone_issue" value="الهاتف لا يوجد حرارة">الهاتف لا يوجد حرارة</label><br>
								<label><input type="checkbox" id="isoIT_other_issue" value="اخرى">اخرى</label><br>
							</div>-->
							<div id="ITForm" class="department-form" style="display: none;">
								<h3>حدد المشاكل المراد الابلاغ عنها</h3>
								<label>1.أجهزة الكمبيوتر(PC):-</label><br>
								<div>
									<input type="checkbox" id="isoIT_computer_issues" name="issues" value="isoIT_computer_issues">
									<label for="isoIT_computer_issues">جهاز الكمبيوتر لا يعمل</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_calculation_issues" name="issues" value="isoIT_calculation_issues">
									<label for="isoIT_calculation_issues">برامج الحسابات</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_help_issues" name="issues" value="isoIT_help_issues">
									<label for="isoIT_help_issues">برامج مساعدة</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_warehouse_issues" name="issues" value="isoIT_warehouse_issues">
									<label for="isoIT_warehouse_issues">برامج إدارة المستودعات</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_customer_issues" name="issues" value="isoIT_customer_issues">
									<label for="isoIT_customer_issues">برامج العملاء</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_station_issues" name="issues" value="isoIT_station_issues">
									<label for="isoIT_station_issues">برامج إدارة المحطات</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_reader_issues" name="issues" value="isoIT_reader_issues">
									<label for="isoIT_reader_issues">قارئ الكروت (Reader)</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_otherpro_issues" name="issues" value="isoIT_otherpro_issues">
									<label for="isoIT_otherpro_issues">أنظمة اخرى</label>
								</div>
								<label>2. الطابعة(scanner & Printer):-</label><br>
								<div>
									<input type="checkbox" id="isoIT_down_issues" name="issues" value="isoIT_down_issues">
									<label for="isoIT_down_issues">لا تعمل</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_unprint_issues" name="issues" value="isoIT_unprint_issues">
									<label for="isoIT_unprint_issues">الطابعة لا تطبع ملفات</label>
								</div>
								<label>3. الإجهزة و الإتصال:-</label><br>
								<div>
									<input type="checkbox" id="isoIT_cable_issues" name="issues" value="isoIT_cable_issues">
									<label for="isoIT_cable_issues">كيبل الشبكة</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_router_issues" name="issues" value="isoIT_router_issues">
									<label for="isoIT_router_issues">جهاز الشبكة (Router)</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_machinenfc_issues" name="issues" value="isoIT_machinenfc_issues">
									<label for="isoIT_machinenfc_issues">قارئ المكينات</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_well_issues" name="issues" value="isoIT_well_issues">
									<label for="isoIT_well_issues">قارئ الابار</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_machine_issues" name="issues" value="isoIT_machine_issues">
									<label for="isoIT_machine_issues">المكينات</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_cctv_issues" name="issues" value="isoIT_cctv_issues">
									<label for="isoIT_cctv_issues">كاميرات المراقبة</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_phone_issues" name="issues" value="isoIT_phone_issues">
									<label for="isoIT_phone_issues">الهاتف لا يعمل</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_cutphone_issues" name="issues" value="isoIT_cutphone_issues">
									<label for="isoIT_cutphone_issues">الهاتف لا يوجد حرارة</label>
								</div>
								<div>
									<input type="checkbox" id="isoIT_other_issues" name="issues" value="isoIT_other_issues">
									<label for="isoIT_other_issues">اخرى</label>
								</div>
							</div>
							
					<div id="HRForm" class="department-form" style="display: none;">
						<h3>فورم معالجة بلاغات الموارد البشرية</h3>
						<label>حدد نوع البلاغ:</label><br>
						<div>
							<input type="checkbox" id="employee_complaint" name="issues" value="employee_complaint">
							<label for="employee_complaint">شكوى موظف</label>
						</div>
						<div>
							<input type="checkbox" id="attendance_issues" name="issues" value="attendance_issues">
							<label for="attendance_issues">مشاكل الحضور والانصراف</label>
						</div>
						<div>
							<input type="checkbox" id="salary_issues" name="issues" value="salary_issues">
							<label for="salary_issues">مشاكل الرواتب</label>
						</div>
					</div>

					<label for="description"><strong>🔹اكتب بلاغك:<span class="required-star">*</span></strong></label>
						<textarea class="form-control border-0 bg-light px-4 py-3" id="description" name="description" rows="4" placeholder="قم بكتابة وصف لمشكلتك" maxlength="500"></textarea>
						<div style="text-align:left;font-size:0.95em;color:#888;margin-bottom:0.7em;">
							<span id="descCharCount">0</span>/500 حرف
						</div>
						<!-- منطقة رفع الملفات -->
						<div id="fileUploadArea" class="file-upload-area">
							<input type="file" id="fileInput" name="attachments[]" multiple style="display:none" accept="image/*,.pdf,.doc,.png,.jpg,.jpeg,.gif,.docx,.xls,.xlsx,.zip,.rar,.txt,.ppt,.pptx,.csv,.7z,.tar,.gz,.mp3,.mp4,.avi,.mov,.wmv,.mkv,.svg,.json,.xml,.html,.css,.js,.php,.py,.java,.c,.cpp,.h,.hpp,.md">
							<div id="dropZone" class="drop-zone" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2.2em 1em;border:2.5px dashed #1c5fac;border-radius:18px;background:#f8fafd;cursor:pointer;margin-bottom:1em;position:relative;transition:border-color 0.3s,background 0.3s;min-height:180px;">
								<i class="fas fa-cloud-upload-alt" style="font-size:3.5em;color:#1c5fac;margin-bottom:0.5em;"></i>
								<div class="dz-text" style="font-size:1.15em;color:#1c5fac;margin-bottom:0.3em;">اسحب الملفات هنا أو <span id="browseFiles" style="color:#1c5fac;cursor:pointer;text-decoration:underline;">اختر من جهازك</span></div>
								<small style="color:#888;">يمكنك إرفاق صور، مستندات، ملفات مضغوطة، أو أي نوع آخر (اختياري)</small>
							</div>
							<ul id="fileList" class="file-list"></ul>
						</div>
						<button id="submitButton"><i class="fas fa-paper-plane"></i> تقديم التقرير</button>

				</div>
<script src="script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // عناصر منطقة رفع الملفات
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const browseFiles = document.getElementById('browseFiles');
    const fileList = document.getElementById('fileList');
    let selectedFiles = [];

    if (browseFiles && fileInput) {
        browseFiles.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.click();
        });
    }
    if (dropZone && fileInput) {
        dropZone.addEventListener('click', function(e) {
            if (e.target.id === 'browseFiles' || e.target.classList.contains('dz-text') || e.target === dropZone) {
                fileInput.click();
            }
        });
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('dragover');
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                addFiles(Array.from(e.dataTransfer.files));
            }
        });
    }
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            addFiles(Array.from(e.target.files));
            fileInput.value = '';
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
        if (!fileList) return;
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
});
</script>
</body>
</html>