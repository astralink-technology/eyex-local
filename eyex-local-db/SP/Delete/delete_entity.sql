DROP PROCEDURE IF EXISTS delete_entity;
CREATE PROCEDURE delete_entity(
  IN pEntityId VARCHAR(128)
)
BEGIN
  IF
    pEntityId IS NOT NULL
  THEN
    DELETE FROM entity WHERE
    ((pEntityId IS NULL) OR (entity._id = pEntityId));
  END IF;
END;