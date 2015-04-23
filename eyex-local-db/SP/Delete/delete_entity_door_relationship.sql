DROP PROCEDURE IF EXISTS delete_entity_door_relationship;
CREATE PROCEDURE delete_entity_door_relationship(
  IN pEntityDoorRelationshipId INTEGER
)
BEGIN
  IF
    pEntityDoorRelationshipId IS NOT NULL
  THEN
    DELETE FROM entity_door_relationship WHERE
    ((pEntityDoorRelationshipId IS NULL) OR (entity_door_relationship._id = pEntityDoorRelationshipId));
  END IF;
END;