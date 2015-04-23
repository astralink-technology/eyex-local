DROP PROCEDURE IF EXISTS update_door;

CREATE PROCEDURE  update_door(
  IN pDoorId VARCHAR(128)
  , IN pDoorName VARCHAR(128)
  , IN pDoorNode INTEGER
)
BEGIN
  /* declare the new variables */
  DECLARE nDoorName VARCHAR(128);
  DECLARE nDoorNode INTEGER;

  /* declare the old variables */
  DECLARE oDoorName VARCHAR(128);
  DECLARE oDoorNode INTEGER;

  SET autocommit = 0;

  IF pDoorId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oDoorName := door_name
        , @oDoorNode := door_node
    FROM
        door
    WHERE
        _id = pDoorId;

    /* set the variables with the new inputs */
    IF pDoorName IS NULL THEN
        SET @nDoorName = @oDoorName;
    ELSEIF pDoorName = '' THEN
        SET @nDoorName = NULL;
    ELSE
        SET @nDoorName = pDoorName;
    END IF;
    
    IF pDoorNode IS NULL THEN
        SET @nDoorNode = @oDoorNode;
    ELSEIF pDoorNode = '' THEN
        SET @nDoorNode = NULL;
    ELSE
        SET @nDoorNode = pDoorNode;
    END IF;

    UPDATE door
    SET
        door_name = @nDoorName
        , door_node = @nDoorNode
    WHERE _id = pDoorId;
    COMMIT;
  END IF;
END;