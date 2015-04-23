DROP PROCEDURE IF EXISTS update_entity;

CREATE PROCEDURE update_entity(
  IN pEntityId VARCHAR(128)
  , IN pAuthenticationString VARCHAR(256)
  , IN pAuthenticationStringLower VARCHAR(256)
  , IN pFirstName VARCHAR(256)
  , IN pLastName VARCHAR(256)
  , IN pName VARCHAR(512)
  , IN pExtension VARCHAR(128)
  , IN pCard VARCHAR(256)
  , IN pPin VARCHAR(16)
)
BEGIN
  /* declare the new variables */
  DECLARE nAuthenticationString VARCHAR(256);
  DECLARE nAuthenticationStringLower VARCHAR(256);
  DECLARE nFirstName VARCHAR(256);
  DECLARE nLastName VARCHAR(256);
  DECLARE nName VARCHAR(512);
  DECLARE nExtension VARCHAR(128);
  DECLARE nCard VARCHAR(256);
  DECLARE nPin VARCHAR(16);

  /* declare the old variables */
  DECLARE oAuthenticationString VARCHAR(256);
  DECLARE oAuthenticationStringLower VARCHAR(256);
  DECLARE oFirstName VARCHAR(256);
  DECLARE oLastName VARCHAR(256);
  DECLARE oName VARCHAR(512);
  DECLARE oExtension VARCHAR(128);
  DECLARE oCard VARCHAR(256);
  DECLARE oPin VARCHAR(16);

  SET autocommit = 0;

  IF pEntityId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oAuthenticationString := authentication_string
        , @oAuthenticationStringLower := authentication_string_lower
        , @oFirstName := first_name
        , @oLastName := last_name
        , @oName := name
        , @oExtension := extension
        , @oCard := card
        , @oPin := pin
    FROM
        entity
    WHERE
        _id = pEntityId;

    /* set the variables with the new inputs */
    IF pAuthenticationString IS NULL THEN
        SET @nAuthenticationString = @oAuthenticationString;
    ELSEIF pAuthenticationString = '' THEN
        SET @nAuthenticationString = NULL;
    ELSE
        SET @nAuthenticationString = pAuthenticationString;
    END IF;
    
    IF pAuthenticationStringLower IS NULL THEN
        SET @nAuthenticationStringLower = @oAuthenticationStringLower;
    ELSEIF pAuthenticationStringLower = '' THEN
        SET @nAuthenticationStringLower = NULL;
    ELSE
        SET @nAuthenticationStringLower = pAuthenticationStringLower;
    END IF;
    
    IF pFirstName IS NULL THEN
        SET @nFirstName = @oFirstName;
    ELSEIF pFirstName = '' THEN
        SET @nFirstName = NULL;
    ELSE
        SET @nFirstName = pFirstName;
    END IF;

    IF pLastName IS NULL THEN
        SET @nLastName = @oLastName;
    ELSEIF pLastName = '' THEN
        SET @nLastName = NULL;
    ELSE
        SET @nLastName = pLastName;
    END IF;
    
    IF pName IS NULL THEN
        SET @nName = @oName;
    ELSEIF pName = '' THEN
        SET @nName = NULL;
    ELSE
        SET @nName = pName;
    END IF;

    IF pExtension IS NULL THEN
        SET @nExtension = @oExtension;
    ELSEIF pExtension = '' THEN
        SET @nExtension = NULL;
    ELSE
        SET @nExtension = pExtension;
    END IF;

    IF pCard IS NULL THEN
        SET @nCard = @oCard;
    ELSEIF pCard = '' THEN
        SET @nCard = NULL;
    ELSE
        SET @nCard = pCard;
    END IF;

    IF pPin IS NULL THEN
        SET @nPin = @oPin;
    ELSEIF pPin = '' THEN
        SET @nPin = NULL;
    ELSE
        SET @nPin = pPin;
    END IF;

    UPDATE entity
    SET
        authentication_string = @nAuthenticationString
        , authentication_string_lower = @nAuthenticationStringLower
        , first_name = @nFirstName
        , last_name = @nLastName
        , name = @nName
        , extension = @nExtension
        , card = @nCard
        , pin = @nPin
    WHERE _id = pEntityId;
    COMMIT;
  END IF;
END;