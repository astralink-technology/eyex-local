DROP PROCEDURE IF EXISTS update_entity_door_relationship;

CREATE PROCEDURE update_entity_door_relationship(
  IN pEntityDoorRelationshipId INTEGER
  , IN pDoor VARCHAR(128)
  , IN pEntity VARCHAR(128)
)
BEGIN
  /* declare the new variables */
  DECLARE nDoor VARCHAR(128);
  DECLARE nEntity VARCHAR(128);

  /* declare the old variables */
  DECLARE oDoor VARCHAR(128);
  DECLARE oEntity VARCHAR(128);

  SET autocommit = 0;

  IF pEntityDoorRelationshipId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oDoor := door
        , @oEntity := entity
    FROM
        entity_door_relationship
    WHERE
        _id = pEntityDoorRelationshipId;

    /* set the variables with the new inputs */
    IF pDoor IS NULL THEN
        SET @nDoor = @oDoor;
    ELSEIF pDoor = '' THEN
        SET @nDoor = NULL;
    ELSE
        SET @nDoor = pDoor;
    END IF;
    
    IF pEntity IS NULL THEN
        SET @nEntity = @oEntity;
    ELSEIF pEntity = '' THEN
        SET @nEntity = NULL;
    ELSE
        SET @nEntity = pEntity;
    END IF;

    UPDATE entity_door_relationship
    SET
        entity = @nEntity
        , door = @nDoor
    WHERE _id = pEntityDoorRelationshipId;
    COMMIT;
  END IF;
END;