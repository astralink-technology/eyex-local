DROP PROCEDURE IF EXISTS get_entity;
CREATE PROCEDURE get_entity(
  IN pEntityId VARCHAR(128)
  , IN pPin VARCHAR(16)
  , IN pCard VARCHAR(256)
  , IN pExtension VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)

BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS entityTemp AS (
    SELECT
      entity._id
      , entity.authentication_string
      , entity.authentication_string_lower
      , entity.first_name
      , entity.last_name
      , entity.name
      , entity.extension
      , entity.card
      , entity.pin
    FROM entity
    WHERE (
      ((pEntityId IS NULL) OR (entity._id = pEntityId)) AND
      ((pExtension IS NULL) OR (entity.extension = pExtension)) AND
      ((pCard IS NULL) OR (entity.card = pCard)) AND
      ((pPin IS NULL) OR (entity.pin = pPin))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    entityTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from entityTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
