<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/your-username/semcom-campusconnect/actions"><img src="https://github.com/your-username/semcom-campusconnect/workflows/CI/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/your-username/semcom-campusconnect"><img src="https://img.shields.io/packagist/dt/your-username/semcom-campusconnect" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/your-username/semcom-campusconnect"><img src="https://img.shields.io/packagist/v/your-username/semcom-campusconnect" alt="Latest Stable Version"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/packagist/l/your-username/semcom-campusconnect" alt="License"></a>
</p>

<h1 align="center">SEMCOM CampusConnect</h1>

<p align="center">
  A Laravel 11–based academic management platform to streamline administrative, counseling, teaching, and student workflows for a college.
</p>

---

## 📖 Overview

SEMCOM CampusConnect is a role-based academic management system built with Laravel 11, PHP 8+, MySQL, Tailwind CSS, and Vite. It provides four distinct dashboards—Admin, Counselor, Teacher, and Student—each secured by custom middleware to enforce access control. The platform handles bulk imports, attendance tracking, reporting, and notifications, reducing manual effort and ensuring data accuracy.

---

## 🚀 Features

### Admin Dashboard
- **Bulk Data Import**: Import Students, Teachers, Subjects, Optional Subjects, Subject Mappings, and Student Classes via Excel/CSV (Maatwebsite Excel).
- **Teacher Management**: Add / Edit / Delete teaching staff; send notification emails to professors.
- **Access Code Generation**: Create unique “Gcodes” for each class session to record attendance.
- **Timetable & Teaching Hours**: Define time slots, assign subjects, and manage teaching staff assignments.
- **Notifications**: Send system-wide announcements and automated email alerts to users.
- **Export & Reporting**: Export master data and attendance reports in PDF/Excel.

### Counselor Dashboard
- **Student Profiles**: Create / Edit / Delete individual student records; apply filters and search.
- **Subject Management**: Create and list subjects; map subjects to specific classes or programs.
- **Bulk Uploads**: Import student rosters, optional subjects, and teacher assignments via separate import modules.

### Teacher Dashboard
- **Attendance Tracking**: Generate or delete one-time session codes; students enter codes to mark attendance.
- **Attendance Management**: View, edit, or delete attendance entries by code; filter by date, class, or student.
- **Report Generation**: Download attendance records as PDF (Dompdf) or Excel.
- **WhatsApp Notifications**: Send automated WhatsApp messages to absent students or their guardians (Twilio or custom API).

### Student Portal
- **Code Submission**: Enter a session-specific Gcode to register attendance.
- **Attendance History**: View personal attendance records and percentage calculations in real time.
- **Notifications**: Receive email/WhatsApp alerts regarding attendance status.

---

## 🛠️ Technologies & Integrations

- **Backend**  
  - Laravel 11 (PHP 8+)  
  - MySQL (Eloquent ORM)  
  - Custom Middleware: `ValidAdmin`, `ValidCounselor`, `ValidTeacher`
- **Frontend**  
  - Blade Templates  
  - Tailwind CSS  
  - Vite (Asset Bundler)  
  - Axios (AJAX requests)
- **Data Import/Export**  
  - [Maatwebsite Excel](https://github.com/Maatwebsite/Laravel-Excel)  
  - [Dompdf](https://github.com/dompdf/dompdf) for PDF generation
- **Notifications**  
  - Laravel Mail (SMTP)  
  - WhatsApp API integration (e.g., [Twilio](https://www.twilio.com/))  
  - Laravel Queues (Redis/Database)
- **Authentication & Security**  
  - Laravel’s built-in authentication scaffolding  
  - Role-based access control via middleware  
  - Environment variables (`.env`) for sensitive configuration

---

## 📋 Prerequisites

- PHP 8.1+  
- Composer  
- MySQL 5.7+ or MariaDB  
- Node.js & npm (for Vite/Tailwind)  
- Git (optional, for cloning)

---

## 🔧 Installation

1. **Clone the Repository**  
   ```bash
   git clone https://github.com/your-username/semcom-campusconnect.git
   cd semcom-campusconnect
