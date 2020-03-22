/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100410
 Source Host           : localhost:3306
 Source Schema         : new-elearning

 Target Server Type    : MySQL
 Target Server Version : 100410
 File Encoding         : 65001

 Date: 23/03/2020 00:19:07
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for field_tambahan
-- ----------------------------
DROP TABLE IF EXISTS `field_tambahan`;
CREATE TABLE `field_tambahan`  (
  `id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of field_tambahan
-- ----------------------------
INSERT INTO `field_tambahan` VALUES ('check-urgent-info', 'Check Urgent Info', '{\"info\":\"\",\"last_check\":\"2020-03-22 20:10:13\"}');

-- ----------------------------
-- Table structure for kelas
-- ----------------------------
DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `parent_id` int(11) NULL DEFAULT NULL,
  `urutan` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=aktif 0=tidak',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE,
  INDEX `parent_id_2`(`parent_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 16 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kelas
-- ----------------------------
INSERT INTO `kelas` VALUES (1, 'KELAS X', NULL, 1, 1);
INSERT INTO `kelas` VALUES (2, 'KELAS X - A', 1, 2, 1);
INSERT INTO `kelas` VALUES (3, 'KELAS X - B', 1, 3, 1);
INSERT INTO `kelas` VALUES (4, 'KELAS X - C', 1, 4, 1);
INSERT INTO `kelas` VALUES (5, 'KELAS X - D', 1, 5, 1);
INSERT INTO `kelas` VALUES (6, 'KELAS XI', NULL, 6, 1);
INSERT INTO `kelas` VALUES (7, 'KELAS XI - A', 6, 7, 1);
INSERT INTO `kelas` VALUES (8, 'KELAS XI - B', 6, 8, 1);
INSERT INTO `kelas` VALUES (9, 'KELAS XI - C', 6, 9, 1);
INSERT INTO `kelas` VALUES (10, 'KELAS XI - D', 6, 10, 1);
INSERT INTO `kelas` VALUES (11, 'KELAS XII', NULL, 11, 1);
INSERT INTO `kelas` VALUES (12, 'KELAS XII - A', 11, 12, 1);
INSERT INTO `kelas` VALUES (13, 'KELAS XII - B', 11, 13, 1);
INSERT INTO `kelas` VALUES (14, 'KELAS XII - C', 11, 14, 1);
INSERT INTO `kelas` VALUES (15, 'KELAS XII - D', 11, 15, 1);

-- ----------------------------
-- Table structure for kelas_siswa
-- ----------------------------
DROP TABLE IF EXISTS `kelas_siswa`;
CREATE TABLE `kelas_siswa`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL COMMENT '0 jika bukan, 1 jika ya',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `kelas_id`(`kelas_id`, `siswa_id`) USING BTREE,
  INDEX `kelas_id_2`(`kelas_id`, `siswa_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of kelas_siswa
-- ----------------------------
INSERT INTO `kelas_siswa` VALUES (1, 12, 1, 1);

-- ----------------------------
-- Table structure for komentar
-- ----------------------------
DROP TABLE IF EXISTS `komentar`;
CREATE TABLE `komentar`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_id` int(11) NOT NULL,
  `materi_id` int(11) NOT NULL,
  `tampil` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=tidak,1=tampil',
  `konten` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `tgl_posting` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `login_id`(`login_id`, `materi_id`) USING BTREE,
  INDEX `login_id_2`(`login_id`, `materi_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for login
-- ----------------------------
DROP TABLE IF EXISTS `login`;
CREATE TABLE `login`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `siswa_id` int(11) NULL DEFAULT NULL,
  `pengajar_id` int(11) NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL COMMENT '0=tidak,1=ya',
  `reset_kode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`, `siswa_id`, `pengajar_id`) USING BTREE,
  INDEX `username_2`(`username`, `siswa_id`, `pengajar_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of login
-- ----------------------------
INSERT INTO `login` VALUES (1, 'admin@admin.com', '0192023a7bbd73250516f069df18b500', NULL, 1, 1, NULL);
INSERT INTO `login` VALUES (2, 'guru@email.com', '0192023a7bbd73250516f069df18b500', NULL, 2, 0, NULL);
INSERT INTO `login` VALUES (3, 'murid@email.com', '0192023a7bbd73250516f069df18b500', 1, NULL, 0, NULL);

-- ----------------------------
-- Table structure for login_log
-- ----------------------------
DROP TABLE IF EXISTS `login_log`;
CREATE TABLE `login_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_id` int(11) NOT NULL,
  `lasttime` datetime(0) NOT NULL,
  `agent` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_activity` int(10) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `login_id`(`login_id`) USING BTREE,
  INDEX `login_id_2`(`login_id`) USING BTREE,
  INDEX `login_id_3`(`login_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of login_log
-- ----------------------------
INSERT INTO `login_log` VALUES (1, 1, '2020-03-22 20:10:11', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584882613);
INSERT INTO `login_log` VALUES (2, 1, '2020-03-22 20:10:36', '{\"is_mobile\":1,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Linux\",\"agent_string\":\"Mozilla\\/5.0 (Linux; Android 5.0; SM-G900P Build\\/LRX21T) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Mobile Safari\\/537.36\",\"ip\":\"::1\"}', 1584882543);
INSERT INTO `login_log` VALUES (3, 1, '2020-03-22 20:52:32', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584885294);
INSERT INTO `login_log` VALUES (4, 2, '2020-03-22 22:22:40', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584890565);
INSERT INTO `login_log` VALUES (5, 2, '2020-03-22 22:26:29', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584890673);
INSERT INTO `login_log` VALUES (6, 3, '2020-03-22 22:26:37', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584891599);
INSERT INTO `login_log` VALUES (7, 3, '2020-03-22 22:42:07', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584892639);
INSERT INTO `login_log` VALUES (8, 2, '2020-03-22 22:59:24', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584897526);
INSERT INTO `login_log` VALUES (9, 2, '2020-03-22 23:10:56', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584893339);
INSERT INTO `login_log` VALUES (10, 3, '2020-03-22 23:11:02', '{\"is_mobile\":0,\"browser\":\"Chrome 80.0.3987.149\",\"platform\":\"Unknown Windows OS\",\"agent_string\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/80.0.3987.149 Safari\\/537.36\",\"ip\":\"::1\"}', 1584893465);

-- ----------------------------
-- Table structure for mapel
-- ----------------------------
DROP TABLE IF EXISTS `mapel`;
CREATE TABLE `mapel`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = ya, 0 = tidak',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of mapel
-- ----------------------------
INSERT INTO `mapel` VALUES (1, 'Bahasa Indonesia', NULL, 1);
INSERT INTO `mapel` VALUES (2, 'Bahasa Inggris', NULL, 1);
INSERT INTO `mapel` VALUES (3, 'Matematika', NULL, 1);
INSERT INTO `mapel` VALUES (4, 'Ekonomi', NULL, 1);
INSERT INTO `mapel` VALUES (5, 'Geografi', NULL, 1);
INSERT INTO `mapel` VALUES (6, 'Biologi', NULL, 1);
INSERT INTO `mapel` VALUES (7, 'Penjas', NULL, 1);
INSERT INTO `mapel` VALUES (8, 'Agama', NULL, 1);
INSERT INTO `mapel` VALUES (9, 'Fisika', NULL, 1);
INSERT INTO `mapel` VALUES (10, 'Kimia', NULL, 1);

-- ----------------------------
-- Table structure for mapel_ajar
-- ----------------------------
DROP TABLE IF EXISTS `mapel_ajar`;
CREATE TABLE `mapel_ajar`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hari_id` tinyint(1) NOT NULL COMMENT '1=senin,2=selasa,3=rabu,4=kamis,5=jum\'at,6=sabtu,7=minggu',
  `jam_mulai` time(0) NOT NULL,
  `jam_selesai` time(0) NOT NULL,
  `pengajar_id` int(11) NOT NULL,
  `mapel_kelas_id` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = aktif 0 = tidak',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `hari_id`(`hari_id`, `pengajar_id`, `mapel_kelas_id`) USING BTREE,
  INDEX `hari_id_2`(`hari_id`, `pengajar_id`, `mapel_kelas_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for mapel_kelas
-- ----------------------------
DROP TABLE IF EXISTS `mapel_kelas`;
CREATE TABLE `mapel_kelas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` int(11) NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `kelas_id`(`kelas_id`, `mapel_id`) USING BTREE,
  INDEX `kelas_id_2`(`kelas_id`, `mapel_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for materi
-- ----------------------------
DROP TABLE IF EXISTS `materi`;
CREATE TABLE `materi`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mapel_id` int(11) NOT NULL,
  `pengajar_id` int(11) NULL DEFAULT NULL,
  `siswa_id` int(11) NULL DEFAULT NULL,
  `judul` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `konten` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `file` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `tgl_posting` datetime(0) NOT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mapel_id`(`mapel_id`, `pengajar_id`, `siswa_id`) USING BTREE,
  INDEX `mapel_id_2`(`mapel_id`, `pengajar_id`, `siswa_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for materi_kelas
-- ----------------------------
DROP TABLE IF EXISTS `materi_kelas`;
CREATE TABLE `materi_kelas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `materi_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `materi_id`(`materi_id`, `kelas_id`) USING BTREE,
  INDEX `materi_id_2`(`materi_id`, `kelas_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` tinyint(1) NOT NULL COMMENT '1=inbox,2=outbox',
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `owner_id` int(11) NOT NULL,
  `sender_receiver_id` int(11) NOT NULL,
  `date` datetime(0) NOT NULL,
  `opened` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=belum,1=sudah',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `type_id`(`type_id`, `owner_id`, `sender_receiver_id`) USING BTREE,
  INDEX `type_id_2`(`type_id`, `owner_id`, `sender_receiver_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for nilai_tugas
-- ----------------------------
DROP TABLE IF EXISTS `nilai_tugas`;
CREATE TABLE `nilai_tugas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nilai` float NOT NULL,
  `tugas_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `tugas_id`(`tugas_id`, `siswa_id`) USING BTREE,
  INDEX `tugas_id_2`(`tugas_id`, `siswa_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for pengajar
-- ----------------------------
DROP TABLE IF EXISTS `pengajar`;
CREATE TABLE `pengajar`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `jenis_kelamin` varchar(9) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tempat_lahir` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tgl_lahir` date NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `foto` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status_id` tinyint(1) NOT NULL COMMENT '0=pending, 1=aktif, 2=blok',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `nip`(`nip`) USING BTREE,
  INDEX `nip_2`(`nip`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengajar
-- ----------------------------
INSERT INTO `pengajar` VALUES (1, '123123123123', 'Administrator', 'Laki-laki', 'Bandung', '1980-01-01', 'Bandung', NULL, 1);
INSERT INTO `pengajar` VALUES (2, '123123123', 'Guru 1', 'Laki-laki', 'Bandung', '1331-01-01', 'Bandung', NULL, 1);

-- ----------------------------
-- Table structure for pengaturan
-- ----------------------------
DROP TABLE IF EXISTS `pengaturan`;
CREATE TABLE `pengaturan`  (
  `id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengaturan
-- ----------------------------
INSERT INTO `pengaturan` VALUES ('email-server', 'Email server', 'no-reply@domain.com');
INSERT INTO `pengaturan` VALUES ('email-template-approve-pengajar', 'Approve pengajar (email pengajar)', '{\"subject\":\"Pengaktifan Akun\",\"body\":\"<p>Hai {$nama},<\\/p>\\n<p>Anda telah diterima sebagai pengajar pada {$nama_sekolah}, berikut informasi data diri anda:<\\/p>\\n<p>{$tabel_profil}<\\/p>\\n<p>Anda dapat login ke sistem E-Learning menggunakan username dan password yang telah anda buat saat pendaftaran, login pada url berikut : {$url_login}<\\/p>\"}');
INSERT INTO `pengaturan` VALUES ('email-template-approve-siswa', 'Approve siswa (email siswa)', '{\"subject\":\"Pengaktifan Akun\",\"body\":\"<p>Hai {$nama},<\\/p>\\n<p>Anda telah diterima sebagai siswa pada {$nama_sekolah}, berikut informasi data diri anda:<\\/p>\\n<p>{$tabel_profil}<\\/p>\\n<p>Anda dapat login ke sistem E-Learning menggunakan username dan password yang telah anda buat saat pendaftaran, login pada url berikut : {$url_login}<\\/p>\"}');
INSERT INTO `pengaturan` VALUES ('email-template-link-reset', 'Link Reset Password', '{\"subject\":\"Reset Password\",\"body\":\"<p>Hai,<\\/p>\\n<p>Anda mengirimkan permintaan untuk reset password anda, klik link berikut untuk memulai reset password : {$link_reset}<\\/p>\"}');
INSERT INTO `pengaturan` VALUES ('email-template-register-pengajar', 'Register pengajar (email pengajar)', '{\"subject\":\"Registrasi Berhasil\",\"body\":\"<p>Hai {$nama},<\\/p>\\n<p>Terimakasih telah melakukan pendaftaran sebagai pengajar di E-Learning {$nama_sekolah}. Akun anda akan segera diaktifkan oleh admin.<\\/p>\"}');
INSERT INTO `pengaturan` VALUES ('email-template-register-siswa', 'Register siswa (email siswa)', '{\"subject\":\"Registrasi Berhasil\",\"body\":\"<p>Hai {$nama},<\\/p>\\n<p>Terimakasih telah melakukan pendaftaran sebagai siswa di E-Learning {$nama_sekolah}. Akun anda akan segera diaktifkan oleh admin.<\\/p>\"}');
INSERT INTO `pengaturan` VALUES ('info-registrasi', 'Info Registrasi', '<p>Silakan mendaftar sebagai siswa atau pengajar (jika anda sebagai pengajar) dengan memilih sesuai tab berikut :</p>');
INSERT INTO `pengaturan` VALUES ('peraturan-elearning', 'Peraturan E-learning', '');
INSERT INTO `pengaturan` VALUES ('registrasi-pengajar', 'Registrasi Pengajar', '1');
INSERT INTO `pengaturan` VALUES ('registrasi-siswa', 'Registrasi Siswa', '1');
INSERT INTO `pengaturan` VALUES ('versi', 'Versi', '2.0');
INSERT INTO `pengaturan` VALUES ('jenjang', 'jenjang', 'SMA');
INSERT INTO `pengaturan` VALUES ('nama-sekolah', 'nama-sekolah', 'SMAN 1 Percobaan');
INSERT INTO `pengaturan` VALUES ('alamat', 'alamat', 'Bandung');
INSERT INTO `pengaturan` VALUES ('telp', 'telp', '123123123123');
INSERT INTO `pengaturan` VALUES ('install-success', 'install-success', '1');

-- ----------------------------
-- Table structure for pengumuman
-- ----------------------------
DROP TABLE IF EXISTS `pengumuman`;
CREATE TABLE `pengumuman`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `konten` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tgl_tampil` date NOT NULL,
  `tgl_tutup` date NOT NULL,
  `tampil_siswa` tinyint(1) NOT NULL DEFAULT 1,
  `tampil_pengajar` tinyint(1) NOT NULL DEFAULT 1,
  `pengajar_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pengajar_id`(`pengajar_id`) USING BTREE,
  INDEX `pengajar_id_2`(`pengajar_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pilihan
-- ----------------------------
DROP TABLE IF EXISTS `pilihan`;
CREATE TABLE `pilihan`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertanyaan_id` int(11) NOT NULL,
  `konten` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `kunci` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=tidak',
  `urutan` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pertanyaan_id`(`pertanyaan_id`) USING BTREE,
  INDEX `pertanyaan_id_2`(`pertanyaan_id`, `kunci`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pilihan
-- ----------------------------
INSERT INTO `pilihan` VALUES (1, 1, 'asdasd', 0, 1, 1);
INSERT INTO `pilihan` VALUES (2, 1, 'dsadsa', 0, 2, 1);
INSERT INTO `pilihan` VALUES (3, 1, 'dsadas', 1, 3, 1);

-- ----------------------------
-- Table structure for siswa
-- ----------------------------
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `nama` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `jenis_kelamin` varchar(9) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Laki-laki dan Perempuan',
  `tempat_lahir` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tgl_lahir` date NULL DEFAULT NULL,
  `agama` char(7) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `alamat` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tahun_masuk` year NOT NULL,
  `foto` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status_id` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending, 1=aktif, 2=blok, 3=alumni, 4=deleted',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `nis`(`nis`, `nama`, `status_id`) USING BTREE,
  INDEX `nis_2`(`nis`, `nama`, `status_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of siswa
-- ----------------------------
INSERT INTO `siswa` VALUES (1, '1233212123', 'Rizki Nugraha', 'Laki-laki', 'Bandung', '1331-01-01', 'ISLAM', 'Bandung', 2015, NULL, 1);
INSERT INTO `siswa` VALUES (2, NULL, '', '', '', NULL, NULL, '', 0000, NULL, 0);

-- ----------------------------
-- Table structure for tugas
-- ----------------------------
DROP TABLE IF EXISTS `tugas`;
CREATE TABLE `tugas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mapel_id` int(11) NOT NULL,
  `pengajar_id` int(11) NOT NULL,
  `type_id` tinyint(1) NOT NULL COMMENT '1=upload,2=essay,3=ganda',
  `judul` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `durasi` int(11) NULL DEFAULT NULL COMMENT 'lama pengerjaan dalam menit',
  `info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 0,
  `tgl_buat` datetime(0) NULL DEFAULT NULL,
  `tampil_siswa` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=tidak tampil di siswa, 1=tampil siswa',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `mapel_id`(`mapel_id`, `pengajar_id`, `type_id`) USING BTREE,
  INDEX `mapel_id_2`(`mapel_id`, `pengajar_id`, `type_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tugas
-- ----------------------------
INSERT INTO `tugas` VALUES (1, 1, 2, 3, 'UAS', 60, 'Ujian', 0, '2020-03-22 22:59:48', 0);
INSERT INTO `tugas` VALUES (2, 6, 2, 2, 'UTS', 60, 'asd', 0, '2020-03-22 23:09:55', 0);

-- ----------------------------
-- Table structure for tugas_kelas
-- ----------------------------
DROP TABLE IF EXISTS `tugas_kelas`;
CREATE TABLE `tugas_kelas`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tugas_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `tugas_id`(`tugas_id`, `kelas_id`) USING BTREE,
  INDEX `tugas_id_2`(`tugas_id`, `kelas_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Records of tugas_kelas
-- ----------------------------
INSERT INTO `tugas_kelas` VALUES (1, 1, 12);
INSERT INTO `tugas_kelas` VALUES (2, 2, 13);

-- ----------------------------
-- Table structure for tugas_pertanyaan
-- ----------------------------
DROP TABLE IF EXISTS `tugas_pertanyaan`;
CREATE TABLE `tugas_pertanyaan`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertanyaan` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `urutan` int(11) NOT NULL,
  `tugas_id` int(11) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `tugas_id`(`tugas_id`) USING BTREE,
  INDEX `tugas_id_2`(`tugas_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tugas_pertanyaan
-- ----------------------------
INSERT INTO `tugas_pertanyaan` VALUES (1, 'Coba coba', 1, 1, 1);

SET FOREIGN_KEY_CHECKS = 1;