CREATE TABLE IF NOT EXISTS entity (
  _id VARCHAR(128) NOT NULL PRIMARY KEY
  , authentication_string VARCHAR(256)
  , authentication_string_lower VARCHAR(256)
  , first_name VARCHAR(256)
  , last_name VARCHAR(256)
  , name VARCHAR(512)
  , extension VARCHAR(128)
  , card VARCHAR(256)
  , pin VARCHAR(16)
);