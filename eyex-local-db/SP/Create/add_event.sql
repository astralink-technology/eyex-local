DROP PROCEDURE IF EXISTS add_event;
CREATE PROCEDURE add_event(
  pEventTypeId VARCHAR(6)
  , pAccessMethod VARCHAR(6)
  , pCreateDate TIMESTAMP
  , pDoor VARCHAR(128)
  , pDevice VARCHAR(128)
  , pEntity VARCHAR(128)
  , pDoorName VARCHAR(128)
)
BEGIN
  INSERT INTO event(
    event_type_id
    , access_method
    , create_date
    , door
    , device
    , entity
    , door_name
  ) VALUES (
    pEventTypeId
    , pAccessMethod
    , pCreateDate
    , pDoor
    , pDevice
    , pEntity
    , pDoorName
  );
  SELECT LAST_INSERT_ID();
END;