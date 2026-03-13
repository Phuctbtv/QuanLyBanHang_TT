# 🛍️ Hệ Thống Quản Lý Bán Hàng

<div align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-005C84?style=flat&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/Training-RIKI-FF6B6B?style=flat"/>
  <img src="https://img.shields.io/badge/Status-Completed-success?style=flat"/>
</div>

## 📌 Giới thiệu
Dự án **Quản lý bán hàng** được xây dựng bằng **PHP Core theo mô hình MVC** - sản phẩm training tại **RIKI**.

## 🏗️ Cấu trúc thư mục
```
QuanLyBanHang_TT/
├── Controller/      # Xử lý logic (Auth, Customer, Orders,...)
├── Model/           # Tương tác database
├── View/            # Giao diện (auth, products, orders,...)
├── config/          # Cấu hình Database.php
├── vendor/          # Thư viện Composer
├── index.php        # Entry point
└── Thiết kế db_test.mwb  # File thiết kế database
```

## ✨ Tính năng chính
- ✅ **Đăng nhập/Đăng ký** - Phân quyền người dùng
- ✅ **CRUD** - Khách hàng, nhân viên, sản phẩm, danh mục, đơn hàng
- ✅ **Thống kê** - Đơn hàng & KPI nhân viên theo tháng
- ✅ **Excel** - Import/Export dữ liệu

## 💻 Công nghệ
- **PHP Core** (OOP, MVC, PDO)
- **MySQL** (Thiết kế bằng MySQL Workbench)
- **HTML/CSS/JS** (Giao diện sinh động)
- **Composer** (Quản lý thư viện)
- **PhpSpreadsheet** (Xử lý Excel)

## 🚀 Cài đặt nhanh
```bash
git clone https://github.com/Phuctbtv/QuanLyBanHang_TT.git
cd QuanLyBanHang_TT
composer install
# Import database từ file .mwb
# Cấu hình config/Database.php
php -S localhost:8000
```

## 📧 Liên hệ
**Training tại:** RIKI  
**Học viên:** PhucPD - phamdaiphuc20003@gmail.com

---
<div align="center">⚡ Project training RIKI ⚡</div>
