-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 15, 2025 lúc 02:04 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `booking_restaurant`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ban`
--

CREATE TABLE `ban` (
  `MaBan` int(11) NOT NULL,
  `MaCoSo` int(11) NOT NULL,
  `TenBan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SucChua` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `ban`
--

INSERT INTO `ban` (`MaBan`, `MaCoSo`, `TenBan`, `SucChua`) VALUES
(1, 2, 'T1-01', 6),
(2, 2, 'T1-01', 6),
(3, 11, 'T1-01', 8),
(4, 11, 'T1-02', 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdondatban`
--

CREATE TABLE `chitietdondatban` (
  `MaDon` int(11) NOT NULL,
  `MaMon` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietdondatban`
--

INSERT INTO `chitietdondatban` (`MaDon`, `MaMon`, `SoLuong`, `DonGia`) VALUES
(100, 15, 1, 75000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coso`
--

CREATE TABLE `coso` (
  `MaCoSo` int(11) NOT NULL,
  `TenCoSo` varchar(255) NOT NULL,
  `DienThoai` varchar(15) NOT NULL,
  `DiaChi` varchar(255) NOT NULL,
  `AnhUrl` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `coso`
--

INSERT INTO `coso` (`MaCoSo`, `TenCoSo`, `DienThoai`, `DiaChi`, `AnhUrl`) VALUES
(2, '10 Nguyễn Văn Huyên', '092278238', 'Thanh Khê', 'https://storage.quannhautudo.com/data/thumb_800/Data/images/product/2023/08/202308051004475343.webp'),
(3, '68 Láng Thượng', '0922782387', 'Ngũ Hành Sơn', 'https://storage.quannhautudo.com/data/thumb_800/Data/images/product/2023/05/202305111648358011.webp'),
(4, '505 Minh Khai', '0922782387', 'Sơn Trà', 'https://storage.quannhautudo.com/data/thumb_800/Data/images/product/2023/06/202306281114157262.webp'),
(5, 'Nguyễn Hữu Thọ (Linh Đàm)', '0922782387', 'Cẩm lệ', 'https://storage.quannhautudo.com/data/thumb_800/Data/images/product/2023/10/202310241151064241.webp'),
(11, '67A Phó Đức Chính', '0922782387', 'Hòa Xuân', 'https://storage.quannhautudo.com/data/thumb_800/Data/images/product/2024/10/202410041831456635.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `MaDM` int(11) NOT NULL,
  `TenDM` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`MaDM`, `TenDM`) VALUES
(26, 'CÁ CÁC MÓN'),
(29, 'CƠM - MỲ'),
(20, 'DÊ TƯƠI'),
(21, 'ĐỒ UỐNG'),
(25, 'HẢI SẢN'),
(24, 'LẨU'),
(27, 'MÓN ĂN CHƠI'),
(18, 'MÓN MỚI'),
(19, 'MÓN CHAY'),
(39, 'MÓN NƯỚNG'),
(30, 'NƯỚNG TẠI BÀN'),
(23, 'RAU XANH'),
(28, 'SALAD - NỘM'),
(17, 'TẤT CẢ'),
(22, 'THIẾT BẢN\r\n'),
(31, 'TRÀ TRÁI CÂY & COCKTAIL');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dondatban`
--

CREATE TABLE `dondatban` (
  `MaDon` int(11) NOT NULL,
  `MaKH` int(11) NOT NULL,
  `MaCoSo` int(11) NOT NULL,
  `MaUD` int(11) DEFAULT NULL,
  `MaNV_XacNhan` int(11) DEFAULT NULL,
  `SoLuongKH` int(11) NOT NULL,
  `ThoiGianBatDau` datetime NOT NULL,
  `GhiChu` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TrangThai` enum('cho_xac_nhan','da_xac_nhan','da_huy','hoan_thanh') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cho_xac_nhan',
  `ThoiGianTao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `dondatban`
--

INSERT INTO `dondatban` (`MaDon`, `MaKH`, `MaCoSo`, `MaUD`, `MaNV_XacNhan`, `SoLuongKH`, `ThoiGianBatDau`, `GhiChu`, `TrangThai`, `ThoiGianTao`) VALUES
(95, 2, 2, NULL, 111, 1, '2025-09-30 21:19:00', 'Đặt bàn tại quán [Tự động hủy do quá hạn]', 'hoan_thanh', '2025-09-30 21:19:52'),
(96, 2, 2, NULL, 111, 1, '2025-09-30 21:21:00', 'Đặt bàn tại quán [Tự động hủy do quá hạn]', 'hoan_thanh', '2025-09-30 21:21:46'),
(97, 2, 2, NULL, 111, 1, '2025-09-30 21:22:00', 'Đặt bàn tại quán [Tự động hủy do quá hạn]', 'hoan_thanh', '2025-09-30 21:22:19'),
(98, 2, 2, NULL, 111, 1, '2025-09-30 21:50:00', 'Đặt bàn tại quán [Tự động hủy do quá hạn]', 'hoan_thanh', '2025-09-30 21:50:36'),
(99, 2, 2, NULL, 111, 1, '2025-09-30 21:55:00', 'Đặt bàn tại quán [Tự động hủy do quá hạn]', 'hoan_thanh', '2025-09-30 21:55:44'),
(100, 2, 11, NULL, 111, 1, '2025-09-30 23:07:00', 'Đặt bàn tại quán [Tự động hủy do quá hạn]', 'hoan_thanh', '2025-09-30 23:07:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dondatban_ban`
--

CREATE TABLE `dondatban_ban` (
  `MaDon` int(11) NOT NULL,
  `MaBan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dondatban_ban`
--

INSERT INTO `dondatban_ban` (`MaDon`, `MaBan`) VALUES
(100, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` int(11) NOT NULL,
  `TenKH` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `SDT` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `TenKH`, `Email`, `SDT`) VALUES
(1, 'Vũ Tín', 'vutin123@gmail.com', '0987654321'),
(2, 'Khách hàng tại quán', '', ''),
(3, 'Admin System', 'admin@system.local', '0000000000'),
(57, 'tín', '', '0362889901');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menu_coso`
--

CREATE TABLE `menu_coso` (
  `MaCoSo` int(11) NOT NULL,
  `MaMon` int(11) NOT NULL,
  `Gia` decimal(10,2) NOT NULL,
  `TinhTrang` enum('con_hang','het_hang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'con_hang'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Đang đổ dữ liệu cho bảng `menu_coso`
--

INSERT INTO `menu_coso` (`MaCoSo`, `MaMon`, `Gia`, `TinhTrang`) VALUES
(2, 1, 149000.00, 'con_hang'),
(2, 2, 150000.00, 'con_hang'),
(2, 3, 120000.00, 'con_hang'),
(2, 4, 180000.00, 'con_hang'),
(2, 5, 90000.00, 'con_hang'),
(2, 6, 200000.00, 'con_hang'),
(2, 7, 130000.00, 'con_hang'),
(2, 8, 160000.00, 'con_hang'),
(2, 9, 110000.00, 'con_hang'),
(2, 10, 95000.00, 'con_hang'),
(2, 11, 140000.00, 'con_hang'),
(2, 12, 11111.00, 'con_hang'),
(2, 13, 170000.00, 'con_hang'),
(2, 14, 85000.00, 'con_hang'),
(2, 15, 75000.00, 'con_hang'),
(2, 16, 25000.00, 'con_hang'),
(2, 17, 30000.00, 'con_hang'),
(2, 18, 15000.00, 'con_hang'),
(2, 19, 45000.00, 'con_hang'),
(2, 20, 120000.00, 'con_hang'),
(2, 21, 180000.00, 'con_hang'),
(2, 22, 220000.00, 'con_hang'),
(2, 23, 60000.00, 'con_hang'),
(2, 24, 80000.00, 'con_hang'),
(3, 1, 149000.00, 'con_hang'),
(3, 2, 150000.00, 'con_hang'),
(3, 3, 120000.00, 'con_hang'),
(3, 4, 180000.00, 'con_hang'),
(3, 5, 90000.00, 'con_hang'),
(3, 6, 200000.00, 'con_hang'),
(3, 7, 130000.00, 'con_hang'),
(3, 8, 160000.00, 'con_hang'),
(3, 9, 110000.00, 'con_hang'),
(3, 10, 95000.00, 'con_hang'),
(3, 11, 140000.00, 'con_hang'),
(3, 12, 11111.00, 'con_hang'),
(3, 13, 170000.00, 'con_hang'),
(3, 14, 85000.00, 'con_hang'),
(3, 15, 75000.00, 'con_hang'),
(3, 16, 25000.00, 'con_hang'),
(3, 17, 30000.00, 'con_hang'),
(3, 18, 15000.00, 'con_hang'),
(3, 19, 45000.00, 'con_hang'),
(3, 20, 120000.00, 'con_hang'),
(3, 21, 180000.00, 'con_hang'),
(3, 22, 220000.00, 'con_hang'),
(3, 23, 60000.00, 'con_hang'),
(3, 24, 80000.00, 'con_hang'),
(3, 25, 100000.00, 'con_hang'),
(3, 26, 100000.00, 'con_hang'),
(3, 27, 100000.00, 'con_hang'),
(3, 28, 100000.00, 'con_hang'),
(3, 29, 100000.00, 'con_hang'),
(3, 30, 100000.00, 'con_hang'),
(3, 31, 100000.00, 'con_hang'),
(3, 32, 100000.00, 'con_hang'),
(3, 33, 100000.00, 'con_hang'),
(3, 34, 100000.00, 'con_hang'),
(3, 35, 100000.00, 'con_hang'),
(3, 36, 100000.00, 'con_hang'),
(3, 37, 100000.00, 'con_hang'),
(3, 38, 100000.00, 'con_hang'),
(3, 39, 100000.00, 'con_hang'),
(3, 40, 100000.00, 'con_hang'),
(3, 41, 100000.00, 'con_hang'),
(3, 42, 100000.00, 'con_hang'),
(3, 43, 100000.00, 'con_hang'),
(3, 44, 100000.00, 'con_hang'),
(3, 45, 100000.00, 'con_hang'),
(3, 46, 100000.00, 'con_hang'),
(3, 47, 100000.00, 'con_hang'),
(3, 48, 100000.00, 'con_hang'),
(3, 49, 100000.00, 'con_hang'),
(3, 50, 100000.00, 'con_hang'),
(3, 51, 100000.00, 'con_hang'),
(3, 52, 100000.00, 'con_hang'),
(3, 53, 100000.00, 'con_hang'),
(3, 54, 100000.00, 'con_hang'),
(3, 55, 100000.00, 'con_hang'),
(3, 56, 100000.00, 'con_hang'),
(3, 57, 100000.00, 'con_hang'),
(3, 58, 100000.00, 'con_hang'),
(3, 59, 100000.00, 'con_hang'),
(3, 60, 100000.00, 'con_hang'),
(3, 61, 100000.00, 'con_hang'),
(3, 62, 100000.00, 'con_hang'),
(3, 63, 100000.00, 'con_hang'),
(3, 64, 100000.00, 'con_hang'),
(3, 65, 100000.00, 'con_hang'),
(3, 66, 100000.00, 'con_hang'),
(3, 67, 100000.00, 'con_hang'),
(3, 68, 100000.00, 'con_hang'),
(3, 69, 100000.00, 'con_hang'),
(3, 70, 100000.00, 'con_hang'),
(3, 71, 100000.00, 'con_hang'),
(3, 72, 100000.00, 'con_hang'),
(3, 73, 100000.00, 'con_hang'),
(3, 74, 100000.00, 'con_hang'),
(3, 75, 100000.00, 'con_hang'),
(3, 76, 100000.00, 'con_hang'),
(3, 77, 100000.00, 'con_hang'),
(3, 78, 100000.00, 'con_hang'),
(3, 79, 100000.00, 'con_hang'),
(3, 80, 100000.00, 'con_hang'),
(3, 81, 100000.00, 'con_hang'),
(3, 82, 100000.00, 'con_hang'),
(3, 83, 100000.00, 'con_hang'),
(3, 84, 100000.00, 'con_hang'),
(3, 85, 100000.00, 'con_hang'),
(3, 86, 100000.00, 'con_hang'),
(3, 87, 100000.00, 'con_hang'),
(3, 88, 100000.00, 'con_hang'),
(3, 89, 100000.00, 'con_hang'),
(3, 90, 100000.00, 'con_hang'),
(3, 91, 100000.00, 'con_hang'),
(3, 92, 100000.00, 'con_hang'),
(3, 93, 100000.00, 'con_hang'),
(3, 94, 100000.00, 'con_hang'),
(3, 95, 100000.00, 'con_hang'),
(3, 96, 100000.00, 'con_hang'),
(3, 97, 100000.00, 'con_hang'),
(3, 98, 100000.00, 'con_hang'),
(3, 99, 100000.00, 'con_hang'),
(3, 100, 100000.00, 'con_hang'),
(3, 101, 100000.00, 'con_hang'),
(3, 102, 100000.00, 'con_hang'),
(3, 103, 100000.00, 'con_hang'),
(3, 104, 100000.00, 'con_hang'),
(3, 105, 100000.00, 'con_hang'),
(3, 106, 100000.00, 'con_hang'),
(3, 107, 100000.00, 'con_hang'),
(3, 108, 100000.00, 'con_hang'),
(3, 109, 100000.00, 'con_hang'),
(3, 110, 100000.00, 'con_hang'),
(3, 111, 100000.00, 'con_hang'),
(3, 112, 100000.00, 'con_hang'),
(3, 113, 100000.00, 'con_hang'),
(3, 114, 100000.00, 'con_hang'),
(3, 115, 100000.00, 'con_hang'),
(3, 116, 100000.00, 'con_hang'),
(4, 1, 149000.00, 'con_hang'),
(4, 2, 150000.00, 'con_hang'),
(4, 3, 120000.00, 'con_hang'),
(4, 4, 180000.00, 'con_hang'),
(4, 5, 90000.00, 'con_hang'),
(4, 6, 200000.00, 'con_hang'),
(4, 7, 130000.00, 'con_hang'),
(4, 8, 160000.00, 'con_hang'),
(4, 9, 110000.00, 'con_hang'),
(4, 10, 95000.00, 'con_hang'),
(4, 11, 140000.00, 'con_hang'),
(4, 12, 11111.00, 'con_hang'),
(4, 13, 170000.00, 'con_hang'),
(4, 14, 85000.00, 'con_hang'),
(4, 15, 75000.00, 'con_hang'),
(4, 16, 25000.00, 'con_hang'),
(4, 17, 30000.00, 'con_hang'),
(4, 18, 15000.00, 'con_hang'),
(4, 19, 45000.00, 'con_hang'),
(4, 20, 120000.00, 'con_hang'),
(4, 21, 180000.00, 'con_hang'),
(4, 22, 220000.00, 'con_hang'),
(4, 23, 60000.00, 'con_hang'),
(4, 24, 80000.00, 'con_hang'),
(4, 25, 100000.00, 'con_hang'),
(4, 26, 100000.00, 'con_hang'),
(4, 27, 100000.00, 'con_hang'),
(4, 28, 100000.00, 'con_hang'),
(4, 29, 100000.00, 'con_hang'),
(4, 30, 100000.00, 'con_hang'),
(4, 31, 100000.00, 'con_hang'),
(4, 32, 100000.00, 'con_hang'),
(4, 33, 100000.00, 'con_hang'),
(4, 34, 100000.00, 'con_hang'),
(4, 35, 100000.00, 'con_hang'),
(4, 36, 100000.00, 'con_hang'),
(4, 37, 100000.00, 'con_hang'),
(4, 38, 100000.00, 'con_hang'),
(4, 39, 100000.00, 'con_hang'),
(4, 40, 100000.00, 'con_hang'),
(4, 41, 100000.00, 'con_hang'),
(4, 42, 100000.00, 'con_hang'),
(4, 43, 100000.00, 'con_hang'),
(4, 44, 100000.00, 'con_hang'),
(4, 45, 100000.00, 'con_hang'),
(4, 46, 100000.00, 'con_hang'),
(4, 47, 100000.00, 'con_hang'),
(4, 48, 100000.00, 'con_hang'),
(4, 49, 100000.00, 'con_hang'),
(4, 50, 100000.00, 'con_hang'),
(4, 51, 100000.00, 'con_hang'),
(4, 52, 100000.00, 'con_hang'),
(4, 53, 100000.00, 'con_hang'),
(4, 54, 100000.00, 'con_hang'),
(4, 55, 100000.00, 'con_hang'),
(4, 56, 100000.00, 'con_hang'),
(4, 57, 100000.00, 'con_hang'),
(4, 58, 100000.00, 'con_hang'),
(4, 59, 100000.00, 'con_hang'),
(4, 60, 100000.00, 'con_hang'),
(4, 61, 100000.00, 'con_hang'),
(4, 62, 100000.00, 'con_hang'),
(4, 63, 100000.00, 'con_hang'),
(4, 64, 100000.00, 'con_hang'),
(4, 65, 100000.00, 'con_hang'),
(4, 66, 100000.00, 'con_hang'),
(4, 67, 100000.00, 'con_hang'),
(4, 68, 100000.00, 'con_hang'),
(4, 69, 100000.00, 'con_hang'),
(4, 70, 100000.00, 'con_hang'),
(4, 71, 100000.00, 'con_hang'),
(4, 72, 100000.00, 'con_hang'),
(4, 73, 100000.00, 'con_hang'),
(4, 74, 100000.00, 'con_hang'),
(4, 75, 100000.00, 'con_hang'),
(4, 76, 100000.00, 'con_hang'),
(4, 77, 100000.00, 'con_hang'),
(4, 78, 100000.00, 'con_hang'),
(4, 79, 100000.00, 'con_hang'),
(4, 80, 100000.00, 'con_hang'),
(4, 81, 100000.00, 'con_hang'),
(4, 82, 100000.00, 'con_hang'),
(4, 83, 100000.00, 'con_hang'),
(4, 84, 100000.00, 'con_hang'),
(4, 85, 100000.00, 'con_hang'),
(4, 86, 100000.00, 'con_hang'),
(4, 87, 100000.00, 'con_hang'),
(4, 88, 100000.00, 'con_hang'),
(4, 89, 100000.00, 'con_hang'),
(4, 90, 100000.00, 'con_hang'),
(4, 91, 100000.00, 'con_hang'),
(4, 92, 100000.00, 'con_hang'),
(4, 93, 100000.00, 'con_hang'),
(4, 94, 100000.00, 'con_hang'),
(4, 95, 100000.00, 'con_hang'),
(4, 96, 100000.00, 'con_hang'),
(4, 97, 100000.00, 'con_hang'),
(4, 98, 100000.00, 'con_hang'),
(4, 99, 100000.00, 'con_hang'),
(4, 100, 100000.00, 'con_hang'),
(4, 101, 100000.00, 'con_hang'),
(4, 102, 100000.00, 'con_hang'),
(4, 103, 100000.00, 'con_hang'),
(4, 104, 100000.00, 'con_hang'),
(4, 105, 100000.00, 'con_hang'),
(4, 106, 100000.00, 'con_hang'),
(4, 107, 100000.00, 'con_hang'),
(4, 108, 100000.00, 'con_hang'),
(4, 109, 100000.00, 'con_hang'),
(4, 110, 100000.00, 'con_hang'),
(4, 111, 100000.00, 'con_hang'),
(4, 112, 100000.00, 'con_hang'),
(4, 113, 100000.00, 'con_hang'),
(4, 114, 100000.00, 'con_hang'),
(4, 115, 100000.00, 'con_hang'),
(4, 116, 100000.00, 'con_hang'),
(5, 1, 149000.00, 'con_hang'),
(5, 2, 150000.00, 'con_hang'),
(5, 3, 120000.00, 'con_hang'),
(5, 4, 180000.00, 'con_hang'),
(5, 5, 90000.00, 'con_hang'),
(5, 6, 200000.00, 'con_hang'),
(5, 7, 130000.00, 'con_hang'),
(5, 8, 160000.00, 'con_hang'),
(5, 9, 110000.00, 'con_hang'),
(5, 10, 95000.00, 'con_hang'),
(5, 11, 140000.00, 'con_hang'),
(5, 12, 11111.00, 'con_hang'),
(5, 13, 170000.00, 'con_hang'),
(5, 14, 85000.00, 'con_hang'),
(5, 15, 75000.00, 'con_hang'),
(5, 16, 25000.00, 'con_hang'),
(5, 17, 30000.00, 'con_hang'),
(5, 18, 15000.00, 'con_hang'),
(5, 19, 45000.00, 'con_hang'),
(5, 20, 120000.00, 'con_hang'),
(5, 21, 180000.00, 'con_hang'),
(5, 22, 220000.00, 'con_hang'),
(5, 23, 60000.00, 'con_hang'),
(5, 24, 80000.00, 'con_hang'),
(5, 25, 100000.00, 'con_hang'),
(5, 26, 100000.00, 'con_hang'),
(5, 27, 100000.00, 'con_hang'),
(5, 28, 100000.00, 'con_hang'),
(5, 29, 100000.00, 'con_hang'),
(5, 30, 100000.00, 'con_hang'),
(5, 31, 100000.00, 'con_hang'),
(5, 32, 100000.00, 'con_hang'),
(5, 33, 100000.00, 'con_hang'),
(5, 34, 100000.00, 'con_hang'),
(5, 35, 100000.00, 'con_hang'),
(5, 36, 100000.00, 'con_hang'),
(5, 37, 100000.00, 'con_hang'),
(5, 38, 100000.00, 'con_hang'),
(5, 39, 100000.00, 'con_hang'),
(5, 40, 100000.00, 'con_hang'),
(5, 41, 100000.00, 'con_hang'),
(5, 42, 100000.00, 'con_hang'),
(5, 43, 100000.00, 'con_hang'),
(5, 44, 100000.00, 'con_hang'),
(5, 45, 100000.00, 'con_hang'),
(5, 46, 100000.00, 'con_hang'),
(5, 47, 100000.00, 'con_hang'),
(5, 48, 100000.00, 'con_hang'),
(5, 49, 100000.00, 'con_hang'),
(5, 50, 100000.00, 'con_hang'),
(5, 51, 100000.00, 'con_hang'),
(5, 52, 100000.00, 'con_hang'),
(5, 53, 100000.00, 'con_hang'),
(5, 54, 100000.00, 'con_hang'),
(5, 55, 100000.00, 'con_hang'),
(5, 56, 100000.00, 'con_hang'),
(5, 57, 100000.00, 'con_hang'),
(5, 58, 100000.00, 'con_hang'),
(5, 59, 100000.00, 'con_hang'),
(5, 60, 100000.00, 'con_hang'),
(5, 61, 100000.00, 'con_hang'),
(5, 62, 100000.00, 'con_hang'),
(5, 63, 100000.00, 'con_hang'),
(5, 64, 100000.00, 'con_hang'),
(5, 65, 100000.00, 'con_hang'),
(5, 66, 100000.00, 'con_hang'),
(5, 67, 100000.00, 'con_hang'),
(5, 68, 100000.00, 'con_hang'),
(5, 69, 100000.00, 'con_hang'),
(5, 70, 100000.00, 'con_hang'),
(5, 71, 100000.00, 'con_hang'),
(5, 72, 100000.00, 'con_hang'),
(5, 73, 100000.00, 'con_hang'),
(5, 74, 100000.00, 'con_hang'),
(5, 75, 100000.00, 'con_hang'),
(5, 76, 100000.00, 'con_hang'),
(5, 77, 100000.00, 'con_hang'),
(5, 78, 100000.00, 'con_hang'),
(5, 79, 100000.00, 'con_hang'),
(5, 80, 100000.00, 'con_hang'),
(5, 81, 100000.00, 'con_hang'),
(5, 82, 100000.00, 'con_hang'),
(5, 83, 100000.00, 'con_hang'),
(5, 84, 100000.00, 'con_hang'),
(5, 85, 100000.00, 'con_hang'),
(5, 86, 100000.00, 'con_hang'),
(5, 87, 100000.00, 'con_hang'),
(5, 88, 100000.00, 'con_hang'),
(5, 89, 100000.00, 'con_hang'),
(5, 90, 100000.00, 'con_hang'),
(5, 91, 100000.00, 'con_hang'),
(5, 92, 100000.00, 'con_hang'),
(5, 93, 100000.00, 'con_hang'),
(5, 94, 100000.00, 'con_hang'),
(5, 95, 100000.00, 'con_hang'),
(5, 96, 100000.00, 'con_hang'),
(5, 97, 100000.00, 'con_hang'),
(5, 98, 100000.00, 'con_hang'),
(5, 99, 100000.00, 'con_hang'),
(5, 100, 100000.00, 'con_hang'),
(5, 101, 100000.00, 'con_hang'),
(5, 102, 100000.00, 'con_hang'),
(5, 103, 100000.00, 'con_hang'),
(5, 104, 100000.00, 'con_hang'),
(5, 105, 100000.00, 'con_hang'),
(5, 106, 100000.00, 'con_hang'),
(5, 107, 100000.00, 'con_hang'),
(5, 108, 100000.00, 'con_hang'),
(5, 109, 100000.00, 'con_hang'),
(5, 110, 100000.00, 'con_hang'),
(5, 111, 100000.00, 'con_hang'),
(5, 112, 100000.00, 'con_hang'),
(5, 113, 100000.00, 'con_hang'),
(5, 114, 100000.00, 'con_hang'),
(5, 115, 100000.00, 'con_hang'),
(5, 116, 100000.00, 'con_hang'),
(11, 1, 14900.00, 'con_hang'),
(11, 2, 150000.00, 'con_hang'),
(11, 3, 120000.00, 'con_hang'),
(11, 4, 180000.00, 'con_hang'),
(11, 5, 90000.00, 'con_hang'),
(11, 6, 200000.00, 'con_hang'),
(11, 7, 130000.00, 'con_hang'),
(11, 8, 160000.00, 'con_hang'),
(11, 9, 110000.00, 'con_hang'),
(11, 10, 95000.00, 'con_hang'),
(11, 11, 140000.00, 'con_hang'),
(11, 12, 11111.00, 'con_hang'),
(11, 13, 170000.00, 'con_hang'),
(11, 14, 85000.00, 'con_hang'),
(11, 15, 75000.00, 'con_hang'),
(11, 16, 25000.00, 'con_hang'),
(11, 17, 30000.00, 'con_hang'),
(11, 18, 15000.00, 'con_hang'),
(11, 19, 45000.00, 'con_hang'),
(11, 20, 120000.00, 'con_hang'),
(11, 21, 180000.00, 'con_hang'),
(11, 22, 220000.00, 'con_hang'),
(11, 23, 60000.00, 'con_hang'),
(11, 24, 80000.00, 'con_hang');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monan`
--

CREATE TABLE `monan` (
  `MaMon` int(11) NOT NULL,
  `MaDM` int(11) NOT NULL,
  `TenMon` varchar(255) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `HinhAnhURL` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `monan`
--

INSERT INTO `monan` (`MaMon`, `MaDM`, `TenMon`, `MoTa`, `HinhAnhURL`) VALUES
(1, 18, 'Nộ da trâu xoài non', 'Nộm da trâu xoài non, với hương vị chua chua, cay cay, dai dai của da trâu và vị thanh mát của xoài non, rất thích hợp để nhậu. Món này vừa có tác dụng kích thích vị giác, vừa có thể ăn kèm với các MÓN CHAY khác mà không gây ngán.\r\n', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271649402242.webp'),
(2, 18, 'Khoai tây chiên Hongkong', 'Khoai tây được cắt khúc lớn, chiên giòn bên ngoài – mềm ngọt bên trong theo kiểu Hồng Kông. Rắc thêm bột gia vị mặn ngọt rất vừa miệng, thích hợp cho cả người lớn lẫn trẻ nhỏ.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506281348304201.webp'),
(3, 18, 'Salad xà lách cá trích ép trứng', 'Món salad này là sự hòa quyện tuyệt vời giữa những miếng cá trích mềm béo, quyện cùng phần trứng cá dậy vị. Điểm nhấn đặc biệt đến từ phần sốt chua nhẹ giúp cân bằng vị giác. Đây là một món ăn không chỉ ngon miệng mà còn cực kỳ giải ngấy. Đây chắc chắn sẽ là lựa chọn hoàn hảo để làm mới khẩu vị trong bữa tiệc của bạn!', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271718237849.webp'),
(4, 18, 'Salad xà lách hải sản', 'Sự kết hợp hoàn hảo giữa vị ngọt tự nhiên của tôm tươi giòn sần sật, thanh cua dai mềm cùng xà lách tươi giòn, cà chua bi ngọt mát và củ cải đỏ giòn cay nhẹ. Tất cả hòa quyện trong lớp sốt đặc biệt chua ngọt thanh mát, phảng phất hương thơm của chanh tươi và rau thơm, giúp giải ngấy cực hiệu quả sau những món chiên nướng đậm vị.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271717089.webp'),
(5, 18, 'Gỏi bò cà pháo đồng quê', ' Cà pháo giòn tan trộn cùng bò, dứa, khế chuối xanh, rau răm, nước mắm tỏi ớt tạo nên một món ăn dân dã mà đậm đà. Vị chua, cay, mặn, ngọt hòa quyện, gợi nhớ vị đồng quê thân quen – càng nhai càng thấm.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271716275294.webp'),
(6, 18, 'Gỏi heo nướng trộn thính', 'Thịt heo nướng thơm lừng được xắt lát mỏng, trộn đều với thính gạo, lá chanh, rau thơm, mang lại hương vị bùi béo – thơm nồng rất bắt mồi. Cắn miếng thịt mềm, chấm nước mắm chua ngọt là “cuốn” miệng khỏi bàn.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/20250627171546088.webp'),
(7, 18, 'Gỏi chân gà trộn thính', NULL, 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271714549081.webp'),
(8, 18, 'Gỏi sứa sốt thái cải cay', 'Sứa giòn mát kết hợp cùng xoài xanh, rau thơm và nước sốt Thái cay chua ngọt dậy vị, làm nên món khai vị cực kỳ “gắt” cho dân nhậu. Món này vừa giải ngấy, vừa đánh thức vị giác ngay từ đũa đầu tiên.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271714066553.webp'),
(9, 18, 'Ốc hương ủ muối thảo mộc', 'Ốc hương ủ muối là một sự lựa chọn tuyệt vời cho những buổi nhâm nhi cùng bạn bè.\r\nNhững con ốc hương tươi ngon được ủ cùng muối biển tinh khiết tạo nên hương vị đậm đà, thơm lừng. Khi thưởng thức, cảm giác giòn béo, dai ngon và đậm đà lan tỏa trong khoang miệng, làm say mê từng miếng. Kết hợp cùng tách bia lạnh hoặc ly rượu đế thơm nồng, món ốc hương ủ muối trở thành sự lựa chọn không thể bỏ qua cho những buổi lai rai vui vẻ.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271713075933.webp'),
(10, 18, 'Ếch sốt tiêu gừng chua cay', 'Thịt ếch săn chắc xào cùng tiêu xanh, gừng tươi, cho ra món ăn thơm lừng, cay nhẹ, ấm bụng. Món này rất hợp trời mưa, ăn kèm cơm nóng hoặc nhậu đều “bắt vị” tuyệt đối.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp'),
(11, 18, 'Bạch tuộc sốt thái cay', 'Bạch tuộc giòn tươi, tẩm sốt Thái cay nồng đậm vị, vừa đưa miệng vừa “đưa tâm trạng” lên cao. Món này là lựa chọn hoàn hảo cho hội thích nhắm mồi kiểu mạnh – càng ăn càng cuốn.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271711027431.webp'),
(12, 18, 'Dồi dê', ' Dồi dê thơm mềm, bên trong đầy đặn với nhân đậm đà, chấm cùng mắm tôm hoặc tương gừng là chuẩn bài. Món này lên bàn là đảm bảo “nhấc đũa không muốn dừng”.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271710002288.webp'),
(13, 18, 'Tôm sú ủ muối thảo mộc', 'Tôm sú ủ muối thảo mộc là một trong những món ăn \"nên thử\" tại Tự Do\r\nTôm có màu cam đẹp mắt, thịt tôm sú chắc nịch, vẫn giữ được vị ngọt tự nhiên vốn có lại còn trở nên đậm đà hơn khi chấm cùng muối tiêu chanh chua chua, mặn mặn kích thích vị giác, rất thích hợp để nhâm nhi cùng những cốc bia mát lạnh.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271709128585.webp'),
(14, 18, 'Trâu xào rau muống', NULL, 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271706594276.webp'),
(15, 18, 'Lợn mán nướng giềng mẻ', 'Thịt lợn mán với vị ngọt tự nhiên, khi được ướp với riềng, mẻ, sả, và các loại gia vị khác, rồi nướng lên sẽ tạo nên hương vị thơm ngon, đậm đà, rất đưa mồi. Khi ăn, cảm giác mềm mọng của thịt hòa quyện cùng vị chua nhẹ và chút cay của giềng, đem lại sự kích thích vị giác một cách mãnh liệt. Món lợn mán nướng giềng mẻ cực hợp để làm mồi nhậu cùng bia lạnh.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271704483259.webp'),
(16, 18, 'Nầm sữa nướng giềng mẻ', ' Nầm sữa nướng giềng mẻ là MÓN CHAY quen thuộc và được nhiều người yêu thích, đặc biệt là trong các buổi tụ tập bạn bè.\r\n\r\nHương vị đậm đà của riềng, mẻ, cùng với vị béo của nầm sữa khi nướng lên sẽ kích thích vị giác, khiến món ăn càng thêm hấp dẫn và đưa miệng. Kết hợp với bia hoặc các loại đồ uống có cồn khác, món này sẽ tạo nên một bữa nhậu thú vị và đáng nhớ.\r\n', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271703552855.webp'),
(17, 18, 'Lạp xưởng tây bắc xào dân tộc', ' Lạp xưởng chiên vàng óng, thơm ngậy, béo nhẹ nhưng không hề ngấy. Món này tưởng đơn giản mà lại gợi nhớ hương vị quê nhà, thích hợp để lai rai đầu giờ hay lót dạ nhẹ trước bữa chính.\r\n', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271659588227.webp'),
(18, 18, 'Cá dưa chua tứ xuyên', 'Cá quả được om nguyên con, thịt cá chắc nịch, không bã, miếng nào miếng nấy ngấm nước om đậm đà. Kèm theo là mấy miếng đậu phụ nướng vàng ươm, béo ngậy, cùng những miếng tiết heo mềm mịn, ngấm vị, cắn vào tan chảy trong miệng. Đặc biệt, nồi còn có thêm đủ thứ \"ăn kèm\" cực chất như ngó xuân giòn sần sật, lạc sống bùi bùi, nấm, mộc nhĩ... đảm bảo nhậu là tới bến! Cái \"đỉnh\" của món này chính là hương vị chua cay tê đặc trưng từ dưa cải muối vừa tới và hạt tê, ăn một miếng là muốn gắp thêm miếng nữa! Tất cả hòa quyện lại tạo nên một nước dùng đậm đà, khó cưỡng. Nồi này vừa có thể ăn kèm bún cho chắc bụng, lại vừa là món \"mồi\" chính để anh em lai rai. Một nồi thế này phù hợp cho 3-4 người chiến thoải mái, đảm bảo cuộc vui thêm phần trọn vẹn!', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271658346999.webp'),
(19, 18, 'Trâu tươi cháy tỏi', ' Trâu tươi cháy tỏi là món ăn cực kì \"tốn bia\" tại Tự Do.\r\nVị thịt ngọt thanh, đậm đà của thịt trâu hòa quyện cùng lớp gia vị tỏi cay nồng độc đáo đã khiến món ăn này rất được yêu thích trong lòng khách hàng. Món ăn này là một lựa chọn hoàn hảo vừa có thể ăn kèm cơm, vừa là MÓN CHAY lý tưởng.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271652467121.webp'),
(20, 18, 'Nầm sữa cháy tỏi', ' Nầm sữa cháy tỏi là MÓN CHAY \"đỉnh của chóp\" dành cho những tín đồ yêu thích hương vị đậm đà, thơm nồng và béo ngậy.\r\nMiếng nầm mềm mại, dai dai, khi cắn vào lớp vỏ ngoài đã vàng giòn, hơi cháy chút xíu, cảm giác ròn rụm, thơm lừng phảng phất hương tỏi tươi xua tan cảm giác ngán ngấy. Sự kết hợp này thơm lừng, đậm đà gia vị tạo nên một hương vị đặc trưng, rất thích hợp để nhâm nhi cùng bia rượu.\r\nTrong quá trình dùng món, nếu quý khách có bất cứ vấn đề gì có thể liên hệ trực tiếp bộ phận CSKH, Quản lý hoặc Giám sát để được hỗ trợ & xử lý nhanh nhất.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271652143285.webp'),
(21, 18, 'Thịt dải heo cháy tỏi', 'Khác biệt với cách chế biến thông thường, thịt được cắt thái dày bản, tẩm ướp đậm vị rồi áp chảo lửa vừa, giúp miếng thịt mềm mọng bên trong, hơi giòn rám bên ngoài mà không hề khô cứng. Vị ngọt tự nhiên từ thịt hòa quyện với mùi tỏi phi thơm lừng, điểm xuyến vị cay nhẹ của ớt tươi, càng ăn càng gây nghiện. Món này đặc biệt \"bắt bia\" – một miếng thịt mềm béo, một ngụm bia lạnh, đảm bảo cuộc nhậu thêm sôi động!', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271650570232.webp'),
(22, 18, 'Nộm da trâu trộn xoài non', 'Nộm da trâu xoài non, với hương vị chua chua, cay cay, dai dai của da trâu và vị thanh mát của xoài non, rất thích hợp để nhậu. Món này vừa có tác dụng kích thích vị giác, vừa có thể ăn kèm với các MÓN CHAY khác mà không gây ngán.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271649402242.webp'),
(23, 18, 'Lẩu gà đen nấm rừng Vân Nam', 'Cái \"linh hồn\" của nồi lẩu này nằm ở nước cốt lẩu ngọt thanh, thơm lừng mùi thảo mộc, được ninh từ đủ loại nấm rừng Vân Nam quý hiếm. Ăn kèm lẩu là nửa con gà đen săn chắc, đã được lọc xương sẵn, chỉ việc nhúng vào rồi chén thôi. Kèm theo là đa dạng các loại đồ thả lẩu tươi ngon khác nữa. Đặc biệt, còn có set nấm riêng với nhiều loại nấm quý như đông trùng hạ thảo, nấm ngọc châm nâu... đảm bảo vừa ngon vừa bổ. Với định lượng thế này, nồi lẩu hoàn hảo cho nhóm từ 3-4 người xì xụp no bụng, đủ để anh em bạn bè cùng tụ tập.', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271648487316.webp'),
(24, 18, 'Đậu pháp xào trứng non sốt XO', 'Món ăn là sự kết hợp hoàn hảo của đậu pháp giòn ngọt, trứng non béo ngậy và sốt XO cay cay, thơm nồng vị hải sản, chắc chắn sẽ gây thương nhớ cho nhiều thực khách.\r\n', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301017077267.webp'),
(25, 19, 'Ếch sốt tiêu gừng chua cay', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271712248578.webp'),
(26, 19, 'Trâu xào rau muống', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271706594276.webp'),
(27, 19, 'Lợn mán nướng giềng mẻ', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271704483259.webp'),
(28, 19, 'Nầm sữa nướng giềng mẻ', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271703552855.webp'),
(29, 19, 'Gà H’Mong rang muối', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404240940237304.webp'),
(30, 19, 'Gà H’Mong chiên mắm', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241051259542.webp'),
(31, 19, 'Chân gà sốt thái chua cay', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241055213531.webp'),
(32, 19, 'Lợn mán xào lăn', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405291714013536.webp'),
(33, 19, 'Lợn mán hấp lá thơm', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241100447173.webp'),
(34, 19, 'Lạp xưởng Tây Bắc', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/20240530101635664.webp'),
(35, 19, 'Khay đồ nguội Tây Bắc', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405290916414523.webp'),
(36, 19, 'Bò một nắng chấm muối kiến vàng', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301014156817.webp'),
(37, 19, 'Gà đen nướng mắc khén', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/20240530101506515.webp'),
(38, 19, 'Ếch chiên hoàng kim', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/2024042317045196.webp'),
(39, 19, 'Heo một nắng kèm sốt me', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404231700157219.webp'),
(40, 19, 'Ếch đồng chiên mắm tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/09/202309061413041526.webp'),
(41, 19, 'Bê chao tứ xuyên', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/09/202309061420490782.webp'),
(42, 19, 'Sườn Thái Cay', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/202305231603405066.webp'),
(43, 19, 'Cánh gà chiên mắm', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/06/202306031537588248.webp'),
(44, 19, 'Sụn gà chiên tiêu muối', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031033147671.webp'),
(45, 19, 'Sụn gà xào tứ xuyên', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/20230523153206957.webp'),
(46, 19, 'Giò heo giòn da', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/20230515161938907.webp'),
(47, 20, 'Dồi dê', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271710002288.webp'),
(48, 20, 'Ba chỉ dê hấp lá tía tô', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405141554352143.webp'),
(49, 20, 'Dê trộn dừa non kèm bánh đa', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301031589645.webp'),
(50, 20, 'Nộm dê bóp thấu', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301048323529.webp'),
(51, 20, 'Nộm dê rau má', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/06/202406051707191395.webp'),
(52, 39, 'Lợn mán nướng giềng mẻ', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271704483259.webp'),
(53, 39, 'Nầm sữa nướng giềng mẻ', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271703552855.webp'),
(54, 39, 'Lợn mán nướng mắc khén', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404231915283755.webp'),
(55, 39, 'Má heo nướng mắc khén', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241052362542.webp'),
(56, 39, 'Gà đen nướng mắc khén', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/20240530101506515.webp'),
(57, 39, 'Bê sữa nướng giềng mẻ', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/09/202309061341366132.webp'),
(58, 39, 'Thịt dải nướng mọi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031050025243.webp'),
(59, 39, 'Sườn nướng mật ong', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/06/202306061148549949.webp'),
(60, 39, 'Heo dăm nướng tảng', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/202305231711420557.webp'),
(61, 22, 'Trâu tươi cháy tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271652467121.webp'),
(62, 22, 'Nầm sữa cháy tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271652143285.webp'),
(63, 22, 'Thịt dải heo cháy tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271650570232.webp'),
(64, 22, 'Bê sữa sốt tiêu xanh', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/09/202309061433521985.webp'),
(65, 22, 'Tràng trứng chim', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/202305251551002232.webp'),
(66, 22, 'Nọng heo cháy tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031018161815.webp'),
(67, 23, 'Rau ngót lào xào tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241059241917.webp'),
(68, 23, 'Rau bò khai xào tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241044095476.webp'),
(69, 23, 'Ngồng cải luộc chấm trứng', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241046216428.webp'),
(70, 23, 'Măng trúc xào ba chỉ gác bếp', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241050057944.webp'),
(71, 23, 'Cải mèo xào tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241056124413.webp'),
(72, 23, 'Cải mèo luộc chấm trứng', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241057066956.webp'),
(73, 23, 'Rau ngót lào xào ba chỉ gác bếp', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301020572687.webp'),
(74, 23, 'Rau bò khai xào ba chỉ gác bếp', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301012158646.webp'),
(75, 23, 'Đậu pháp xào trứng non sốt XO', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301017077267.webp'),
(76, 23, 'Cải mèo xào ba chỉ gác bếp', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301018268257.webp'),
(77, 23, 'Măng trúc xào bò', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301030470862.webp'),
(78, 23, 'Ốc móng tay xào rau muống', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/09/202309061139596085.webp'),
(79, 23, 'Tóp mỡ xốt cà chua rau mầm', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/202305291127396485.webp'),
(80, 23, 'Củ quả luộc chấm kho quẹt', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/202305291111005372.webp'),
(81, 23, 'Dưa chua xào tóp', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/202305291115144371.webp'),
(82, 23, 'Khổ qua xào trứng', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/06/202306061118334244.webp'),
(83, 23, 'Ngọn su xào tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031044597232.webp'),
(84, 23, 'Cải thảo xào tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031021263371.webp'),
(85, 23, 'Muống xào tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/20230529111726023.webp'),
(86, 24, 'Lẩu gà đen nấm rừng Vân Nam', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271648487316.webp'),
(87, 24, 'Lẩu bò tươi Tự Do', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/12/202412141410582989.webp'),
(88, 24, 'Lẩu riêu cua bắp bò', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031022434265.webp'),
(89, 24, 'Lẩu hải sản kiểu thái', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031024450187.webp'),
(90, 24, 'Lẩu ếch măng cay', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031058566287.webp'),
(91, 25, 'Ốc hương ủ muối thảo mộc', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271713075933.webp'),
(92, 25, 'Tôm sú ủ muối thảo mộc', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271709128585.webp'),
(93, 25, 'Cá dưa chua tứ xuyên', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271658346999.webp'),
(94, 25, 'Tôm chiên hoàng kim', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/20240424094950598.webp'),
(95, 25, 'Tôm sú sốt ớt pattaya', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/06/202406051717238266.webp'),
(96, 25, 'Mực hấp sốt Thái', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241028245463.webp'),
(97, 25, 'Mực hấp chanh', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404241029239085.webp'),
(98, 25, 'Mực chiên hoàng kim', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/20240423164805303.webp'),
(99, 25, 'Râu mực xào su hào sốt XO', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/20240530104949342.webp'),
(100, 25, 'Tôm sú sốt me đặc biệt', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404231505291384.webp'),
(101, 25, 'Râu mực xào tứ xuyên', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/09/202309051543541554.webp'),
(102, 25, 'Mực cháy chà bông', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/10/202310272237082488.webp'),
(103, 25, 'Mực khô nướng', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/06/202306011639471545.webp'),
(104, 25, 'Mực chiên bơ tỏi', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/20230523175503329.webp'),
(105, 25, 'Tôm tắm sốt thái', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/20230525164944133.webp'),
(106, 25, 'Khay hải sản tự do', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405031015288594.webp'),
(107, 26, 'Cá dưa chua tứ xuyên', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2025/06/202506271658346999.webp'),
(108, 26, 'Cá chẽm hấp chanh', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/04/202404240931460066.webp'),
(109, 26, 'Cá chẽm sốt Pattaya', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301010522355.webp'),
(110, 26, 'Cá chẽm chiên sốt me đặc biệt', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/202405301010201502.webp'),
(111, 26, 'Cá chẽm chiên giòn kèm xoài xanh', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2024/05/20240530100945073.webp'),
(112, 26, 'Cá diêu hồng chiên sốt chili thái', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/09/20230906154245786.webp'),
(113, 26, 'Cá quả nướng muối ớt', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/05/202305291113500553.webp'),
(114, 26, 'Cá chép om dưa', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/06/202306061053486409.webp'),
(115, 26, 'Cá chép hấp xì dầu hongkong', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/06/202306061045043209.webp'),
(116, 26, 'Cá chép om cay tứ xuyên', '', 'https://storage.quannhautudo.com/data/thumb_400/Data/images/product/2023/06/202306061054587683.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `MaNV` int(11) NOT NULL,
  `MaCoSo` int(11) NOT NULL,
  `TenDN` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `TenNhanVien` varchar(100) NOT NULL,
  `ChucVu` enum('admin','manager','receptionist') NOT NULL DEFAULT 'receptionist'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`MaNV`, `MaCoSo`, `TenDN`, `MatKhau`, `TenNhanVien`, `ChucVu`) VALUES
(111, 11, 'admin', '$2y$10$4xxiEIR3.vy.zlPcpJEHweZgGTlBeA.gV/O4vKKNNTwmYrhsga0Ni', 'Admin', 'admin'),
(112, 11, 'dung', '$2y$10$5PMRPUj6PeNBYR5S0GMjWumcIX/2aVXwaXvWIBsokgKGYuwwZH576', 'Dung', 'manager'),
(113, 11, 'vutin', '$2y$10$CSdZs9k5DZwvN6nYuu3t2uu3J0rPbmEET1S3eC7JQUJzVXUI4KEQe', 'Tin Vu', 'receptionist');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `uudai`
--

CREATE TABLE `uudai` (
  `MaUD` int(11) NOT NULL,
  `TenMaUD` varchar(50) DEFAULT NULL,
  `MoTa` text NOT NULL,
  `GiaTriGiam` decimal(10,2) NOT NULL,
  `LoaiGiamGia` enum('phantram','sotien') NOT NULL,
  `DieuKien` text DEFAULT NULL,
  `NgayBD` date NOT NULL,
  `NgayKT` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `uudai`
--

INSERT INTO `uudai` (`MaUD`, `TenMaUD`, `MoTa`, `GiaTriGiam`, `LoaiGiamGia`, `DieuKien`, `NgayBD`, `NgayKT`) VALUES
(10, 'GIAM10', 'Giảm 10% cho đơn hàng', 10.00, 'phantram', 'Áp dụng cho tất cả đơn hàng', '2024-01-01', '2025-12-31'),
(11, 'GIAM20', 'Giảm 20% cho khách hàng mới', 20.00, 'phantram', 'Dành cho khách hàng đặt lần đầu', '2024-01-01', '2025-12-31'),
(12, 'GIAM30', 'Giảm 30% khuyến mãi đặc biệt', 30.00, 'phantram', 'Áp dụng cho đơn hàng trên 500.000đ', '2024-01-01', '2025-12-31'),
(13, 'GIAM50K', 'Giảm 50.000đ cho đơn hàng', 50000.00, 'sotien', 'Áp dụng cho đơn hàng trên 300.000đ', '2024-01-01', '2025-12-31'),
(14, 'GIAM100K', 'Giảm 100.000đ cho đơn hàng lớn', 100000.00, 'sotien', 'Áp dụng cho đơn hàng trên 500.000đ', '2024-01-01', '2025-12-31'),
(15, 'TET2025', 'Giảm 25% dịp Tết Ất Tỵ', 25.00, 'phantram', 'Chương trình Tết 2025', '2025-01-20', '2025-02-10'),
(16, 'QUOCKHANH', 'Giảm 15% Quốc Khánh', 15.00, 'phantram', 'Kỷ niệm Quốc Khánh', '2025-08-28', '2025-09-05');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ban`
--
ALTER TABLE `ban`
  ADD PRIMARY KEY (`MaBan`),
  ADD KEY `MaCoSo` (`MaCoSo`);

--
-- Chỉ mục cho bảng `chitietdondatban`
--
ALTER TABLE `chitietdondatban`
  ADD PRIMARY KEY (`MaDon`,`MaMon`),
  ADD KEY `MaMon` (`MaMon`);

--
-- Chỉ mục cho bảng `coso`
--
ALTER TABLE `coso`
  ADD PRIMARY KEY (`MaCoSo`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`MaDM`),
  ADD UNIQUE KEY `TenDM` (`TenDM`);

--
-- Chỉ mục cho bảng `dondatban`
--
ALTER TABLE `dondatban`
  ADD PRIMARY KEY (`MaDon`),
  ADD KEY `MaKH` (`MaKH`),
  ADD KEY `MaCoSo` (`MaCoSo`),
  ADD KEY `MaUD` (`MaUD`),
  ADD KEY `MaNV_XacNhan` (`MaNV_XacNhan`);

--
-- Chỉ mục cho bảng `dondatban_ban`
--
ALTER TABLE `dondatban_ban`
  ADD PRIMARY KEY (`MaDon`,`MaBan`),
  ADD KEY `MaBan` (`MaBan`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`),
  ADD UNIQUE KEY `SDT` (`SDT`);

--
-- Chỉ mục cho bảng `menu_coso`
--
ALTER TABLE `menu_coso`
  ADD PRIMARY KEY (`MaCoSo`,`MaMon`),
  ADD KEY `MaMon` (`MaMon`);

--
-- Chỉ mục cho bảng `monan`
--
ALTER TABLE `monan`
  ADD PRIMARY KEY (`MaMon`),
  ADD KEY `MaDM` (`MaDM`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`MaNV`),
  ADD UNIQUE KEY `TenDN` (`TenDN`),
  ADD KEY `MaCoSo` (`MaCoSo`);

--
-- Chỉ mục cho bảng `uudai`
--
ALTER TABLE `uudai`
  ADD PRIMARY KEY (`MaUD`),
  ADD UNIQUE KEY `TenMaUD` (`TenMaUD`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `ban`
--
ALTER TABLE `ban`
  MODIFY `MaBan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `coso`
--
ALTER TABLE `coso`
  MODIFY `MaCoSo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `MaDM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT cho bảng `dondatban`
--
ALTER TABLE `dondatban`
  MODIFY `MaDon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `MaKH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT cho bảng `monan`
--
ALTER TABLE `monan`
  MODIFY `MaMon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `MaNV` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT cho bảng `uudai`
--
ALTER TABLE `uudai`
  MODIFY `MaUD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `ban`
--
ALTER TABLE `ban`
  ADD CONSTRAINT `ban_ibfk_1` FOREIGN KEY (`MaCoSo`) REFERENCES `coso` (`MaCoSo`);

--
-- Các ràng buộc cho bảng `chitietdondatban`
--
ALTER TABLE `chitietdondatban`
  ADD CONSTRAINT `chitietdondatban_ibfk_1` FOREIGN KEY (`MaDon`) REFERENCES `dondatban` (`MaDon`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietdondatban_ibfk_2` FOREIGN KEY (`MaMon`) REFERENCES `monan` (`MaMon`);

--
-- Các ràng buộc cho bảng `dondatban`
--
ALTER TABLE `dondatban`
  ADD CONSTRAINT `dondatban_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`),
  ADD CONSTRAINT `dondatban_ibfk_2` FOREIGN KEY (`MaCoSo`) REFERENCES `coso` (`MaCoSo`),
  ADD CONSTRAINT `dondatban_ibfk_3` FOREIGN KEY (`MaUD`) REFERENCES `uudai` (`MaUD`),
  ADD CONSTRAINT `dondatban_ibfk_4` FOREIGN KEY (`MaNV_XacNhan`) REFERENCES `nhanvien` (`MaNV`);

--
-- Các ràng buộc cho bảng `dondatban_ban`
--
ALTER TABLE `dondatban_ban`
  ADD CONSTRAINT `dondatban_ban_ibfk_1` FOREIGN KEY (`MaDon`) REFERENCES `dondatban` (`MaDon`) ON DELETE CASCADE,
  ADD CONSTRAINT `dondatban_ban_ibfk_2` FOREIGN KEY (`MaBan`) REFERENCES `ban` (`MaBan`);

--
-- Các ràng buộc cho bảng `menu_coso`
--
ALTER TABLE `menu_coso`
  ADD CONSTRAINT `menu_coso_ibfk_1` FOREIGN KEY (`MaCoSo`) REFERENCES `coso` (`MaCoSo`),
  ADD CONSTRAINT `menu_coso_ibfk_2` FOREIGN KEY (`MaMon`) REFERENCES `monan` (`MaMon`);

--
-- Các ràng buộc cho bảng `monan`
--
ALTER TABLE `monan`
  ADD CONSTRAINT `monan_ibfk_1` FOREIGN KEY (`MaDM`) REFERENCES `danhmuc` (`MaDM`);

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`MaCoSo`) REFERENCES `coso` (`MaCoSo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
