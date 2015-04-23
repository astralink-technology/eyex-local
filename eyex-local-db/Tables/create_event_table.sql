CREATE TABLE IF NOT EXISTS event (
  _id VARCHAR(128) NOT NULL PRIMARY KEY
  , event_type_id VARCHAR(6)
  , access_method VARCHAR(6)
  , create_date TIMESTAMP
  , door VARCHAR(128)
  , device VARCHAR(128)
  , entity VARCHAR(128)
);