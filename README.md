# Student Information System (SIS)
A full-stack web application designed to manage student data, unit allocation, and financial records.

## 📋 Project Overview
The **Student Information System** is a comprehensive tool for educational institutions. It allows administrators to manage student enrollment, track academic progress through CAT scores, and monitor fee payments. It also features a role-based login system for both Administrators and Students.

## ✨ Key Features
* **Dual Role Login:** Secure authentication for both **Admin** and **Student** accounts.
* **Unit Allocation:** Automated logic to assign 4 units per semester based on the student's course.
* **Fee Management:** Track total fees, amount paid, and real-time balances.
* **Academic Tracking:** Manage course curriculum and enter CAT/Main Exam scores.
* **Student Dashboard:** Personalized view for students to check their registered units and fee status.

## 🛠️ Tech Stack
* **Frontend:** HTML5, CSS3 (Modern, high-contrast design)
* **Backend:** PHP
* **Database:** MySQL
* **Environment:** XAMPP / Localhost

## 🚀 How to Set Up
1.  **Clone/Download:** Download the project files.
2.  **Move to htdocs:** Place the project folder into your `C:\xampp\htdocs\` directory.
3.  **Database Configuration:**
    * Open **phpMyAdmin**.
    * Create a database named `student_db`.
    * Import the provided SQL file to generate the `students`, `units`, and `fees` tables.
4.  **Connection Setup:** Verify the credentials in your `db_connect.php` file.
5.  **Run:** Open `http://localhost/student-manage` in your browser.

## 👥 Roles
* **Admin:** Full access to manage students, units, and fees.
* **Student:** View personal profile, registered units, and fee balance.

## 👤 Developer
* **Name:** Emannuel Kibet
* **Program:** Diploma in Information Technology
