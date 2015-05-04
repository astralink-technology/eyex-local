DROP PROCEDURE IF EXISTS get_card;
CREATE PROCEDURE get_card(
  IN pCardId VARCHAR(256)
  , IN pCardSerial VARCHAR(256)
  , IN pEntity VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS cardTemp AS (
    SELECT
      card._id
      , card.card_serial
      , card.entity
    FROM card
    WHERE (
      ((pCardId IS NULL) OR (card._id = pCardId)) AND
      ((pCardSerial IS NULL) OR (card.card_serial = pCardSerial)) AND
      ((pEntity IS NULL) OR (card.entity = pEntity))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    cardTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from cardTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
