CREATE TABLE IF NOT EXISTS phone (
  _id VARCHAR(128) NOT NULL PRIMARY KEY --
  , phone_digits VARCHAR(32) -- full number with country code
  , country_code VARCHAR(6) -- +65
  , code VARCHAR(6) -- SG
  , type VARCHAR(6) -- Primary / Secondary
  , entity VARCHAR(128)
);