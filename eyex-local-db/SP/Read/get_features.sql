DROP PROCEDURE IF EXISTS get_features;
CREATE PROCEDURE get_features(
  IN pFeaturesId VARCHAR(128)
  , IN pRemoteDoorControl VARCHAR(6)
  , IN pLocalDoorControl VARCHAR(6)
  , IN pVoicemailPassword VARCHAR(256)
  , IN pVoicemailExtension VARCHAR(128)
  , IN pDevice VARCHAR(128)
  , IN pPageSize INTEGER
  , IN pSkipSize INTEGER
)
BEGIN
  DECLARE pTotalRows INTEGER;

  CREATE TEMPORARY TABLE IF NOT EXISTS featuresTemp AS (
    SELECT
      features._id
      , features.remote_door_control
      , features.local_door_control
      , features.voicemail_password
      , features.voicemail_extension
      , features.pickup
      , features.extra1
      , features.extra2
      , features.extra3
      , features.extra4
      , features.device
    FROM features
    WHERE (
      ((pFeaturesId IS NULL) OR (features._id = pFeaturesId)) AND
      ((pRemoteDoorControl IS NULL) OR (features.remote_door_control = pRemoteDoorControl)) AND
      ((pLocalDoorControl IS NULL) OR (features.local_door_control = pLocalDoorControl)) AND
      ((pVoicemailPassword IS NULL) OR (features.voicemail_password = pVoicemailPassword)) AND
      ((pVoicemailExtension IS NULL) OR (features.voicemail_extension = pVoicemailExtension)) AND
      ((pDevice IS NULL) OR (features.device = pDevice))
    )
  );

  SELECT
    COUNT(*)
    INTO
    @pTotalRows
  FROM
    featuresTemp;

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

  PREPARE stmt FROM "SELECT *, ? AS total_rows from featuresTemp LIMIT ? OFFSET ?;";
  EXECUTE stmt USING @pTotalRows, @pageSize, @skipSize;
  DEALLOCATE PREPARE stmt;

END;
