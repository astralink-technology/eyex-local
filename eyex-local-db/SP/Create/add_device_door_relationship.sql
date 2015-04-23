DROP PROCEDURE IF EXISTS add_device_door_relationship;

CREATE PROCEDURE add_device_door_relationship(
  IN pDevice VARCHAR(128)
  , IN pDoor VARCHAR(128)
)
BEGIN
  INSERT INTO device_door_relationship(
    device
    , door
  ) VALUES (
    pDevice
    , pDoor
  );

  SELECT LAST_INSERT_ID();
END;