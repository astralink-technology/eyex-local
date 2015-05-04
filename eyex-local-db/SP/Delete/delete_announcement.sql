DROP PROCEDURE IF EXISTS delete_announcement;
CREATE PROCEDURE delete_announcement()
BEGIN
  DELETE FROM announcement;
END;