DROP PROCEDURE IF EXISTS get_event;
CREATE PROCEDURE get_event(
  IN pEventId INTEGER
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
      event._id
      , event.event_type_id
      , event.access_method
      , event.create_date
      , event.door
      , event.device
      , event.entity
      , event.door_name
    FROM event
    WHERE (
      ((pEventId IS NULL) OR (event._id = pEventId)) AND
      ((pEventTypeId IS NULL) OR (event.event_type_id = pEventTypeId)) AND
      ((pAccessMethod IS NULL) OR (event.access_method = pAccessMethod)) AND
      ((pDoor IS NULL) OR (event.door = pDoor)) AND
      ((pDevice IS NULL) OR (event.device = pDevice)) AND
      ((pEntity IS NULL) OR (event.entity = pEntity))
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