DROP PROCEDURE IF EXISTS update_device_door_relationship;

CREATE PROCEDURE  update_device_door_relationship(
  IN pDeviceDoorRelationship INTEGER
  , IN pDevice VARCHAR(128)
  , IN pDoor VARCHAR(128)
)
BEGIN
  /* declare the new variables */
  DECLARE nDevice VARCHAR(128);
  DECLARE nDoor VARCHAR(128);

  /* declare the old variables */
  DECLARE oDevice VARCHAR(128);
  DECLARE oDoor VARCHAR(128);

  SET autocommit = 0;

  IF pDeviceDoorRelationshipId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oDevice := device
        , @oDoor := door
    FROM
        device_door_relationship
    WHERE
        _id = pDeviceDoorRelationshipId;

    /* set the variables with the new inputs */
    IF pDevice IS NULL THEN
        SET @nDevice = @oDevice;
    ELSEIF pDevice = '' THEN
        SET @nDevice = NULL;
    ELSE
        SET @nDevice = pDevice;
    END IF;
    
    IF pDoor IS NULL THEN
        SET @nDoor = @oDoor;
    ELSEIF pDoor = '' THEN
        SET @nDoor = NULL;
    ELSE
        SET @nDoor = pDoor;
    END IF;

    UPDATE device_door_relationship
    SET
        device = @nDevice
        , door = @nDoor
    WHERE _id = pDeviceDoorRelationshipId;
    COMMIT;
  END IF;
END;