DROP PROCEDURE IF EXISTS update_device;

CREATE PROCEDURE  update_device(
  IN pDeviceId VARCHAR(128)
  , IN pName VARCHAR(128)
  , IN pType VARCHAR(4)
  , IN pType2 VARCHAR(4)
  , IN pIntPrefix INTEGER
  , IN pDoor VARCHAR(128)
)
BEGIN
  /* declare the new variables */
  DECLARE nName VARCHAR(128);
  DECLARE nType VARCHAR(4);
  DECLARE nType2 VARCHAR(4);
  DECLARE nIntPrefix INTEGER;
  DECLARE nDoor VARCHAR(128);

  /* declare the old variables */
  DECLARE oName VARCHAR(128);
  DECLARE oType VARCHAR(4);
  DECLARE oType2 VARCHAR(4);
  DECLARE oIntPrefix INTEGER;
  DECLARE oDoor VARCHAR(128);

  SET autocommit = 0;

  IF pDeviceId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oName := name
        , @oType := type
        , @oType2 := type2
        , @oIntPrefix := int_prefix
        , @oDoor := door
    FROM
        device
    WHERE
        _id = pDeviceId;

    /* set the variables with the new inputs */
    IF pName IS NULL THEN
        SET @nName = @oName;
    ELSEIF pName = '' THEN
        SET @nName = NULL;
    ELSE
        SET @nName = pName;
    END IF;
    
    IF pType IS NULL THEN
        SET @nType = @oType;
    ELSEIF pType = '' THEN
        SET @nType = NULL;
    ELSE
        SET @nType = pType;
    END IF;
    
    IF pType2 IS NULL THEN
        SET @nType2 = @oType2;
    ELSEIF pType2 = '' THEN
        SET @nType2 = NULL;
    ELSE
        SET @nType2 = pType2;
    END IF;
    
    IF pIntPrefix IS NULL THEN
        SET @nIntPrefix = @oIntPrefix;
    ELSEIF pIntPrefix = '' THEN
        SET @nIntPrefix = NULL;
    ELSE
        SET @nIntPrefix = pIntPrefix;
    END IF;
    
    IF pDoor IS NULL THEN
        SET @nDoor = @oDoor;
    ELSEIF pDoor = '' THEN
        SET @nDoor = NULL;
    ELSE
        SET @nDoor = pDoor;
    END IF;

    UPDATE device
    SET
        name = @nName
        , type = @nType
        , type2 = @nType2
        , int_prefix = @nIntPrefix
        , door = @nDoor
    WHERE _id = pDeviceId;
    COMMIT;
  END IF;
END;