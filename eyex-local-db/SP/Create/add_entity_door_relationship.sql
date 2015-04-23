DROP PROCEDURE IF EXISTS add_entity_door_relationship;
CREATE PROCEDURE add_entity_door_relationship(
  pEntity VARCHAR(128)
  , pDoor VARCHAR(128)
)
BEGIN
  INSERT INTO entity_door_relationship(
    entity
    , door
  ) VALUES (
    pEntity
    , pDoor
  );

  SELECT LAST_INSERT_ID();
END;