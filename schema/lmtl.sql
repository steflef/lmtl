-- Table: rao911_dev

-- DROP TABLE rao911_dev;

CREATE TABLE places
(
  id serial NOT NULL ,
  user_id integer,
  dataset_id integer,
  name character varying(255) NOT NULL ,
  description text,
  address text,
  latitude double precision,
  longitude double precision,
  geohash varying(255),
  point_26918 geography(POINT,26918),
  point_4326 geography(POINT,4326),
  point_3857 geography(POINT,3857),
  privacy smallint,
  status smallint,
  tags test,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  CONSTRAINT places_pkey PRIMARY KEY (id )
);

  address text,
  latitude double precision,
  longitude double precision,
  geohash varying(255),
  point_26918 geography(POINT,26918),
  point_4326 geography(POINT,4326),
  point_3857 geography(POINT,3857),
  privacy smallint,
  status smallint,
  tags test,
ALTER TABLE test_2 ADD COLUMN cq_id serial NOT NULL;
ALTER TABLE test_2 ADD COLUMN location text;
ALTER TABLE test_2 ADD COLUMN latitude double precision;
ALTER TABLE test_2 ADD COLUMN longitude double precision;
ALTER TABLE test_2 ADD COLUMN geohash varying(255);
ALTER TABLE test_2 ADD COLUMN point_4326 geography(POINT,4326);
ALTER TABLE test_2 ADD COLUMN privacy smallint;
ALTER TABLE test_2 ADD COLUMN status smallint;
ALTER TABLE test_2 ADD COLUMN tags text;

CREATE TABLE datasets
(
  id serial NOT NULL ,
  user_id integer NOT NULL ,
  collection_id integer NOT NULL ,
  name character varying(255) NOT NULL ,
  description text NOT NULL ,
  label varying (255) NOT NULL ,
  privacy smallint,
  mime_type varying(64) NOT NULL,
  -- points hstore,
  tmpl hstore,
  -- points_json text,
  tmpl_json text,
  bbox_26918 geography(POLYGON,26918),
  bbox_4326 geography(POLYGON,4326),
  bbox_3857 geography(POLYGON,3857),
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  downloaded_count integer DEFAULT 0,
  features_count integer DEFAULT 0,
  status smallint,
  CONSTRAINT datasets_pkey PRIMARY KEY (id )
);

CREATE TABLE collections
(
  id serial NOT NULL ,
  user_id integer,
  name character varying(255),
  description text,
  privacy smallint,
  created_at timestamp without time zone,
  updated_at timestamp without time zone,
  downloaded_count integer DEFAULT 0,
  datasets_count integer DEFAULT 0,
  status smallint,
  CONSTRAINT collections_pkey PRIMARY KEY (id )
);

CREATE TABLE apikeys(
  id serial NOT NULL ,
  api_key varchar UNIQUE ,
  user_id integer NOT NULL ,
  domain varchar NOT NULL,
  CONSTRAINT apikeys_pkey PRIMARY KEY (id )
);

CREATE TABLE users(
  id serial NOT NULL ,
  email varchar NOT NULL UNIQUE ,
  password integer NOT NULL ,
  salt varchar NOT NULL,
  username varchar NOT NULL UNIQUE,
  datasets_count integer DEFAULT 0,
  created_at timestamp without time zone ,
  updated_at timestamp without time zone,
  CONSTRAINT users_pkey PRIMARY KEY (id )
);
-- SQLITE
CREATE TABLE users(
  id INTEGER PRIMARY KEY,
  email TEXT UNIQUE,
  password TEXT,
  salt TEXT,
  role INTEGER,
  username TEXT UNIQUE,
  datasets_count INTEGER,
  created_at NUMERIC ,
  updated_at NUMERIC
);

INSERT INTO users (email, password, salt, role, username, datasets_count, created_at, updated_at)
VALUES ('admin@admin.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', '$2y$24$sRWNKw7DRrtPJBSI9vALPA', 1, 'STEFLEF', 0, '1359304821', '1359304821');

CREATE TABLE groups(
  id serial NOT NULL,
  name character varying(20) NOT NULL,
  description character varying(100) NOT NULL,
  CONSTRAINT groups_pkey PRIMARY KEY (id )
);

CREATE TABLE queue(
  id serial NOT NULL,
  items integer,
  run_script character varying(255),
  script_params text,
  completed smallint,
  inserted_dt timestamp without time zone,
  completed_dt timestamp without time zone,
  CONSTRAINT queue_pkey PRIMARY KEY (id )
);