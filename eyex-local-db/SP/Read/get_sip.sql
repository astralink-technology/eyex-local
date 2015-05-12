DROP PROCEDURE IF EXISTS get_sip;
CREATE PROCEDURE get_sip(
  IN pSipId VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS sipTemp AS (
    SELECT
      sip._id
      , sip.username
      , sip.password
      , sip.host
    FROM sip
    WHERE (
      ((pSipId IS NULL) OR (sip._id = pSipId))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    sipTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from sipTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
