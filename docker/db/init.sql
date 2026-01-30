--
-- PostgreSQL database dump
--

\restrict W3poeLMOmHlJw8RVaikhUgLji4PLlqY9cDZ0zqohRA0SsrkoYK4U1vCFnLbpw3r

-- Dumped from database version 18.1 (Debian 18.1-1.pgdg13+2)
-- Dumped by pg_dump version 18.1

-- Started on 2026-01-30 09:27:20 UTC

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 224 (class 1259 OID 16420)
-- Name: activities; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.activities (
    id integer NOT NULL,
    user_id integer NOT NULL,
    category_id integer,
    title character varying(255) NOT NULL,
    start_time timestamp with time zone NOT NULL,
    end_time timestamp with time zone NOT NULL,
    is_completed boolean DEFAULT false,
    is_recurring boolean DEFAULT false,
    recurrence_pattern character varying(100),
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.activities OWNER TO docker;

--
-- TOC entry 223 (class 1259 OID 16419)
-- Name: activities_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.activities_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.activities_id_seq OWNER TO docker;

--
-- TOC entry 3502 (class 0 OID 0)
-- Dependencies: 223
-- Name: activities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.activities_id_seq OWNED BY public.activities.id;


--
-- TOC entry 222 (class 1259 OID 16403)
-- Name: categories; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.categories (
    id integer NOT NULL,
    user_id integer,
    name character varying(50) NOT NULL,
    color_hex character varying(7) DEFAULT '#3b82f6'::character varying NOT NULL,
    icon_name character varying(50)
);


ALTER TABLE public.categories OWNER TO docker;

--
-- TOC entry 221 (class 1259 OID 16402)
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categories_id_seq OWNER TO docker;

--
-- TOC entry 3503 (class 0 OID 0)
-- Dependencies: 221
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- TOC entry 227 (class 1259 OID 16464)
-- Name: roles; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.roles (
    id integer NOT NULL,
    name character varying(50) NOT NULL
);


ALTER TABLE public.roles OWNER TO docker;

--
-- TOC entry 226 (class 1259 OID 16463)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO docker;

--
-- TOC entry 3504 (class 0 OID 0)
-- Dependencies: 226
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 225 (class 1259 OID 16445)
-- Name: user_settings; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.user_settings (
    user_id integer NOT NULL,
    day_start_time time without time zone DEFAULT '08:00:00'::time without time zone,
    day_end_time time without time zone DEFAULT '22:00:00'::time without time zone,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.user_settings OWNER TO docker;

--
-- TOC entry 220 (class 1259 OID 16386)
-- Name: users; Type: TABLE; Schema: public; Owner: docker
--

CREATE TABLE public.users (
    id integer NOT NULL,
    firstname character varying(100) NOT NULL,
    lastname character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    password character varying(255) NOT NULL,
    enabled boolean DEFAULT true,
    role_id integer NOT NULL
);


ALTER TABLE public.users OWNER TO docker;

--
-- TOC entry 219 (class 1259 OID 16385)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: docker
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO docker;

--
-- TOC entry 3505 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: docker
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 3312 (class 2604 OID 16423)
-- Name: activities id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.activities ALTER COLUMN id SET DEFAULT nextval('public.activities_id_seq'::regclass);


--
-- TOC entry 3310 (class 2604 OID 16406)
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- TOC entry 3319 (class 2604 OID 16467)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 3308 (class 2604 OID 16389)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 3493 (class 0 OID 16420)
-- Dependencies: 224
-- Data for Name: activities; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.activities (id, user_id, category_id, title, start_time, end_time, is_completed, is_recurring, recurrence_pattern, created_at) FROM stdin;
8	4	19	test	2026-01-27 09:00:00+00	2026-01-27 10:00:00+00	f	t	daily	2026-01-27 17:52:06.349255
6	2	8	test	2026-01-27 09:00:00+00	2026-01-27 10:00:00+00	t	f	\N	2026-01-27 04:33:37.559647
7	2	6	test	2026-01-01 09:00:00+00	2026-01-01 10:00:00+00	f	t	daily	2026-01-27 17:49:29.54725
\.


--
-- TOC entry 3491 (class 0 OID 16403)
-- Dependencies: 222
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.categories (id, user_id, name, color_hex, icon_name) FROM stdin;
5	2	Work	#2196f3	
6	2	Health	#00bcd4	
7	2	Personal	#ff9800	
8	2	Education	#9c27b0	
9	3	Work	#2196f3	
10	3	Health	#00bcd4	
11	3	Personal	#ff9800	
12	3	Education	#9c27b0	
13	2	test	#9b65ec	📓
14	2	testing	#000000	
15	4	Work	#2196f3	
16	4	Health	#00bcd4	
17	4	Personal	#ff9800	
18	4	Education	#9c27b0	
19	4	test	#0d0d0d	📓
\.


--
-- TOC entry 3496 (class 0 OID 16464)
-- Dependencies: 227
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.roles (id, name) FROM stdin;
1	standard
2	admin
\.


--
-- TOC entry 3494 (class 0 OID 16445)
-- Dependencies: 225
-- Data for Name: user_settings; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.user_settings (user_id, day_start_time, day_end_time, updated_at) FROM stdin;
\.


--
-- TOC entry 3489 (class 0 OID 16386)
-- Dependencies: 220
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: docker
--

COPY public.users (id, firstname, lastname, email, password, enabled, role_id) FROM stdin;
3	test	tester	tester@test.com	$2y$10$/40CHtt5AjcfB0wjqW4UmOrvQY6S8/GA5BfUftHU2XL0roBgMuB.e	t	1
4	test	test	test@test.com	$2y$10$.SV1FJInv.jqVSRvgDWm.OXcjvewNQJEqRxH3G1NC7qzucj1bJ8Ri	t	1
2	Tomasz	Ostrowski	diadon8080@gmail.com	$2y$10$6Ko.O7TjYQYT7xdufRbg9OuyU8SY3RdZNPADd3HF0iZiBZtHKO0Vi	t	2
\.


--
-- TOC entry 3506 (class 0 OID 0)
-- Dependencies: 223
-- Name: activities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.activities_id_seq', 8, true);


--
-- TOC entry 3507 (class 0 OID 0)
-- Dependencies: 221
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.categories_id_seq', 19, true);


--
-- TOC entry 3508 (class 0 OID 0)
-- Dependencies: 226
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.roles_id_seq', 2, true);


--
-- TOC entry 3509 (class 0 OID 0)
-- Dependencies: 219
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: docker
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- TOC entry 3328 (class 2606 OID 16433)
-- Name: activities activities_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.activities
    ADD CONSTRAINT activities_pkey PRIMARY KEY (id);


--
-- TOC entry 3325 (class 2606 OID 16412)
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- TOC entry 3333 (class 2606 OID 16473)
-- Name: roles roles_name_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_key UNIQUE (name);


--
-- TOC entry 3335 (class 2606 OID 16471)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 3331 (class 2606 OID 16453)
-- Name: user_settings user_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.user_settings
    ADD CONSTRAINT user_settings_pkey PRIMARY KEY (user_id);


--
-- TOC entry 3321 (class 2606 OID 16401)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 3323 (class 2606 OID 16399)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 3329 (class 1259 OID 16444)
-- Name: idx_activities_user_date; Type: INDEX; Schema: public; Owner: docker
--

CREATE INDEX idx_activities_user_date ON public.activities USING btree (user_id, start_time);


--
-- TOC entry 3326 (class 1259 OID 16418)
-- Name: idx_categories_user; Type: INDEX; Schema: public; Owner: docker
--

CREATE INDEX idx_categories_user ON public.categories USING btree (user_id);


--
-- TOC entry 3338 (class 2606 OID 16439)
-- Name: activities activities_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.activities
    ADD CONSTRAINT activities_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- TOC entry 3339 (class 2606 OID 16434)
-- Name: activities activities_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.activities
    ADD CONSTRAINT activities_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3337 (class 2606 OID 16413)
-- Name: categories categories_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- TOC entry 3336 (class 2606 OID 16474)
-- Name: users fk_users_role; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES public.roles(id);


--
-- TOC entry 3340 (class 2606 OID 16454)
-- Name: user_settings user_settings_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: docker
--

ALTER TABLE ONLY public.user_settings
    ADD CONSTRAINT user_settings_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


-- Completed on 2026-01-30 09:27:20 UTC

--
-- PostgreSQL database dump complete
--