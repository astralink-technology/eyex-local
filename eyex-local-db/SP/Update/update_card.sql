DROP PROCEDURE IF EXISTS update_card;

CREATE PROCEDURE  update_card(
  IN pCardId VARCHAR(256)
  , IN pCardSerial VARCHAR(256)
  , IN pEntity VARCHAR(128)
)
BEGIN
  /* declare the new variables */
  DECLARE nCardId VARCHAR(256);
  DECLARE nCardSerial VARCHAR(256);
  DECLARE nEntity VARCHAR(128);

  /* declare the old variables */
  DECLARE oCardId VARCHAR(256);
  DECLARE oCardSerial VARCHAR(256);
  DECLARE oEntity VARCHAR(128);

  SET autocommit = 0;

  IF pCardId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oCardSerial := card_serial
        , @oEntity := entity
    FROM
        card
    WHERE
        _id = pCardId;

    /* set the variables with the new inputs */
    IF pCardSerial IS NULL THEN
        SET @nCardSerial = @oCardSerial;
    ELSEIF pCardSerial = '' THEN
        SET @nCardSerial = NULL;
    ELSE
        SET @nCardSerial = pCardSerial;
    END IF;
    
    IF pEntity IS NULL THEN
        SET @nEntity = @oEntity;
    ELSEIF pEntity = '' THEN
        SET @nEntity = NULL;
    ELSE
        SET @nEntity = pEntity;
    END IF;

    UPDATE card
    SET
        card_serial = @nCardSerial
        , entity = @nEntity
    WHERE _id = pCardId;
    COMMIT;
  END IF;
END;