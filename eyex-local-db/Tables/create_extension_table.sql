CREATE TABLE IF NOT EXISTS extension (
  _id VARCHAR(128) NOT NULL PRIMARY KEY
  , number INTEGER
  , extension_password VARCHAR(256)
  , entity VARCHAR(128)
);