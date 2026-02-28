-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.

CREATE TABLE public.cabang_kos (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  id_user bigint,
  kode_kos bigint,
  kode_koscabang bigint GENERATED ALWAYS AS IDENTITY NOT NULL UNIQUE,
  nama_kos text,
  alamat text,
  rentan_harga numeric,
  CONSTRAINT cabang_kos_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id),
  CONSTRAINT cabang_kos_kode_kos_fkey FOREIGN KEY (kode_kos) REFERENCES public.kos(kode_kos)
);
CREATE TABLE public.failed_jobs (
  id bigint NOT NULL DEFAULT nextval('failed_jobs_id_seq'::regclass),
  uuid character varying NOT NULL UNIQUE,
  connection text NOT NULL,
  queue text NOT NULL,
  payload text NOT NULL,
  exception text NOT NULL,
  failed_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT failed_jobs_pkey PRIMARY KEY (id)
);
CREATE TABLE public.fasilitas (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  nama_fasilitas text,
  id_kamar bigint,
  CONSTRAINT fasilitas_pkey PRIMARY KEY (id),
  CONSTRAINT fasilitas_id_kamar_fkey FOREIGN KEY (id_kamar) REFERENCES public.kamar(id)
);
CREATE TABLE public.jenis_langganans (
  id bigint NOT NULL DEFAULT nextval('jenis_langganans_id_seq'::regclass),
  nama character varying NOT NULL,
  harga numeric NOT NULL,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  CONSTRAINT jenis_langganans_pkey PRIMARY KEY (id)
);
CREATE TABLE public.kamar (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  nomor_kamar text UNIQUE,
  id_kos bigint,
  harga_kamar numeric,
  status_kamar boolean,
  kode_kamar bigint GENERATED ALWAYS AS IDENTITY NOT NULL UNIQUE,
  CONSTRAINT kamar_pkey PRIMARY KEY (id),
  CONSTRAINT kamar_id_kos_fkey FOREIGN KEY (id_kos) REFERENCES public.kos(id)
);
CREATE TABLE public.kos (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  nama_kos text,
  lokasi kos text,
  rentan_harga numeric,
  id_pemilik bigint,
  kode_kos bigint GENERATED ALWAYS AS IDENTITY NOT NULL UNIQUE,
  CONSTRAINT kos_pkey PRIMARY KEY (id),
  CONSTRAINT KOS_id_pemilik_fkey FOREIGN KEY (id_pemilik) REFERENCES public.users(id)
);
CREATE TABLE public.langganans (
  id bigint NOT NULL DEFAULT nextval('langganans_id_seq'::regclass),
  id_user bigint NOT NULL,
  id_langganan bigint NOT NULL,
  tanggal_pembayaran date,
  status character varying NOT NULL DEFAULT 'pending'::character varying,
  jumlah_kamar integer NOT NULL DEFAULT 0,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  CONSTRAINT langganans_pkey PRIMARY KEY (id),
  CONSTRAINT langganans_id_user_foreign FOREIGN KEY (id_user) REFERENCES public.users(id),
  CONSTRAINT langganans_id_langganan_foreign FOREIGN KEY (id_langganan) REFERENCES public.jenis_langganans(id)
);
CREATE TABLE public.migrations (
  id integer NOT NULL DEFAULT nextval('migrations_id_seq'::regclass),
  migration character varying NOT NULL,
  batch integer NOT NULL,
  CONSTRAINT migrations_pkey PRIMARY KEY (id)
);
CREATE TABLE public.model_has_permissions (
  permission_id bigint NOT NULL,
  model_type character varying NOT NULL,
  model_id bigint NOT NULL,
  CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type),
  CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id)
);
CREATE TABLE public.model_has_roles (
  role_id bigint NOT NULL,
  model_type character varying NOT NULL,
  model_id bigint NOT NULL,
  CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type),
  CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id)
);
CREATE TABLE public.password_reset_tokens (
  email character varying NOT NULL,
  token character varying NOT NULL,
  created_at timestamp without time zone,
  CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email)
);
CREATE TABLE public.pending_users (
  id bigint NOT NULL DEFAULT nextval('pending_users_id_seq'::regclass),
  name character varying NOT NULL,
  email character varying NOT NULL UNIQUE,
  password character varying NOT NULL,
  nik character varying NOT NULL,
  nomor_wa character varying NOT NULL,
  tanggal_lahir date NOT NULL,
  alamat text NOT NULL,
  id_plans integer NOT NULL,
  plan_type character varying,
  package_type character varying,
  jumlah_kamar integer NOT NULL DEFAULT 0,
  status character varying NOT NULL DEFAULT 'pending'::character varying,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  rejection_reason text,
  CONSTRAINT pending_users_pkey PRIMARY KEY (id)
);
CREATE TABLE public.permissions (
  id bigint NOT NULL DEFAULT nextval('permissions_id_seq'::regclass),
  name character varying NOT NULL,
  guard_name character varying NOT NULL,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  CONSTRAINT permissions_pkey PRIMARY KEY (id)
);
CREATE TABLE public.personal_access_tokens (
  id bigint NOT NULL DEFAULT nextval('personal_access_tokens_id_seq'::regclass),
  tokenable_type character varying NOT NULL,
  tokenable_id bigint NOT NULL,
  name character varying NOT NULL,
  token character varying NOT NULL UNIQUE,
  abilities text,
  last_used_at timestamp without time zone,
  expires_at timestamp without time zone,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id)
);
CREATE TABLE public.plans (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  nama_plans text,
  CONSTRAINT plans_pkey PRIMARY KEY (id)
);
CREATE TABLE public.role_has_permissions (
  permission_id bigint NOT NULL,
  role_id bigint NOT NULL,
  CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id),
  CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id),
  CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id)
);
CREATE TABLE public.roles (
  id bigint NOT NULL DEFAULT nextval('roles_id_seq'::regclass),
  name character varying NOT NULL,
  guard_name character varying NOT NULL,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  CONSTRAINT roles_pkey PRIMARY KEY (id)
);
CREATE TABLE public.transaksi (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  jumlah_pembyaran numeric,
  status_pembayaran text,
  user_id bigint,
  tanggal_pembayaran date,
  CONSTRAINT transaksi_pkey PRIMARY KEY (id),
  CONSTRAINT pembayaran_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id)
);
CREATE TABLE public.transaksi has user (
  id bigint GENERATED ALWAYS AS IDENTITY NOT NULL,
  created_at timestamp with time zone NOT NULL DEFAULT now(),
  id_user bigint,
  id_transaksi bigint,
  CONSTRAINT transaksi has user_pkey PRIMARY KEY (id),
  CONSTRAINT transaksi has user_id_transaksi_fkey FOREIGN KEY (id_transaksi) REFERENCES public.transaksi(id),
  CONSTRAINT transaksi has user_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id)
);
CREATE TABLE public.users (
  id bigint NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  name character varying NOT NULL,
  email character varying NOT NULL UNIQUE,
  email_verified_at timestamp without time zone,
  password character varying NOT NULL UNIQUE,
  remember_token character varying,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  id_plans bigint,
  nik numeric NOT NULL UNIQUE,
  nomor_wa numeric UNIQUE,
  tanggal_lahir date,
  alamat text,
  status character varying NOT NULL DEFAULT 'pending'::character varying,
  CONSTRAINT users_pkey PRIMARY KEY (id),
  CONSTRAINT users_id_plans_fkey FOREIGN KEY (id_plans) REFERENCES public.plans(id)
);