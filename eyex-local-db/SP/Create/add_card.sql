DROP PROCEDURE IF EXISTS add_card;
CREATE PROCEDURE add_card(
  IN pCardId VARCHAR(256)
  , IN pCardSerial VARCHAR(256)
  , IN pEntity VARCHAR(128)
)
BEGIN
  INSERT INTO card(
    _id
    , card_serial
    , entity
  ) VALUES (
    pCardId
    , pCardSerial
    , pEntity
  );

  SELECT LAST_INSERT_ID();
END;