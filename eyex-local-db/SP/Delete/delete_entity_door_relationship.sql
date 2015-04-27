DROP PROCEDURE IF EXISTS delete_entity_door_relationship;
CREATE PROCEDURE delete_entity_door_relationship(
  IN pEntityDoorRelationshipId INTEGER
  , IN pEntityId VARCHAR(128)
  , IN pDoorId VARCHAR(128)
)
BEGIN
  IF
    pEntityDoorRelationshipId IS NOT NULL OR
    pEntityId IS NOT NULL OR
    pDoorId IS NOT NULL
  THEN
    DELETE FROM entity_door_relationship WHERE
    ((pEntityDoorRelationshipId IS NULL) OR (entity_door_relationship._id = pEntityDoorRelationshipId)) AND
    ((pEntityId IS NULL) OR (entity_door_relationship.entity = pEntityId)) AND
    ((pDoorId IS NULL) OR (entity_door_relationship.door = pDoorId));
  END IF;
END;