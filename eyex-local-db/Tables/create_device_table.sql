CREATE TABLE IF NOT EXISTS device (
  _id VARCHAR(128) NOT NULL PRIMARY KEY
  , name VARCHAR(128)
  , type VARCHAR(4)
  , type2 VARCHAR(4)
  , int_prefix INTEGER
  , door VARCHAR(128)
);