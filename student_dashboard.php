<?php
session_start();

// 1. DATABASE CONNECTION (The Bridge)
define('DB_SERVER', 'sql302.infinityfree.com');
define('DB_USERNAME', 'if0_41413175');
define('DB_PASSWORD', 'vtesh1234');
define('DB_NAME', 'if0_41413175_student');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. SECURITY CHECK
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// 3. FETCH STUDENT PROFILE
$student_stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$student_stmt->bind_param("i", $student_id);
$student_stmt->execute();
$student = $student_stmt->get_result()->fetch_assoc();

// 4. FETCH AUTOMATIC UNITS BASED ON STUDENT YEAR/SEM
$year = $student['year'] ?? 1;
$sem  = $student['semester'] ?? 1;

$units_stmt = $conn->prepare("SELECT * FROM units WHERE year = ? AND semester = ?");
$units_stmt->bind_param("ii", $year, $sem);
$units_stmt->execute();
$units_result = $units_stmt->get_result();

// 5. FETCH ACADEMIC RESULTS
$marks_stmt = $conn->prepare("
    SELECT units.unit_name, marks.total, marks.grade 
    FROM marks 
    JOIN units ON marks.unit_id = units.id 
    WHERE marks.student_id = ?
");
$marks_stmt->bind_param("i", $student_id);
$marks_stmt->execute();
$marks_result = $marks_stmt->get_result();

// 6. CALCULATE STATS
$stats_stmt = $conn->prepare("SELECT COUNT(*) as total_units, AVG(total) as avg_score FROM marks WHERE student_id = ?");
$stats_stmt->bind_param("i", $student_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();

$total_units_count = $units_result->num_rows; 
$display_gpa = ($stats['total_units'] > 0) ? number_format(($stats['avg_score'] / 100) * 4, 2) : "0.00";

// 7. FETCH ATTENDANCE RECORDS
$attendance_stmt = $conn->prepare("
    SELECT units.unit_name, attendance.status, attendance.date 
    FROM attendance 
    JOIN units ON attendance.unit_id = units.id 
    WHERE attendance.student_id = ? 
    ORDER BY attendance.date DESC
");
$attendance_stmt->bind_param("i", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();

// 8. FETCH FINANCIAL DATA
$fee_stmt = $conn->prepare("SELECT * FROM fees WHERE student_id = ?");
$fee_stmt->bind_param("i", $student_id);
$fee_stmt->execute();
$fee_data = $fee_stmt->get_result()->fetch_assoc();

// Fallback values
$total_billed = $fee_data['initial_fee'] ?? 0;
$total_paid   = $fee_data['paid_amount'] ?? 0;
$balance      = $fee_data['balance'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zetech University | Student Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --nav-dark: #0b1528;
            --accent-orange: #f36f21;
            --bg-body: #f0f2f5;
            --text-main: #333;
        }

        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg-body); color: var(--text-main); }

        /* Navigation Bar */
        .top-nav {
            background: var(--nav-dark);
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        /* Profile Dropdown Styling */
.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 45px;
    background: white;
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    border-radius: 8px;
    overflow: hidden;
    z-index: 1001;
}

.dropdown-menu a {
    color: #333;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    font-size: 14px;
    transition: 0.2s;
}

.dropdown-menu a:hover {
    background-color: #f8f9fa;
    color: var(--accent-orange);
}

.dropdown-menu i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Show class for JS */
.show-dropdown {
    display: block;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

        .nav-brand { display: flex; align-items: center; gap: 15px; color: white; text-decoration: none; }
        .nav-links { display: flex; list-style: none; gap: 20px; margin: 0; padding: 0; }
        .nav-links li a { 
            color: #bdc3c7; 
            text-decoration: none; 
            font-size: 14px; 
            font-weight: 500;
            padding: 10px 5px;
            transition: 0.3s;
        }
        .nav-links li a:hover, .nav-links li.active a { color: white; border-bottom: 3px solid var(--accent-orange); }

        .user-actions { display: flex; align-items: center; gap: 20px; color: white; }
        .profile-circle { 
            background: #2c3e50; 
            width: 35px; height: 35px; 
            border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            font-weight: bold; border: 1px solid #555;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(11, 21, 40, 0.7), rgba(11, 21, 40, 0.7)), 
                        url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070');
            background-size: cover;
            background-position: center;
            height: 320px;
            display: flex;
            align-items: center;
            padding: 0 10%;
            color: white;
            transition: 0.3s ease-in-out;
        }

        /* Main Content */
        .main-container { max-width: 1200px; margin: -60px auto 50px; padding: 0 20px; }
        
        .no-hero-margin { margin-top: 30px !important; }

        .card { 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            padding: 25px; 
            border-top: 5px solid var(--accent-orange);
            display: none; 
        }
        .card.active { display: block; animation: slideUp 0.4s ease-out; }

        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .stat-bar { display: flex; justify-content: space-around; margin-bottom: 30px; }
        .stat-item { text-align: center; }
        .stat-item h4 { margin: 0; color: #7f8c8d; font-size: 0.9rem; }
        .stat-item p { margin: 5px 0 0; font-size: 1.5rem; font-weight: bold; color: var(--nav-dark); }

        .table-res { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 12px; background: #f8f9fa; border-bottom: 2px solid #eee; }
        td { padding: 12px; border-bottom: 1px solid #eee; }

        .btn-print { background: #34495e; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }

        /* Style for clickable rows */
        .clickable-row:hover { background-color: #fcfcfc; cursor: pointer; }
    </style>
</head>
<body>

    <nav class="top-nav">
        <div style="display: flex; align-items: center; gap: 40px;">
            <a href="#" class="nav-brand">
                <i class="fa fa-graduation-cap fa-2x" style="color: var(--accent-orange);"></i>
                <h2 style="margin:0; font-size: 1.2rem;">ZETECH<br><small style="font-weight: normal; font-size: 0.7rem;">UNIVERSITY</small></h2>
            </a>
            <ul class="nav-links">
                <li class="active" id="li-home"><a href="javascript:void(0)" onclick="showSec('home')">Home</a></li>
                <li id="li-dashboard"><a href="javascript:void(0)" onclick="showSec('dashboard')">Dashboard</a></li>
                <li id="li-results"><a href="javascript:void(0)" onclick="showSec('results')">Results</a></li>
                <li id="li-profile"><a href="javascript:void(0)" onclick="showSec('profile')">My Profile</a></li>
                <li id="li-fees"><a href="javascript:void(0)" onclick="showSec('fees')">Finance</a></li>
            </ul>
        </div>

        <div class="user-actions">
    <i class="fa fa-bell"></i>
    <i class="fa fa-envelope"></i>
    
    <div style="position: relative;">
        <div class="profile-circle" onclick="toggleDropdown()" style="cursor: pointer;">
            <?php echo strtoupper(substr($student['full_name'], 0, 2)); ?>
        </div>
        
        <div id="profileDropdown" class="dropdown-menu">
            <a href="javascript:void(0)" onclick="showSec('attendance')">
                <i class="fa fa-calendar-check"></i> Attendance
            </a>
            <a href="logout.php" style="color: #e74c3c; border-top: 1px solid #eee;">
                <i class="fa fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>
    </nav>

    <header class="hero" id="mainHero">
        <div class="hero-content">
            <h1>Learn on the <span>GO</span></h1>
            <p>ANYWHERE! ANYTIME!</p>
        </div>
    </header>

    <main class="main-container" id="contentWrapper">
        
        <section id="home" class="card active">
            <h2>Welcome back, <?php echo explode(' ', $student['full_name'])[0]; ?>!</h2>
            <p>You are currently logged into the Zetech University Student Portal. Ensure your unit registration for Year <?php echo $year; ?> Semester <?php echo $sem; ?> is complete.</p>
        </section>

        <section id="dashboard" class="card">
            <div id="unitListView">
                <h2>Student Dashboard</h2>
                <div class="stat-bar">
                    <div class="stat-item"><h4>GPA</h4><p><?php echo $display_gpa; ?></p></div>
                    <div class="stat-item"><h4>Units</h4><p><?php echo $total_units_count; ?></p></div>
                    <div class="stat-item"><h4>Fees</h4><p style="color: #27ae60;">KES 0</p></div>
                </div>
                <h3>Registered Units (Click to open)</h3>
                <div class="table-res">
                    <table>
                        <thead><tr><th>Code</th><th>Unit Name</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php 
                            $units_result->data_seek(0);
                            while($unit = $units_result->fetch_assoc()): ?>
                                <tr class="clickable-row" onclick="openUnitDetails('<?php echo htmlspecialchars($unit['unit_name']); ?>', '<?php echo htmlspecialchars($unit['unit_code'] ?? 'N/A'); ?>')">
                                    <td><?php echo htmlspecialchars($unit['unit_code'] ?? 'N/A'); ?></td>
                                    <td><strong><?php echo htmlspecialchars($unit['unit_name']); ?></strong></td>
                                    <td><span style='color: green;'>Active</span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            

            <div id="unitDetailView" style="display: none;">
                <button onclick="backToDashboard()" style="margin-bottom: 20px; cursor: pointer; border: none; background: #eee; padding: 5px 10px; border-radius: 4px;">
                    <i class="fa fa-arrow-left"></i> Back to Dashboard
                </button>
                <h2 id="detailUnitName">Unit Name</h2>
                <p id="detailUnitCode" style="color: #7f8c8d;"></p>
                <hr>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">
                        <h3><i class="fa fa-file-alt"></i> Unit Notes</h3>
                        <ul id="unitNotes">
                            <li>Lecture 1: Introduction</li>
                            <li>Lecture 2: Core Concepts</li>
                        </ul>
                    </div>
                    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">
                        <h3><i class="fa fa-edit"></i> CATs</h3>
                        <ul id="unitCats">
                            <li>CAT 1: Released (Due: 20th)</li>
                            <li>Assignment 1: Completed</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="results" class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Academic Performance</h2>
                <button class="btn-print" onclick="window.print()">Print</button>
            </div>
            <table>
                <thead><tr><th>Unit</th><th>Score</th><th>Grade</th></tr></thead>
                <tbody>
                    <?php 
                    $marks_result->data_seek(0);
                    while($row = $marks_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['unit_name']); ?></td>
                            <td><?php echo $row['total']; ?>%</td>
                            <td><strong><?php echo $row['grade']; ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section id="profile" class="card">
            <h2>Personal Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
            <p><strong>Reg No:</strong> <?php echo htmlspecialchars($student['reg_number']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course']); ?></p>
        </section>

        <section id="fees" class="card">
    <h2>Financial Overview</h2>
    <div style="padding: 20px; border: 1px solid #eee; border-radius: 8px; background: #fafafa;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Total Billed:</span>
            <strong>KES <?php echo number_format($total_billed, 2); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span>Total Paid:</span>
            <strong style="color: #27ae60;">KES <?php echo number_format($total_paid, 2); ?></strong>
        </div>
        <hr style="border: 0; border-top: 1px solid #ddd; margin: 15px 0;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 1.1rem; font-weight: bold;">Balance Due:</span>
            <strong style="color: <?php echo ($balance > 0) ? '#e74c3c' : '#27ae60'; ?>; font-size: 1.2rem;">
                KES <?php echo number_format($balance, 2); ?>
            </strong>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: right;">
        <button class="btn-print" onclick="window.print()">
            <i class="fa fa-download"></i> Download Statement
        </button>
    </div>
</section>
        <section id="attendance" class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>My Attendance History</h2>
        <span style="background: var(--nav-dark); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem;">
            Total Sessions: <?php echo $attendance_result->num_rows; ?>
        </span>
    </div>

    <div class="table-res">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Unit Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($attendance_result->num_rows > 0): ?>
                    <?php while($att = $attendance_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('D, d M Y', strtotime($att['date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($att['unit_name']); ?></strong></td>
                            <td>
                                <?php if($att['status'] == 'Present'): ?>
                                    <span style="color: #27ae60;"><i class="fa fa-check-circle"></i> Present</span>
                                <?php else: ?>
                                    <span style="color: #e74c3c;"><i class="fa fa-times-circle"></i> Absent</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 30px; color: #7f8c8d;">
                            No attendance records found for this semester.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

    </main>

    <script>
        function openUnitDetails(name, code) {
            document.getElementById('unitListView').style.display = 'none';
            document.getElementById('unitDetailView').style.display = 'block';
            document.getElementById('detailUnitName').innerText = name;
            document.getElementById('detailUnitCode').innerText = "Unit Code: " + code;
        }

        function backToDashboard() {
            document.getElementById('unitDetailView').style.display = 'none';
            document.getElementById('unitListView').style.display = 'block';
        }

        function showSec(id) {
            const hero = document.getElementById('mainHero');
            const wrapper = document.getElementById('contentWrapper');

            // Reset dashboard view whenever moving between tabs
            if (id === 'dashboard') {
                backToDashboard();
            }

            // 1. Hide/Show Hero and Adjust Margins
            if (id === 'home') {
                hero.style.display = 'flex';
                wrapper.classList.remove('no-hero-margin');
            } else {
                hero.style.display = 'none';
                wrapper.classList.add('no-hero-margin');
            }

            // 2. Hide all cards
            document.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
            // 3. Show selected card
            document.getElementById(id).classList.add('active');
            
            // 4. Update Navigation Active State
            document.querySelectorAll('.nav-links li').forEach(li => li.classList.remove('active'));
            document.getElementById('li-' + id).classList.add('active');
        }
        function toggleDropdown() {
    document.getElementById("profileDropdown").classList.toggle("show-dropdown");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.profile-circle')) {
        var dropdowns = document.getElementsByClassName("dropdown-menu");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show-dropdown')) {
                openDropdown.classList.remove('show-dropdown');
            }
        }
    }
}
    </script>
</body>
</html>