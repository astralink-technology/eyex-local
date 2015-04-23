DROP PROCEDURE IF EXISTS get_device_door_relationship;
CREATE PROCEDURE get_device_door_relationship(
  IN pDeviceDoorRelationshipId INTEGER
  , IN pDoor VARCHAR(128)
  , IN pDevice VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS deviceDoorRelationshipTemp AS (
    SELECT
      device_door_relationship._id as device_door_relationship_id
      , device_door_relationship.device
      , device_door_relationship.door
    FROM device_door_relationship
    WHERE (
      ((pDeviceDoorRelationshipId IS NULL) OR (device_door_relationship._id = pDeviceDoorRelationshipId)) AND
      ((pDevice IS NULL) OR (device_door_relationship.device = pDevice)) AND
      ((pDoor IS NULL) OR (device_door_relationship.door = pDoor))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    deviceDoorRelationshipTemp;

  -- LIMITS
  SET @pageSize = 99999999999;
  SET @skipSize = 0;
  IF pPageSize IS NOT NULL AND pSkipSize IS NOT NULL THEN
    SET @pageSize = pPageSize;
    SET @skipSize = pSkipSize;
  ElSEIF pPageSize IS NOT NULL AND pSkipSize IS NULL THEN
    SET @pageSize = pPageSize;
    SET @skipSize = 0;
  ElSEIF pPageSize IS NULL AND pSkipSize IS NOT NULL THEN
    SET @pageSize = 9999999999999;
    SET @skipSize = pSkipSize;
  END IF;

  PREPARE stmt FROM "SELECT *, ? AS total_rows from deviceDoorRelationshipTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
