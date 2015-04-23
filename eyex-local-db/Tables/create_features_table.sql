CREATE TABLE IF NOT EXISTS features(
  _id VARCHAR(128) NOT NULL PRIMARY KEY
  , remote_door_control VARCHAR(8)
  , local_door_control VARCHAR(8)
  , voicemail_password VARCHAR(256)
  , voicemail_extension VARCHAR(128)
  , pickup VARCHAR(32)
  , extra1 VARCHAR(32)
  , extra2 VARCHAR(32)
  , extra3 VARCHAR(32)
  , extra4 VARCHAR(32)
  , device VARCHAR(128)
);