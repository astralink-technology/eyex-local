DROP PROCEDURE IF EXISTS add_device;

CREATE PROCEDURE add_device(
  IN pDeviceId VARCHAR(128)
  , IN pName VARCHAR(128)
  , IN pType VARCHAR(4)
  , IN pType2 VARCHAR(4)
  , IN pIntPrefix INTEGER
  , IN pDoor VARCHAR(128)
)
BEGIN
  INSERT INTO device(
    _id
    , name
    , type
    , type2
    , int_prefix
    , door
  ) VALUES (
    pDeviceId
    , pName
    , pType
    , pType2
    , pIntPrefix
    , pDoor
  );

  SELECT LAST_INSERT_ID();
END;