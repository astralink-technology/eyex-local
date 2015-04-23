DROP PROCEDURE IF EXISTS delete_device_door_relationship;
CREATE PROCEDURE delete_device_door_relationship(
  IN pDeviceDoorRelationshipId INTEGER
)
BEGIN
  IF
    pDeviceDoorRelationshipId IS NOT NULL
  THEN
    DELETE FROM device_door_relationship WHERE
    ((pDeviceDoorRelationshipId IS NULL) OR (device_door_relationship._id = pDeviceDoorRelationshipId));
  END IF;
END;