DROP PROCEDURE IF EXISTS delete_door;
CREATE PROCEDURE delete_door(
  IN pDoorId VARCHAR(128)
)
BEGIN
  IF
    pDoorId IS NOT NULL
  THEN
    DELETE FROM door WHERE
    ((pDoorId IS NULL) OR (door._id = pDoorId));
  END IF;
END;