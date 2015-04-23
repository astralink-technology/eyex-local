DROP PROCEDURE IF EXISTS add_event;
CREATE PROCEDURE add_event(
  pEventId VARCHAR(128)
  , pEventTypeId VARCHAR(6)
  , pAccessMethod VARCHAR(6)
  , pCreateDate TIMESTAMP
  , pDoor VARCHAR(128)
  , pDevice VARCHAR(128)
  , pEntity VARCHAR(128)
)
BEGIN
  INSERT INTO event(
    _id
    , event_type_id
    , access_method
    , create_date
    , door
    , device
    , entity
  ) VALUES (
    pEventId
    , pEventTypeId
    , pAccessMethod
    , pCreateDate
    , pDoor
    , pDevice
    , pEntity
  );
  SELECT LAST_INSERT_ID();
END;