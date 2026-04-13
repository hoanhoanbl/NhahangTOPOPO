# PROJECT SCOPE SUMMARY
## Hệ thống Đặt Bàn Nhà Hàng — Đã Chốt

> **Ngày chốt scope:** 2026-04-12
> **Source:** `C:\xampp\htdocs\DatBanNH\`
> **Tech:** Vanilla PHP / MySQL / XAMPP
> **Phân tích viên:** August — Project Analysis Consultant

---

## MỤC LỤC

1. [Tổng quan](#1-tổng-quan)
2. [Luật đặt bàn — Đã chốt](#2-luật-đặt-bàn--đã-chốt)
3. [Luồng đặt bàn chính](#3-luồng-đặt-bàn-chính)
4. [User Stories trong scope](#4-user-stories-trong-scope)
5. [User Stories loại bỏ](#5-user-stories-loại-bỏ)
6. [Phân tích source hiện tại](#6-phân-tích-source-hiện-tại)
7. [Thành phần giữ lại / ẩn / bỏ](#7-thành-phần-giữ-lại--ẩn--bỏ)
8. [Rủi ro và lưu ý kỹ thuật](#8-rủi-ro-và-lưu-ý-kỹ-thuật)

---

## 1. TỔNG QUAN

| Thông tin | Chi tiết |
|---|---|
| Mục tiêu | Đồ án trường — Booking-focused restaurant system |
| Nghiệp vụ chính | Đặt bàn + order món trước + thanh toán cọc |
| Người dùng | Khách hàng (public), Admin/Manager/Receptionist (back-office) |
| RBAC | 3 role: Admin / Manager / Receptionist |
| Thanh toán | Nút QR SePay — hiện để đẹp, không bắt buộc |
| Hoàn tiền | Không |

---

## 2. LUẬT ĐẶT BÀN — ĐÃ CHỐT

| Luật | Giá trị |
|---|---|
| Ngày thường (T2–T6) | Không cọc |
| T7 / CN / Lễ | Cọc **100,000đ** |
| Đặt món trước | Cọc **50% tiền món** đã chọn |
| Đặt món + T7/CN/Lễ | Cọc **50% bill + 100,000đ** (cộng dồn) |
| Lead time tối thiểu | **2 tiếng** trước giờ đến |
| Số khách | **Không giới hạn** |
| Đặt tối đa trước | **30 ngày** |
| Giờ mở cửa | **9:00 – 22:00** (cố định) |

### Cách tính deposit

```
deposit = 0
nếu T7/CN/Lễ:      deposit += 100,000
nếu có đặt món:    deposit += (tổng tiền món × 50%)

Ví dụ:
  - Ngày thứ 3, không đặt món  → deposit = 0đ
  - Ngày thứ 3, đặt 200,000đ   → deposit = 100,000đ
  - Thứ 7, không đặt món      → deposit = 100,000đ
  - Thứ 7, đặt 200,000đ       → deposit = 100,000 + 100,000 = 200,000đ
  - CN, đặt 300,000đ          → deposit = 100,000 + 150,000 = 250,000đ
```

---

## 3. LUỒNG ĐẶT BÀN CHÍNH

```
┌─────────────────────────────────────────────────┐
│  B1: Chọn chi nhánh → Ngày → Giờ → Số khách    │
│      ↳ Hệ thống tự detect T7/CN/Lễ            │
│         → Hiện cảnh báo deposit               │
└────────────────────┬────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  B2: Kiểm tra bàn trống (US-12)                │
│      → Trả về danh sách bàn khả dụng            │
│         + gợi ý nếu hết bàn                    │
└────────────────────┬────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  B3: Chọn món (tùy chọn) — US-15               │
│      ↳ Tính tổng tiền món                      │
│      ↳ Hiện cảnh báo deposit = 50%            │
└────────────────────┬────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  B4: Nhập thông tin liên hệ — US-13            │
│      Tên, SĐT, email, ghi chú                  │
└────────────────────┬────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  B5: Xác nhận                                   │
│  ┌─────────────────┐  ┌──────────────────────┐  │
│  │Thanh toán cọc   │  │Xác nhận đặt bàn      │  │
│  │→ Hiện QR SePay  │  │→ Lưu da_xac_nhan      │  │
│  │→ Tự quyết đóng  │  │→ Không cần CK        │  │
│  └─────────────────┘  └──────────────────────┘  │
└────────────────────┬────────────────────────────┘
                     ↓
┌─────────────────────────────────────────────────┐
│  B6: Trang chi tiết booking — US-18            │
│      Mã booking, QR, thông tin, chính sách      │
└─────────────────────────────────────────────────┘
```

---

## 4. USER STORIES TRONG SCOPE

### 4.1 Nền tảng & Bảo mật

| Mã | Tên | Mô tả |
|---|---|---|
| **US-01** | RBAC + Data Masking | 3 role: Admin / Manager / Receptionist. Mask SĐT ở UI. RBAC middleware. |

### 4.2 Cấu hình (Admin/Manager)

| Mã | Tên | Mô tả |
|---|---|---|
| **US-02** | Cấu hình chi nhánh | CRUD chi nhánh (tên, địa chỉ, hotline, giờ mở cửa) |
| **US-03** | Quản lý bàn | CRUD bàn, zone, sức chứa |
| **US-05** | Luật đặt bàn | Lead time, cutoff, ngày đặt tối đa, deposit rule |

### 4.3 Vận hành (Admin/Manager)

| Mã | Tên | Mô tả |
|---|---|---|
| **US-06** | Quản lý menu | CRUD món, category, ảnh, publish/unpublish |
| **US-07** | Cập nhật trạng thái món | Toggle còn hàng / hết hàng |

### 4.4 Khách hàng — Public (Không cần đăng nhập)

| Mã | Tên | Mô tả |
|---|---|---|
| **US-08** | Tìm kiếm & lọc | Filter theo quận, ngày, số người |
| **US-09** | Xem nhà hàng & menu | Public detail page, menu public |

### 4.5 Khách hàng — Có tài khoản

| Mã | Tên | Mô tả |
|---|---|---|
| **US-10** | Đăng nhập OTP | Gửi OTP SMS, verify, session restore |

### 4.6 Booking Engine — Core

| Mã | Tên | Mô tả |
|---|---|---|
| **US-12** | Kiểm tra bàn trống realtime | Availability engine: theo ngày/giờ/số người/chi nhánh |
| **US-13** | Nhập thông tin liên hệ | Tạo booking: tên, SĐT, số khách, ghi chú |
| **US-14** | Quản lý trạng thái booking | 4 trạng thái: chờ xác nhận, đã xác nhận, đã hủy, hoàn thành |
| **US-15** | Chọn món trước | Pre-order cart tích hợp luồng đặt bàn |
| **US-17** | Nút thanh toán cọc | QR SePay hiện số tiền deposit — không bắt buộc |
| **US-18** | Xem chi tiết booking | Trang confirmation: mã booking, thông tin, chính sách |

### 4.7 Vận hành tại quán

| Mã | Tên | Mô tả |
|---|---|---|
| **US-19** | Xem booking & sơ đồ bàn | Calendar/timeline view + seat map realtime |

### 4.8 Khách hàng — Self-service

| Mã | Tên | Mô tả |
|---|---|---|
| **US-21** | Đổi giờ / chỉnh sửa booking | Reschedule trong phạm vi policy + rule |
| **US-23** | Xem lịch sử & đặt lại | My Bookings page, re-book shortcut |

---

## 5. USER STORIES LOẠI BỎ

| Mã | Tên | Lý do |
|---|---|---|
| US-04 | Cấu hình ca & slot | Không cần — giờ cố định 9h–22h |
| US-11 | Social Login | OTP đã đủ |
| US-16 | Áp voucher | Không cần |
| US-20 | QR check-in | Receptionist tra tên/SĐT là đủ |
| US-22 | Hủy booking & hoàn cọc | Chỉ đổi status da_huy, không hoàn tiền |
| US-24 | Nhắc lịch | Không cần cho đồ án |
| US-25 | Live chat | Hotline có sẵn |
| US-26 | Xuất báo cáo | Không cần |
| US-28 | Performance monitoring | Không cần giai đoạn này |

---

## 6. PHÂN TÍCH SOURCE HIỆN TẠI

### 6.1 Kiến trúc source hiện tại

```
DatBanNH/
├── app/
│   ├── controllers/          # MVC controllers
│   │   ├── BookingController.php      ← Luồng A (đặt bàn cơ bản)
│   │   ├── BranchController.php       ← US-02 (CRUD chi nhánh)
│   │   ├── MenuController.php         ← US-06 + voucher API
│   │   ├── NhanVienController.php     ← Staff dashboard
│   │   └── AdminController.php        ← Admin panel
│   ├── models/
│   │   ├── BookingModel.php
│   │   ├── BranchModel.php
│   │   ├── MenuModel.php
│   │   └── NhanVienModel.php
│   └── views/
│       ├── admin/             # Back-office UI
│       ├── booking/           # Luồng A (create, payment, success)
│       ├── menu2/             # Luồng B (menu + cart) ← GIỮ
│       └── nhanvien/          # Staff views
├── config/
│   ├── database.php          ← Database connection
│   └── config.php             ← App config
├── includes/
│   ├── auth.php              ← Auth logic
│   └── EmailService.php      ← Email (tắt đi)
├── sepay/                    ← Payment standalone
│   ├── booking_sepay_webhook.php
│   ├── sepay_payment.php
│   └── invoice.php
├── database/
│   └── booking_restaurant.sql ← DB schema
└── libs/
    └── PHPMailer/            ← Email library (tắt đi)
```

### 6.2 Database — Bảng có trong source

| Bảng | US tương ứng | Trạng thái |
|---|---|---|
| `coso` | US-02 | ✅ Giữ — thêm field |
| `ban` | US-03 | ✅ Giữ |
| `monan` | US-06 | ✅ Giữ |
| `danhmuc` | US-06 | ✅ Giữ |
| `menu_coso` | US-06, US-07 | ✅ Giữ — thêm toggle UI |
| `dondatban` | US-13, US-14, US-17 | ✅ Giữ — mở rộng trạng thái |
| `dondatban_ban` | US-12 | ✅ Giữ |
| `chitietdondatban` | US-15 | ✅ Giữ — tích hợp luồng online |
| `khachhang` | US-13 | ✅ Giữ — thêm OTP flow |
| `nhanvien` | US-01 (RBAC) | ✅ Giữ — thêm role column |
| `uudai` | Không dùng | ⚠️ Giữ nguyên — ẩn đi |
| `uudai` | Không dùng | ⚠️ Giữ nguyên — ẩn đi |

### 6.3 Database — Bảng CẦN THÊM

| Bảng | US | Mô tả |
|---|---|---|
| `roles` | US-01 | Vai trò: admin / manager / receptionist |
| `permissions` | US-01 | Ma trận quyền |
| `branch_hours` | US-05 | Giờ mở cửa cố định 9:00–22:00 |
| `special_dates` | US-05 | Ngày lễ / ngày đặc biệt (override giờ) |
| `booking_rules` | US-05 | Lead time, cutoff, deposit rule |
| `booking_audit_logs` | US-05 | Log hành động hủy/đổi booking |

### 6.4 Hai luồng source — Xử lý thế nào

| Luồng | File | Xử lý |
|---|---|---|
| **Luồng A** (BookingController) | `booking/create.php`, `booking/payment.php`, `booking/success.php` | **HỢP NHẤT** vào `menu2/` — giữ UI, bỏ controller riêng |
| **Luồng B** (sepay/menu2) | `menu2/menu2.php`, `menu2/process-create.php` | **GIỮ** — đã có menu + cart + booking flow |

**Hướng đi chọn:** Dùng **Luồng B** làm nền tảng, tích hợp thêm:
- OTP login (US-10)
- Availability engine (US-12)
- Booking rules (US-05)
- Deposit calculation (US-17)

---

## 7. THÀNH PHẦN GIỮ LẠI / ẨN / BỎ

### ✅ GIỮ NGUYÊN — Đã khớp scope

| Thành phần | Lý do |
|---|---|
| `menu2/menu2.php` + `process-create.php` | Nền tảng US-15 Pre-order + booking |
| `sepay/` (folder + files) | Giữ nguyên, ẩn nav — webhook vẫn chạy |
| `BookingModel.php` | Dùng cho US-12, US-14, US-17 |
| `menu_coso.TinhTrang` | Dùng cho US-07 |
| `coso`, `ban`, `monan`, `danhmuc` | Dùng cho US-02, US-03, US-06 |
| `dondatban`, `dondatban_ban` | Dùng cho US-13, US-14, US-17 |
| `nhanvien` table | Dùng cho US-01 RBAC — thêm role column |
| `uudai` table | Không ảnh hưởng — không gọi trong flow |
| `AuthController.php` | Dùng cho admin/manager login |

### ⚠️ GIỮ — Cần sửa / mở rộng

| Thành phần | Cần làm |
|---|---|
| `menu2/menu2.php` | Thêm bước chọn ngày/giờ/số khách → check availability |
| `menu2/process-create.php` | Tích hợp OTP login, deposit calc, booking status |
| `sepay_payment.php` | Sửa: không bắt buộc thanh toán, vẫn xác nhận đặt |
| `BookingModel.php` | Thêm method availability engine, booking audit log |
| `BranchModel.php` | Thêm method kiểm tra giờ mở cửa |
| `nhanvien` table | Thêm cột `role` (admin/manager/receptionist) |
| RBAC middleware | Viết mới — kiểm tra role trước mỗi action |
| `app/views/admin/` | Thêm UI toggle stock (US-07), sơ đồ bàn (US-19) |

### 🔴 ẨN ĐI (không xóa)

| Thành phần | Xử lý |
|---|---|
| `booking/create.php` (Luồng A) | Ẩn nav, không xóa |
| `BookingController.php` (Luồng A) | Không dùng nữa |
| `EmailService.php` | Tắt gọi từ webhook — comment out |
| `app/views/admin/uudai/` | Ẩn menu voucher |
| `app/views/admin/user/` | Ẩn — quản lý nhân viên không cần |
| `NhanVienController.php` | Giữ cho login, ẩn các section khác |

### ❌ BỎ (hoặc không đụng đến)

| Thành phần | Lý do |
|---|---|
| Social login code | Không trong scope |
| Live chat integration | Không trong scope |
| Reporting/Export module | Không trong scope |
| Load test / APM code | Không trong scope |

---

## 8. RỦI RO VÀ LƯU Ý KỸ THUẬT

### 8.1 Rủi ro cao

| Rủi ro | Mức | Mô tả |
|---|---|---|
| **Double booking** | 🔴 | `layBanTrongTheoThoiGian()` chỉ check 2 giờ — cần mở rộng availability engine |
| **Hai luồng song song** | 🔴 | Cần hợp nhất sớm — chọn Luồng B làm nền tảng |
| **Không có shift config** | 🟠 | Không biết giờ nào bàn "có thể đặt" — dùng giờ cố định 9h–22h thay thế |

### 8.2 Rủi ro trung bình

| Rủi ro | Mức | Mô tả |
|---|---|---|
| **RBAC chưa có middleware** | 🟠 | Cần viết middleware kiểm tra role trước mỗi render |
| **Pre-order cart tách biệt** | 🟠 | Luồng A không có cart — cần tích hợp vào Luồng B |
| **Không có date table** | 🟠 | Cần bảng `special_dates` để detect ngày lễ Tết |

### 8.3 Thứ tự ưu tiên triển khai đề xuất

```
Giai đoạn 1 — Nền tảng (US-01, US-05, US-02):
  1. RBAC + role column trong nhanvien
  2. Branch hours + special_dates + booking_rules tables
  3. Deposit calculation logic

Giai đoạn 2 — Luồng đặt bàn (US-10, US-12, US-13, US-15):
  4. OTP login flow
  5. Availability engine
  6. Tích hợp pre-order cart vào Luồng B

Giai đoạn 3 — Thanh toán & xác nhận (US-17, US-18):
  7. Sửa sepay_payment.php — không bắt buộc thanh toán
  8. Trang confirmation booking

Giai đoạn 4 — Vận hành (US-19, US-21, US-23):
  9. Calendar view + seat map
  10. Reschedule/edit booking
  11. My Bookings page

Giai đoạn 5 — Hoàn thiện (US-03, US-06, US-07, US-08, US-09):
  12. CRUD bàn/menu + toggle stock
  13. Search & filter + public detail page
```

---

## PHỤ LỤC: FILE BACKLOG THAM CHIẾU

| File | Sheet | Mô tả |
|---|---|---|
| `product_backlog_hoan_chinh.xlsx` | `04_PRODUCT_BACKLOG` | Backlog gốc — 28 US |
| `ANALYSIS_scope_gap_report.md` | — | Phân tích gap chi tiết |

---

*Tài liệu này là output của quá trình phân tích scope cùng August — Project Analysis Consultant*
*Ngày: 2026-04-12*
