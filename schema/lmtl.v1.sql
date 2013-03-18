-- Table: rao911_dev

-- DROP TABLE rao911_dev;

-- USERS SQLITE
CREATE TABLE users(
  id INTEGER PRIMARY KEY,
  email TEXT UNIQUE,
  password TEXT,
  salt TEXT,
  role INTEGER,
  username TEXT UNIQUE,
  datasets_count INTEGER,
  created_at NUMERIC,
  updated_at NUMERIC
);

INSERT INTO users (email, password, salt, role, username, datasets_count, created_at, updated_at)
VALUES ('admin@admin.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', '$2y$24$sRWNKw7DRrtPJBSI9vALPA', 1, 'STEFLEF', 0, '1359304821', '1359304821');

-- GROUPS SQLITE
CREATE TABLE groups(
  id serial NOT NULL,
  name character varying(20) NOT NULL,
  description character varying(100) NOT NULL,
  CONSTRAINT groups_pkey PRIMARY KEY (id )
);

-- COLLECTIONS SQLITE
CREATE TABLE collections
(
  id INTEGER PRIMARY KEY,
  user_id INTEGER,
  name TEXT,
  description TEXT,
  privacy INTEGER,
  created_at NUMERIC,
  updated_at NUMERIC,
  downloaded_count INTEGER,
  datasets_count INTEGER,
  status INTEGER
);

-- DATASETS SQLITE
CREATE TABLE datasets
(
  id INTEGER PRIMARY KEY,
  user_id INTEGER,
  collection_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  description TEXT NOT NULL,
  label TEXT NOT NULL ,
  privacy INTEGER,
  mime_type TEXT NOT NULL,
  tmpl_json TEXT,
  bbox_26918 TEXT,
  bbox_4326 TEXT,
  bbox_3857 TEXT,
  created_at NUMERIC,
  updated_at NUMERIC,
  downloaded_count INTEGER,
  features_count INTEGER,
  status INTEGER
);

-- PLACES SQLITE
CREATE TABLE places
(
  id INTEGER PRIMARY KEY,
  user_id INTEGER,
  dataset_id INTEGER,
  title TEXT NOT NULL,
  description TEXT,
  location TEXT,
  latitude FLOAT,
  longitude FLOAT,
  geohash TEXT,
  point_26918 TEXT,
  point_4326 TEXT,
  point_3857 TEXT,
  privacy INTEGER,
  status INTEGER,
  tags TEXT, -- json
  created_at NUMERIC,
  updated_at NUMERIC
);

-- CATEGORIES SQLITE
CREATE TABLE categories
(
  id INTEGER PRIMARY KEY,
  parent_id INTEGER,
  dataset_id INTEGER,
  french TEXT NOT NULL,
  english TEXT NOT NULL,
  icon TEXT NOT NULL
);

-- PLACE_CATEGORIES SQLITE
CREATE TABLE place_categories
(
  id INTEGER PRIMARY KEY,
  place_id INTEGER NOT NULL,
  categorie_id INTEGER NOT NULL,
  cat_order INTEGER
);