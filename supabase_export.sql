-- Ngekos.id PostgreSQL Export for Local Migration
-- Alignment: 100% d.sql Truth
SET session_replication_role = 'replica';

-- Table: plans
DELETE FROM "plans" CASCADE;
INSERT INTO "plans" ("id", "created_at", "nama_plans") VALUES (1, '2026-02-26 14:47:25+00', 'Anak Kos');
INSERT INTO "plans" ("id", "created_at", "nama_plans") VALUES (2, '2026-02-26 14:47:25+00', 'Premium');
INSERT INTO "plans" ("id", "created_at", "nama_plans") VALUES (3, '2026-02-26 14:47:25+00', 'Pro');
INSERT INTO "plans" ("id", "created_at", "nama_plans") VALUES (4, '2026-02-26 14:47:25+00', 'Per Kamar Premium');
INSERT INTO "plans" ("id", "created_at", "nama_plans") VALUES (5, '2026-02-26 14:47:25+00', 'Per Kamar Pro');
INSERT INTO "plans" ("id", "created_at", "nama_plans") VALUES (6, '2026-02-26 14:47:25+00', 'Super Admin');

-- Table: users
DELETE FROM "users" CASCADE;
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "id_plans", "nik", "nomor_wa", "tanggal_lahir", "alamat", "status") VALUES (1, 'Budi Santoso', 'anakkos@mail.com', NULL, '$2y$10$a7f1IOyJIatZ4cyZwg33m.sXtIIdUxvNAMYL74XAbX1B0ntElUgX6', NULL, '2026-02-26 14:50:48', '2026-02-28 15:31:44', 1, '3201010101010001', '81234567890', '2000-01-01', 'Jl. Kebon Jeruk No 1, Jakarta', 'pending');
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "id_plans", "nik", "nomor_wa", "tanggal_lahir", "alamat", "status") VALUES (5, 'Admin Pro', 'pro@mail.com', NULL, '$2y$10$J.ysQIXSvX/1X7eCRBX9COSKKJljJ.aBfxAFQeqvakcIFiVxe0QdK', NULL, '2026-02-27 05:47:55', '2026-02-28 15:31:44', 2, '8011940750', '8995146065', '1990-01-01', 'Alamat Dummy', 'pending');
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "id_plans", "nik", "nomor_wa", "tanggal_lahir", "alamat", "status") VALUES (7, 'Admin Per Kamar Pro', 'perkamar_pro@mail.com', NULL, '$2y$10$XEZajEsJNwNPvho/qCcHsON0j2BWjDfHe5qNdy2foix0ZTXW2rufm', NULL, '2026-02-27 05:58:09', '2026-02-28 15:31:44', 5, '3746839162', '8335300931', '1990-01-01', 'Alamat Dummy', 'pending');
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "id_plans", "nik", "nomor_wa", "tanggal_lahir", "alamat", "status") VALUES (8, 'Admin Per Kamar Premium', 'perkamar_premium@mail.com', NULL, '$2y$10$NhxAHJmXHjQlQ.3n2kxrreLPpgCF169RBJr.iLiC5EfO9mTVyZIza', NULL, '2026-02-27 05:58:13', '2026-02-28 15:31:44', 4, '1683686193', '8784520340', '1990-01-01', 'Alamat Dummy', 'pending');
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "id_plans", "nik", "nomor_wa", "tanggal_lahir", "alamat", "status") VALUES (9, 'riski', 'user@mail.com', NULL, '$2y$10$Sv1nakRTISp0mHYwZNxV/ukGCL1z86t6uWoX7JmrqvaI.kysHgkm.', NULL, '2026-02-27 06:18:16', '2026-02-28 15:31:44', 1, '1410676567', '8386036012', '1990-01-01', 'Alamat Dummy', 'pending');
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "id_plans", "nik", "nomor_wa", "tanggal_lahir", "alamat", "status") VALUES (4, 'Super Admin', 'superadmin@ngekos.id', NULL, '$2y$10$/2Y8OiG69H/lZ2evduBeCODpimbamn9s.pkgbtjFer5TQoYPbEIIm', NULL, '2026-02-26 16:36:50', '2026-02-28 15:32:01', 6, '9999999999999999', '89999999999', '1990-01-01', NULL, 'active');
INSERT INTO "users" ("id", "name", "email", "email_verified_at", "password", "remember_token", "created_at", "updated_at", "id_plans", "nik", "nomor_wa", "tanggal_lahir", "alamat", "status") VALUES (10, 'Superadmin System', 'superadmin@mail.com', NULL, '$2y$10$OW.4SJdM5O3GXS5FIdttYuRVtfo8XOAylhYCW9MB5P1xYgsn0Bch6', NULL, '2026-02-27 06:18:35', '2026-02-28 15:32:01', 6, '3149411597', '8325901392', '1990-01-01', 'Alamat Dummy', 'active');

-- Table: jenis_langganans
DELETE FROM "jenis_langganans" CASCADE;
INSERT INTO "jenis_langganans" ("id", "nama", "harga", "created_at", "updated_at") VALUES (1, 'MEMBER BIASA', '0.00', '2026-02-27 05:57:53', '2026-02-27 05:57:53');
INSERT INTO "jenis_langganans" ("id", "nama", "harga", "created_at", "updated_at") VALUES (2, 'MEMBER PREMIUM', '50000.00', '2026-02-27 05:57:54', '2026-02-27 05:57:54');
INSERT INTO "jenis_langganans" ("id", "nama", "harga", "created_at", "updated_at") VALUES (3, 'MEMBER PRO', '80000.00', '2026-02-27 05:57:54', '2026-02-27 05:57:54');
INSERT INTO "jenis_langganans" ("id", "nama", "harga", "created_at", "updated_at") VALUES (4, 'PER KAMAR PREMIUM', '3000.00', '2026-02-27 05:57:55', '2026-02-27 05:57:55');
INSERT INTO "jenis_langganans" ("id", "nama", "harga", "created_at", "updated_at") VALUES (5, 'PER KAMAR PRO', '5500.00', '2026-02-27 05:57:56', '2026-02-27 05:57:56');

-- Table: langganans
DELETE FROM "langganans" CASCADE;
INSERT INTO "langganans" ("id", "id_user", "id_langganan", "tanggal_pembayaran", "status", "jumlah_kamar", "created_at", "updated_at") VALUES (5, 9, 1, '2026-02-27', 'active', 0, '2026-02-27 06:18:18', '2026-02-27 06:18:18');
INSERT INTO "langganans" ("id", "id_user", "id_langganan", "tanggal_pembayaran", "status", "jumlah_kamar", "created_at", "updated_at") VALUES (1, 5, 3, '2026-02-27', 'active', 0, '2026-02-27 05:58:04', '2026-02-27 06:18:22');
INSERT INTO "langganans" ("id", "id_user", "id_langganan", "tanggal_pembayaran", "status", "jumlah_kamar", "created_at", "updated_at") VALUES (3, 7, 5, '2026-02-27', 'active', 20, '2026-02-27 05:58:12', '2026-02-27 06:18:30');
INSERT INTO "langganans" ("id", "id_user", "id_langganan", "tanggal_pembayaran", "status", "jumlah_kamar", "created_at", "updated_at") VALUES (4, 8, 4, '2026-02-27', 'active', 10, '2026-02-27 05:58:17', '2026-02-27 06:18:34');
INSERT INTO "langganans" ("id", "id_user", "id_langganan", "tanggal_pembayaran", "status", "jumlah_kamar", "created_at", "updated_at") VALUES (6, 10, 3, '2026-02-27', 'active', 0, '2026-02-27 06:18:37', '2026-02-27 06:18:37');

-- Table: roles
DELETE FROM "roles" CASCADE;
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (1, 'admin', 'web', NULL, NULL);
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (2, 'users', 'web', NULL, NULL);
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (3, 'superadmin', 'web', NULL, NULL);
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (4, 'pro', 'web', '2026-02-27 02:51:43', '2026-02-27 02:51:43');
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (5, 'premium', 'web', '2026-02-27 02:51:48', '2026-02-27 02:51:48');
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (6, 'anak_kos', 'web', '2026-02-27 03:36:14', '2026-02-27 03:36:14');
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (7, 'per_kamar_premium', 'web', '2026-02-27 03:36:20', '2026-02-27 03:36:20');
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (8, 'per_kamar_pro', 'web', '2026-02-27 03:36:22', '2026-02-27 03:36:22');
INSERT INTO "roles" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (9, 'super_admin', 'web', '2026-02-27 03:36:24', '2026-02-27 03:36:24');

-- Table: permissions
DELETE FROM "permissions" CASCADE;
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (1, 'menu.cabang_kos', 'web', '2026-02-27 02:51:36', '2026-02-27 02:51:36');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (2, 'menu.dashboard', 'web', '2026-02-27 02:51:37', '2026-02-27 02:51:37');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (3, 'menu.data_penyewa', 'web', '2026-02-27 02:51:38', '2026-02-27 02:51:38');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (4, 'menu.kamar', 'web', '2026-02-27 02:51:39', '2026-02-27 02:51:39');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (5, 'menu.laporan_pembayaran', 'web', '2026-02-27 02:51:40', '2026-02-27 02:51:40');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (6, 'menu.order', 'web', '2026-02-27 02:51:41', '2026-02-27 02:51:41');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (7, 'menu.pesan_aduan', 'web', '2026-02-27 02:51:42', '2026-02-27 02:51:42');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (8, 'menu.tagihan_sistem', 'web', '2026-02-27 02:51:42', '2026-02-27 02:51:42');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (10, 'menu.jatuh_tempo', 'web', '2026-02-27 03:03:02', '2026-02-27 03:03:02');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (12, 'menu.fasilitas', 'web', '2026-02-27 03:58:43', '2026-02-27 03:58:43');
INSERT INTO "permissions" ("id", "name", "guard_name", "created_at", "updated_at") VALUES (13, 'menu.pengaturan', 'web', '2026-02-27 05:43:54', '2026-02-27 05:43:54');

-- Table: model_has_roles
DELETE FROM "model_has_roles" CASCADE;
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (2, 'app\Models\User', 2);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (3, 'App\Models\User', 3);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (3, 'App\Models\User', 4);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (2, 'App\Models\User', 1);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (2, 'App\Models\User', 9);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (4, 'App\Models\User', 5);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (1, 'App\Models\User', 5);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (8, 'App\Models\User', 7);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (1, 'App\Models\User', 7);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (7, 'App\Models\User', 8);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (1, 'App\Models\User', 8);
INSERT INTO "model_has_roles" ("role_id", "model_type", "model_id") VALUES (3, 'App\Models\User', 10);

-- Table: model_has_permissions
DELETE FROM "model_has_permissions" CASCADE;

-- Table: role_has_permissions
DELETE FROM "role_has_permissions" CASCADE;
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (1, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (2, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (3, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (4, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (5, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (6, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (7, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (8, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (10, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (12, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (13, 3);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (1, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (2, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (3, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (4, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (5, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (6, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (7, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (8, 4);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (2, 5);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (3, 5);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (4, 5);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (5, 5);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (6, 5);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (8, 5);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (2, 7);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (3, 7);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (4, 7);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (5, 7);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (6, 7);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (8, 7);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (1, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (2, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (3, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (4, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (5, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (6, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (13, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (7, 8);
INSERT INTO "role_has_permissions" ("permission_id", "role_id") VALUES (8, 8);

-- Table: kos
DELETE FROM "kos" CASCADE;

-- Table: cabang_kos
DELETE FROM "cabang_kos" CASCADE;

-- Table: kamar
DELETE FROM "kamar" CASCADE;

-- Table: fasilitas
DELETE FROM "fasilitas" CASCADE;

-- Table: transaksi
DELETE FROM "transaksi" CASCADE;

-- Table: transaksi has user
DELETE FROM "transaksi has user" CASCADE;

-- Table: migrations
DELETE FROM "migrations" CASCADE;
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (2, '2014_10_12_100000_create_password_reset_tokens_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (3, '2019_08_19_000000_create_failed_jobs_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (4, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (5, '2026_02_25_042032_create_permission_tables', 1);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (6, '2026_02_27_054255_create_jenis_langganans_table', 2);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (7, '2026_02_27_054256_create_langganans_table', 3);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (8, '2026_02_28_145737_add_status_to_users_table', 4);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (9, '2026_02_27_021805_add_performance_indexes_to_auth_tables', 5);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES (10, '2026_02_28_154819_add_indexes_to_status_columns', 5);

-- Table: personal_access_tokens
DELETE FROM "personal_access_tokens" CASCADE;

SET session_replication_role = 'origin';
