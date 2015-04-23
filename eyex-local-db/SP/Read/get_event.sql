DROP PROCEDURE IF EXISTS get_event;
CREATE PROCEDURE get_event(
  IN pEventId VARCHAR(128)
  , IN pEventTypeId VARCHAR(6)
  , IN pAccessMethod VARCHAR(6)
  , IN pDoor VARCHAR(128)
  , IN pDevice VARCHAR(128)
  , IN pEntity VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS eventTemp AS (
    SELECT
      event._id as event_id
      , event.entity_type_id
      , event.access_method
      , event.create_date
      , event.door
      , event.device
      , event.entity
    FROM event
    WHERE (
      ((pEventId IS NULL) OR (event._id = pEventId)) AND
      ((pEntityTypeId IS NULL) OR (event.entity_type_id = pEntityTypeId)) AND
      ((pAccessMethod IS NULL) OR (event.access_method = pAccessMethod)) AND
      ((pDoor IS NULL) OR (event.door = pDoor)) AND
      ((pDevice IS NULL) OR (event.device = pDevice)) AND
      ((pEntity IS NULL) OR (event.entity = pEntity)) AND
      ((pDoor IS NULL) OR (entity_door_relationship.door = pDoor))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    eventTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from eventTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
