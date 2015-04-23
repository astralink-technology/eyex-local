DROP PROCEDURE IF EXISTS get_device;
CREATE PROCEDURE get_device(
  IN pDeviceId VARCHAR(128)
  , IN pName VARCHAR(128)
  , IN pType VARCHAR(4)
  , IN pType2 VARCHAR(4)
  , IN pIntPrefix INTEGER
  , IN pDoor VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS deviceTemp AS (
    SELECT
      device._id as device_id
      , device.name
      , device.type
      , device.type2
      , device.int_prefix
      , device.door
    FROM device
    WHERE (
      ((pDeviceId IS NULL) OR (device._id = pDeviceId)) AND
      ((pName IS NULL) OR (device.name = pName)) AND
      ((pType IS NULL) OR (device.type = pType)) AND
      ((pType2 IS NULL) OR (device.type2 = pType2)) AND
      ((pIntPrefix IS NULL) OR (device.int_prefix = pIntPrefix)) AND
      ((pDoor IS NULL) OR (device.door = pDoor))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    deviceTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from deviceTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
