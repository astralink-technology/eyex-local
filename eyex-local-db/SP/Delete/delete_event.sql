DROP PROCEDURE IF EXISTS delete_entity;
CREATE PROCEDURE delete_entity(
  IN pEventId VARCHAR(128)
)
BEGIN
  IF
    pEventId IS NOT NULL
  THEN
    DELETE FROM event WHERE
    ((pEventId IS NULL) OR (event._id = pEventId));
  END IF;
END;