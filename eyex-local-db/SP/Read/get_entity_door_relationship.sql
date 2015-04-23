DROP PROCEDURE IF EXISTS get_entity_door_relationship;
CREATE PROCEDURE get_entity_door_relationship(
  IN pEntityDoorRelationshipId INTEGER
  , IN pDoor VARCHAR(128)
  , IN pEntity VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS entityDoorRelationshipTemp AS (
    SELECT
      entity_door_relationship._id as entity_door_relationship_id
      , entity_door_relationship.entity
      , entity_door_relationship.door
    FROM entity_door_relationship
    WHERE (
      ((pEntityDoorRelationshipId IS NULL) OR (entity_door_relationship._id = pEntityDoorRelationshipId)) AND
      ((pEntity IS NULL) OR (entity_door_relationship.entity = pEntity)) AND
      ((pDoor IS NULL) OR (entity_door_relationship.door = pDoor))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    entityDoorRelationshipTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from entityDoorRelationshipTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
