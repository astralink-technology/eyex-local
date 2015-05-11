DROP PROCEDURE IF EXISTS update_sip;

CREATE PROCEDURE  update_sip(
  IN pSipId VARCHAR(128)
  , IN pUsername VARCHAR(256)
  , IN pPassword VARCHAR(64)
  , IN pHost VARCHAR(256)
)
BEGIN
  /* declare the new variables */
  DECLARE oUsername VARCHAR(256);
  DECLARE oPassword VARCHAR(64);
  DECLARE oHost VARCHAR(256);

  /* declare the old variables */
  DECLARE nUsername VARCHAR(256);
  DECLARE nPassword VARCHAR(64);
  DECLARE nHost VARCHAR(256);

  SET autocommit = 0;

  IF pSipId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oUsername := username
        , @oPassword := password
        , @oHost := host
    FROM
        sip
    WHERE
        _id = pSipId;

    /* set the variables with the new inputs */
    IF pUsername IS NULL THEN
        SET @nUsername = @oUsername;
    ELSEIF pUsername = '' THEN
        SET @nUsername = NULL;
    ELSE
        SET @nUsername = pUsername;
    END IF;
    
    IF pPassword IS NULL THEN
        SET @nPassword = @oPassword;
    ELSEIF pPassword = '' THEN
        SET @nPassword = NULL;
    ELSE
        SET @nPassword = pPassword;
    END IF;
    
    IF pHost IS NULL THEN
        SET @nHost = @oHost;
    ELSEIF pHost = '' THEN
        SET @nHost = NULL;
    ELSE
        SET @nHost = pHost;
    END IF;

    UPDATE sip
    SET
        username = @nUsername
        , password = @nPassword
        , host = @nHost
    WHERE _id = pSipId;
    COMMIT;
  END IF;
END;