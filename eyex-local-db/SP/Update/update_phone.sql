DROP PROCEDURE IF EXISTS update_phone;
CREATE PROCEDURE update_phone(
  IN pPhoneId VARCHAR(128)
  , IN pPhoneDigits VARCHAR(32)
  , IN pCountryCode VARCHAR(6)
  , IN pCode VARCHAR(6)
  , IN pType VARCHAR(6)
  , IN pEntity VARCHAR(128)
)
BEGIN
  /* declare the new variables */
  DECLARE nPhoneDigits VARCHAR(32);
  DECLARE nCountryCode VARCHAR(6);
  DECLARE nCode VARCHAR(6);
  DECLARE nType VARCHAR(6);
  DECLARE nEntity VARCHAR(128);

  /* declare the old variables */
  DECLARE oPhoneDigits VARCHAR(32);
  DECLARE oCountryCode VARCHAR(6);
  DECLARE oCode VARCHAR(6);
  DECLARE oType VARCHAR(6);
  DECLARE oEntity VARCHAR(128);

  SET autocommit = 0;

  IF pPhoneId IS NOT NULL THEN
    /* get the old variables */
    SELECT
      @oPhoneDigits = phone_digits
      , @oCountryCode = country_code
      , @oCode = code
      , @oType = type
      , @oEntity = entity
    FROM
        phone
    WHERE
        _id = pPhoneId;

    /* set the variables with the new inputs */
    IF pPhoneDigits IS NULL THEN
        SET @nPhoneDigits = @oPhoneDigits;
    ELSEIF pPhoneDigits = '' THEN
        SET @nPhoneDigits = NULL;
    ELSE
        SET @nPhoneDigits = pPhoneDigits;
    END IF;
    
    IF pCountryCode IS NULL THEN
        SET @nCountryCode = @oCountryCode;
    ELSEIF pCountryCode = '' THEN
        SET @nCountryCode = NULL;
    ELSE
        SET @nCountryCode = pCountryCode;
    END IF;
    
    IF pCode IS NULL THEN
        SET @nCode = @oCode;
    ELSEIF pCode = '' THEN
        SET @nCode = NULL;
    ELSE
        SET @nCode = pCode;
    END IF;
    
    IF pType IS NULL THEN
        SET @nType = @oType;
    ELSEIF pType = '' THEN
        SET @nType = NULL;
    ELSE
        SET @nType = pType;
    END IF;
    
    IF pEntity IS NULL THEN
        SET @nEntity = @oEntity;
    ELSEIF pEntity = '' THEN
        SET @nEntity = NULL;
    ELSE
        SET @nEntity = pEntity;
    END IF;

    UPDATE phone
    SET
        phone_digits = @nPhoneDigits
        , country_code = @nCountryCode
        , code = @nCode
        , type = @nType
        , entity = @nEntity
    WHERE _id = pPhoneId;
    COMMIT;
  END IF;
END;