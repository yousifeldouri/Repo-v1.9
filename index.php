<?php
session_start();
// سنحدد الرابط المناسب حسب الدور
$dashboard_link = '';
$dashboard_label = '';
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            $dashboard_link = 'admin_dashboard.html';
            $dashboard_label = 'لوحة الأدمن';
            break;
        case 'manager':
            $dashboard_link = 'manager_dashboard.html';
            $dashboard_label = 'لوحة المدير';
            break;
        case 'head':
            $dashboard_link = 'head_dashboard.html';
            $dashboard_label = 'لوحة رئيس القسم';
            break;
        case 'supervisor':
            $dashboard_link = 'admin_dashboard.html'; // أو صفحة خاصة إذا وجدت
            $dashboard_label = 'لوحة المشرف';
            break;
        // employee أو أي دور آخر: لا شيء
    }
}
?>
<!DOCTYPE HTML>
<!--
	Massively by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html lang="ar" dir="rtl">
<head>
	<link rel="icon" href="12.png">
	<title>Reboo</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
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
			background: #fff;
			border-radius: 16px;
			box-shadow: 0 2px 8px rgba(44,62,80,0.08);
			padding: 2rem 2.5rem;
			text-align: center;
			min-width: 180px;
			transition: box-shadow 0.2s, transform 0.2s;
			font-size: 1.1rem;
			font-weight: bold;
			color: #1c5fac;
			text-decoration: none;
			display: flex;
			flex-direction: column;
			align-items: center;
		}
		.nav-card:hover {
			box-shadow: 0 4px 16px rgba(44,62,80,0.18);
			background: #e0e242;
			color: #222;
			transform: translateY(-4px) scale(1.04);
			text-decoration: none;
		}
		.nav-card i {
			font-size: 2.2rem;
			margin-bottom: 0.7rem;
			color: #1c5fac;
			transition: color 0.2s;
		}
		.nav-card:hover i {
			color: #222;
		}
        .dashboard-btn {
            display: block;
            margin: 2rem auto 0 auto;
            padding: 1rem 2.5rem;
            background: #1c5fac;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 2px 8px rgba(44,62,80,0.08);
        }
        .dashboard-btn:hover {
            background: #e0e242;
            color: #222;
        }
	</style>
</head>
<body class="is-preload">

	<!-- Wrapper -->
	<div id="wrapper" class="fade-in">

		<!-- Intro -->
		<div id="intro">
			<h1>مرحباً، <span id="employeeName"></span></h1>
			<p>قسمك: <span id="employeeSection"></span></p>
			<p>عدد البلاغات المرسلة إليك: <span id="reportCount"></span></p>
			<ul class="actions">
				<li><a href="generic.php" class="button icon solid solo fa-arrow-down scrolly"></a><span class="label"></span>إرسال بلاغ</a></li>
				<li><a href="elements.html" class="button icon solid solo fa-eye scrolly"></a><span class="label"></span>عرض البلاغات</a></li>
			</ul>
            <?php if ($dashboard_link): ?>
                <a href="<?= $dashboard_link ?>" class="dashboard-btn"><i class="fas fa-tachometer-alt"></i> <?= $dashboard_label ?></a>
            <?php endif; ?>
		</div>

		<!-- Header -->
		<header id="header">
			<a href="index.php" class="logo">تطبيق بلآغ</a>
		</header>

		<!-- Nav Cards -->
		<div class="nav-cards-container">
			<a href="index.php" class="nav-card"><i class="fas fa-home"></i>الرئيسية</a>
			<a href="generic.php" class="nav-card"><i class="fas fa-pen"></i>كتابة بلاغ</a>
			<a href="elements.html" class="nav-card"><i class="fas fa-list"></i>عرض البلاغات</a>
			<a href="assigned_reports.html" class="nav-card"><i class="fas fa-tasks"></i>بلاغاتي المستلمة</a>
			<a href="my_reports.html" class="nav-card"><i class="fas fa-user"></i>بلاغاتي</a>
			<a href="login.html" class="nav-card"><i class="fas fa-sign-out-alt"></i>تسجيل خروج</a>
		</div>

		<!-- Main -->
		<div id="main">

			<!-- Featured Post -->
			<article class="post featured">
				<header class="major">
					<span class="date">Oct 27, 2024</span>
					<h2>
						<a href="#">
							شركة بشائر<br />
							للطاقة
						</a>
					</h2>
					<h3>
						شركة بشائر للطاقة ترحب بموظفينها<br />
						و تزف لهم اغلى عبارات الشكر على عملهم الدؤوب الذي يسمو بشركتنا <br />
						مع تمنياتنا لهم ببيئة عمل مريحة و سلسه
					</h3>
				</header>
			</article>

			<!-- Footer -->
			<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

		</div>
	</div>
	
</body>
</html> 