-- phpMyAdmin SQL Dump
-- Database: `hoahuongduongphone`

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Cấu trúc bảng `binhluan` (Đã bổ sung cột solansua)
-- --------------------------------------------------------

CREATE TABLE `binhluan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_taikhoan` int(11) NOT NULL,
  `id_sanpham` int(11) NOT NULL,
  `noidung` varchar(1000) NOT NULL,
  `ngaydang` date NOT NULL,
  `sosao` int(11) NOT NULL,
  `trangthai` varchar(100) NOT NULL DEFAULT '1',
  `solansua` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu mẫu bảng `binhluan`
INSERT INTO `binhluan` (`id`, `id_taikhoan`, `id_sanpham`, `noidung`, `ngaydang`, `sosao`, `trangthai`, `solansua`) VALUES
(7, 1, 1, 'Tốt', '2026-06-24', 5, '1', 0),
(10, 1, 1, 'Hay quá', '2026-06-25', 5, '1', 0),
(13, 1, 1, 'Hay quá', '2026-06-25', 5, '1', 0);

-- --------------------------------------------------------
-- Cấu trúc bảng `chitietdonhang`
-- --------------------------------------------------------

CREATE TABLE `chitietdonhang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_donhang` int(11) NOT NULL,
  `id_sanpham` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `dongia` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `chitietdonhang` (`id`, `id_donhang`, `id_sanpham`, `soluong`, `dongia`) VALUES
(1, 2, 1, 1, 1500000),
(2, 5, 1, 1, 1500000),
(3, 6, 1, 1, 1500000),
(4, 7, 1, 1, 1500000),
(5, 8, 1, 1, 1500000),
(6, 9, 1, 4, 1500000),
(7, 9, 5, 1, 11990000),
(8, 10, 8, 1, 2199000),
(9, 11, 5, 1, 11990000);

-- --------------------------------------------------------
-- Cấu trúc bảng `donhang`
-- --------------------------------------------------------

CREATE TABLE `donhang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_taikhoan` int(100) NOT NULL,
  `ngaymua` date NOT NULL,
  `tongtien` int(11) NOT NULL,
  `trangthai` varchar(100) NOT NULL,
  `diachigiaohang` varchar(1000) NOT NULL,
  `tennguoinhan` varchar(100) NOT NULL,
  `sdtnhan` int(11) NOT NULL,
  `thanhtoan` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `donhang` (`id`, `id_taikhoan`, `ngaymua`, `tongtien`, `trangthai`, `diachigiaohang`, `tennguoinhan`, `sdtnhan`, `thanhtoan`) VALUES
(2, 1, '2026-06-25', 1500000, 'Đang giao', '', '', 0, ''),
(3, 1, '2026-06-25', 1500000, 'Chờ xác nhận', '', '', 0, ''),
(4, 1, '2026-06-25', 1500000, 'Chờ xác nhận', '', '', 0, ''),
(5, 1, '2026-06-25', 1500000, 'Chờ xác nhận', '', '', 0, ''),
(6, 1, '2026-06-25', 1500000, 'Hoàn thành', 'Cần Thơ', 'Văn A', 123456789, 'COD (Thanh toán khi nhận hàng)'),
(7, 1, '2026-06-25', 1500000, 'Đang giao', 'Cần Thơ', 'Văn A', 123456789, 'COD (Thanh toán khi nhận hàng)'),
(8, 1, '2026-06-25', 1500000, 'Chờ xác nhận', 'Cần Thơ', 'Văn A', 123456789, 'COD (Thanh toán khi nhận hàng)'),
(9, 1, '2026-07-20', 17990000, 'Hoàn thành', 'Cần Thơ', 'Văn A', 123456789, 'COD (Thanh toán khi nhận hàng)'),
(10, 1, '2026-07-20', 2199000, 'Hoàn thành', 'Cần Thơ', 'Văn A', 123456789, 'COD (Thanh toán khi nhận hàng)'),
(11, 1, '2026-07-20', 11990000, 'Hoàn thành', 'Cần Thơ', 'Văn A', 123456789, 'COD (Thanh toán khi nhận hàng)');

-- --------------------------------------------------------
-- Cấu trúc bảng `giohang`
-- --------------------------------------------------------

CREATE TABLE `giohang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_taikhoan` int(11) NOT NULL,
  `id_sanpham` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `giohang` (`id`, `id_taikhoan`, `id_sanpham`, `soluong`) VALUES
(5, 2, 1, 1),
(6, 2, 5, 1);

-- --------------------------------------------------------
-- Cấu trúc bảng `lienhe`
-- --------------------------------------------------------

CREATE TABLE `lienhe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `noidung` varchar(1000) NOT NULL,
  `ngaygui` date NOT NULL,
  `trangthai` varchar(100) NOT NULL DEFAULT 'Chưa duyệt',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Cấu trúc bảng `sanpham`
-- --------------------------------------------------------

CREATE TABLE `sanpham` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten` varchar(100) NOT NULL,
  `hang` varchar(100) NOT NULL,
  `gia` int(11) NOT NULL,
  `mota` varchar(100) NOT NULL,
  `manhinh` varchar(1000) NOT NULL,
  `ram` varchar(50) NOT NULL,
  `bonho` varchar(100) NOT NULL,
  `pin` varchar(100) NOT NULL,
  `cpu` varchar(100) NOT NULL,
  `soluong` int(11) NOT NULL,
  `khuyenmai` float NOT NULL,
  `trangthai` varchar(100) NOT NULL,
  `hinhanh` varchar(255) NOT NULL,
  `hienthi` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Cấu trúc bảng `taikhoan`
-- --------------------------------------------------------

CREATE TABLE `taikhoan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ten` varchar(100) NOT NULL,
  `matkhau` varchar(100) NOT NULL,
  `sdt` varchar(200) NOT NULL,
  `diachi` varchar(1000) NOT NULL,
  `email` varchar(1000) NOT NULL,
  `trangthai` varchar(100) NOT NULL,
  `ngaytao` date NOT NULL,
  `vaitro` varchar(50) NOT NULL DEFAULT 'khachhang',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Khoá ngoại (Foreign Keys)
-- --------------------------------------------------------

ALTER TABLE `binhluan`
  ADD CONSTRAINT `binhluan_ibfk_1` FOREIGN KEY (`id_sanpham`) REFERENCES `sanpham` (`id`),
  ADD CONSTRAINT `binhluan_ibfk_2` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`);

ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`id_donhang`) REFERENCES `donhang` (`id`),
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`id_sanpham`) REFERENCES `sanpham` (`id`);

ALTER TABLE `donhang`
  ADD CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`);

ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`id_sanpham`) REFERENCES `sanpham` (`id`),
  ADD CONSTRAINT `giohang_ibfk_2` FOREIGN KEY (`id_taikhoan`) REFERENCES `taikhoan` (`id`);

COMMIT;