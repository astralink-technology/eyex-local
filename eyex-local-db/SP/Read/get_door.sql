DROP PROCEDURE IF EXISTS get_door;
CREATE PROCEDURE get_door(
  IN pDoorId VARCHAR(128)
  , IN pDoorName VARCHAR(128)
  , IN pDoorNode INTEGER
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS doorTemp AS (
    SELECT
      door._id as door_id
      , door.door_name
      , door.door_node
    FROM door
    WHERE (
      ((pDoorId IS NULL) OR (door._id = pDoorId)) AND
      ((pDoorName IS NULL) OR (door.door_name = pDoorName)) AND
      ((pDoorNode IS NULL) OR (door.door_node = pDoorNode))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    doorTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from doorTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
