DROP PROCEDURE IF EXISTS delete_device;
CREATE PROCEDURE delete_device(
  IN pDeviceId VARCHAR(128)
)
BEGIN
  IF
    pDeviceId IS NOT NULL
  THEN
    DELETE FROM device WHERE
    ((pDeviceId IS NULL) OR (device._id = pDeviceId));
  END IF;
END;