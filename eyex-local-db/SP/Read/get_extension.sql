DROP PROCEDURE IF EXISTS get_extension;
CREATE PROCEDURE get_extension(
  IN pExtensionId VARCHAR(128)
  , IN pNumber VARCHAR(6)
  , IN pEntity VARCHAR(6)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS extensionTemp AS (
    SELECT
      extension._id as extension_id
      , extension.number
      , extension.entity
    FROM event
    WHERE (
      ((pExtensionId IS NULL) OR (extension._id = pExtensionId)) AND
      ((pNumber IS NULL) OR (extension.entity_type_id = pNumber)) AND
      ((pEntity IS NULL) OR (extension.entity = pEntity))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    extensionTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from extensionTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
