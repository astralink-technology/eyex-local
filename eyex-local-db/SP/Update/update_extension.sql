DROP PROCEDURE IF EXISTS update_extension;

CREATE PROCEDURE update_extension(
  IN pExtensionId VARCHAR(128)
  , IN pNumber INTEGER
  , IN pExtensionPassword VARCHAR(256)
  , IN pEntity VARCHAR(128)
)
BEGIN
  /* declare the new variables */
  DECLARE nNumber INTEGER;
  DECLARE nExtensionPassword VARCHAR(256);
  DECLARE nEntity VARCHAR(128);

  /* declare the old variables */
  DECLARE oNumber INTEGER;
  DECLARE oExtensionPassword VARCHAR(256);
  DECLARE oEntity VARCHAR(128);

  SET autocommit = 0;

  IF pExtensionId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oNumber := number
        , @oExtensionPassword := extension_password
        , @oEntity := entity
    FROM
        extension
    WHERE
        _id = pExtensionId;

    /* set the variables with the new inputs */
    IF pNumber IS NULL THEN
        SET @nNumber = @oNumber;
    ELSEIF pNumber = '' THEN
        SET @nNumber = NULL;
    ELSE
        SET @nNumber = pNumber;
    END IF;
    
    IF pExtensionPassword IS NULL THEN
        SET @nExtensionPassword = @oExtensionPassword;
    ELSEIF pExtensionPassword = '' THEN
        SET @nExtensionPassword = NULL;
    ELSE
        SET @nExtensionPassword = pExtensionPassword;
    END IF;
    
    IF pEntity IS NULL THEN
        SET @nEntity = @oEntity;
    ELSEIF pEntity = '' THEN
        SET @nEntity = NULL;
    ELSE
        SET @nEntity = pEntity;
    END IF;

    UPDATE extension
    SET
        number = @nNumber
        , extension_password = @nExtensionPassword
        , entity = @nEntity
    WHERE _id = pExtensionId;
    COMMIT;
  END IF;
END;