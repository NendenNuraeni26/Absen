/*
 Navicat Premium Data Transfer

 Source Server         : LOCAL
 Source Server Type    : MySQL
 Source Server Version : 100432
 Source Host           : localhost:3306
 Source Schema         : absen

 Target Server Type    : MySQL
 Target Server Version : 100432
 File Encoding         : 65001

 Date: 17/06/2026 11:38:11
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for karyawan
-- ----------------------------
DROP TABLE IF EXISTS `karyawan`;
CREATE TABLE `karyawan`  (
  `id_karyawan` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_croatian_ci NOT NULL,
  `nama_karyawan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_croatian_ci NOT NULL,
  `status` enum('Aktif','Non Aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_croatian_ci NOT NULL DEFAULT 'Aktif',
  `id_unit` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_karyawan`) USING BTREE,
  UNIQUE INDEX `nomor_karyawan`(`nomor_karyawan`) USING BTREE,
  INDEX `fk_unit`(`id_unit`) USING BTREE,
  CONSTRAINT `fk_unit` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id_unit`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 217 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_croatian_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
