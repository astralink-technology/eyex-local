CREATE TABLE IF NOT EXISTS device_door_relationship (
  _id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY
  , device VARCHAR(128)
  , door VARCHAR(128)
);