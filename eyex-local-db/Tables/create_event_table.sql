CREATE TABLE IF NOT EXISTS event (
  _id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY
  , event_type_id VARCHAR(6)
  , access_method VARCHAR(6)
  , door_name VARCHAR(128)
  , create_date TIMESTAMP
  , door VARCHAR(128)
  , device VARCHAR(128)
  , entity VARCHAR(128)
);