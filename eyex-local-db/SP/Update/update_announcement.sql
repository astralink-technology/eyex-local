DROP PROCEDURE IF EXISTS update_announcement;

CREATE PROCEDURE  update_announcement(
  IN pMessage TEXT
)
BEGIN
  /* declare the new variables */
  DECLARE nMessage TEXT;

  /* declare the old variables */
  DECLARE oMessage TEXT;

  SET autocommit = 0;

  /* get the old variables */
  SELECT
      @oMessage := message
  FROM
      announcement;

  /* set the variables with the new inputs */
  IF pMessage IS NULL THEN
      SET @nMessage = @oMessage;
  ELSEIF pMessage = '' THEN
      SET @nMessage = NULL;
  ELSE
      SET @nMessage = pMessage;
  END IF;

  UPDATE announcement
  SET
      message = @nMessage;
  COMMIT;
END;