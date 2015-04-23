DROP PROCEDURE IF EXISTS add_door;
CREATE PROCEDURE add_door(
  IN pDoorId VARCHAR(128)
  , IN pDoorName VARCHAR(128)
  , IN pDoorNode INTEGER
)
BEGIN
  INSERT INTO door(
    _id
    , door_name
    , door_node
  ) VALUES (
    pDoorId
    , pDoorName
    , pDoorNode
  );

  SELECT LAST_INSERT_ID();
END;