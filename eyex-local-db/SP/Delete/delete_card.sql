DROP PROCEDURE IF EXISTS delete_card;
CREATE PROCEDURE delete_card(
  IN pCardId VARCHAR(256)
)
BEGIN
  IF
    pCardId IS NOT NULL
  THEN
    DELETE FROM card WHERE
    ((pCardId IS NULL) OR (card._id = pCardId));
  END IF;
END;