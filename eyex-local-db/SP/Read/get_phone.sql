DROP PROCEDURE IF EXISTS get_phone;
CREATE PROCEDURE get_phone(
  IN pPhoneId VARCHAR(128)
  , IN pCountryCode VARCHAR(6)
  , IN pCode VARCHAR(6)
  , IN pType VARCHAR(6)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS phoneTemp AS (
    SELECT
      phone._id
      , phone.phone_digits
      , phone.country_code
      , phone.code
      , phone.type
      , phone.entity
    FROM phone
    WHERE (
      ((pPhoneId IS NULL) OR (phone._id = pPhoneId)) AND
      ((pCountryCode IS NULL) OR (phone.country_code = pCountryCode)) AND
      ((pCode IS NULL) OR (phone.code = pCode)) AND
      ((pType IS NULL) OR (phone.type = pType))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    phoneTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from phoneTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
