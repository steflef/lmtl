-- Table: places

-- DROP TABLE places;

CREATE TABLE places
(
  -- CARTODB default
  _cartodb_id serial NOT NULL,
  _created_at timestamp without time zone,
  _the_geom geometry, -- POINTS
  _updated_at timestamp without time zone,

  -- Location
  address character varying(255),
  city character varying(255),
  latitude double precision,
  location text,
  longitude double precision,
  postal_code character varying(7),
  tel_number character varying(255),
  website character varying(255),

  -- Metadata
  created_by integer, -- user_id
  dataset_id integer, -- maybe dataset_id varying (255) NOT NULL,
  desc_en text,
  desc_fr text,
  label varying(255),
  name_en character varying(255) NOT NULL,
  name_fr character varying(255) NOT NULL,
  place_id serial NOT NULL,
  privacy smallint,
  slug character varying(255) NOT NULL
  status smallint,
  version smallint,

  -- Categories
  primary_category_id integer,
  secondary_category_id integer,

  -- Extra Fields
  tags text,

  CONSTRAINT places_pkey PRIMARY KEY ( _cartodb_id )
);


-- Table: datasets

-- DROP TABLE datasets;
CREATE TABLE datasets
(
  -- CARTODB default
  _cartodb_id serial NOT NULL,
  _created_at timestamp without time zone,
  _the_geom geometry, -- POLYGON
  _updated_at timestamp without time zone,

  -- Dataset Meta
  attributions text NOT NULL, -- source
  bbox_4326 geography(POLYGON,4326), -- POLYGON
  collection_id integer NOT NULL,
  created_by integer NOT NULL, -- user_id
  dataset_id serial NOT NULL, -- maybe dataset_id varchar (255) NOT NULL,
  dataset_extra_fields TEXT, -- places/tags JSON
  desc_en text NOT NULL,
  desc_fr text NOT NULL,
  google_drive_id varchar(255),
  label varchar(255) NOT NULL,
  licence TEXT NOT NULL,
  name_en varchar(255) NOT NULL,
  name_fr varchar(255) NOT NULL,
  privacy smallint,
  slug varchar(255) NOT NULL
  status smallint,
  version smallint,

  -- Categories
  primary_category_id integer,
  secondary_category_id integer,
  tertiary_category_id integer,

  -- Stats
  downloaded_count integer DEFAULT 0,

  -- Localy Saved File infos
  file_format varchar(64) NOT NULL,
  file_hash varchar (255) NOT NULL,
  file_mime varchar(64) NOT NULL,
  file_size varchar(255) NOT NULL,
  file_uri varchar(255) NOT NULL,

  CONSTRAINT datasets_pkey PRIMARY KEY ( _cartodb_id )
);

-- Table: categories

-- DROP TABLE categories;

CREATE TABLE categories
(

  -- CARTODB default
  _cartodb_id serial NOT NULL,
  _created_at timestamp without time zone,
  _updated_at timestamp without time zone,

  -- META
  id integer NOT NULL,
  parent_id integer NOT NULL,
  en character varchar(255) NOT NULL,
  fr character varchar(255) NOT NULL,
  icon character varchar(255) NOT NULL,

  CONSTRAINT datasets_pkey PRIMARY KEY ( _cartodb_id )
);

-- Table: geo_admin

-- DROP TABLE geo_admin;

CREATE TABLE geo_admin
(
  -- CARTODB default
  _cartodb_id serial NOT NULL,
  _created_at timestamp without time zone,
  _the_geom geometry, -- POLYGON
  _updated_at timestamp without time zone,

  -- META
  attributions text NOT NULL, -- source
  bbox_4326 geography(POLYGON,4326),
  desc_fr text NOT NULL,
  name_fr varchar(255) NOT NULL,

  CONSTRAINT geo_admin_pkey PRIMARY KEY ( _cartodb_id )
);

-- Table: collections

-- DROP TABLE collections;

CREATE TABLE collections
(
  -- CARTODB default
  _cartodb_id serial NOT NULL,
  _created_at timestamp without time zone,
  _the_geom geometry,
  _updated_at timestamp without time zone,

  -- META
  collection_id serial NOT NULL,
  created_by integer, -- user_id
  desc_en text NOT NULL,
  desc_fr text NOT NULL,
  name_en character varying(255) NOT NULL,
  name_fr character varying(255) NOT NULL,
  privacy smallint,

  CONSTRAINT collections_pkey PRIMARY KEY ( _cartodb_id )
);