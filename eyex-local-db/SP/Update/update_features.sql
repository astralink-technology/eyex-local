DROP PROCEDURE IF EXISTS update_features;

CREATE PROCEDURE update_features(
  IN pFeaturesId VARCHAR(128)
  , IN pRemoteControlDoor VARCHAR(8)
  , IN pLocalControlDoor VARCHAR(8)
  , IN pVoicemailPassword VARCHAR(256)
  , IN pVoicemailExtension VARCHAR(128)
  , IN pPickup VARCHAR(32)
  , IN pExtra1 VARCHAR(32)
  , IN pExtra2 VARCHAR(32)
  , IN pExtra3 VARCHAR(32)
  , IN pExtra4 VARCHAR(32)
  , IN pDevice VARCHAR(128)
)
BEGIN
  /* declare the new variables */
  DECLARE nRemoteControlDoor VARCHAR(8);
  DECLARE nLocalControlDoor VARCHAR(8);
  DECLARE nVoicemailPassword VARCHAR(256);
  DECLARE nVoicemailExtension VARCHAR(128);
  DECLARE nPickup VARCHAR(32);
  DECLARE nExtra1 VARCHAR(32);
  DECLARE nExtra2 VARCHAR(32);
  DECLARE nExtra3 VARCHAR(32);
  DECLARE nExtra4 VARCHAR(32);
  DECLARE nDevice VARCHAR(128);

  /* declare the old variables */
  DECLARE oRemoteControlDoor VARCHAR(8);
  DECLARE oLocalControlDoor VARCHAR(8);
  DECLARE oVoicemailPassword VARCHAR(256);
  DECLARE oVoicemailExtension VARCHAR(128);
  DECLARE oPickup VARCHAR(32);
  DECLARE oExtra1 VARCHAR(32);
  DECLARE oExtra2 VARCHAR(32);
  DECLARE oExtra3 VARCHAR(32);
  DECLARE oExtra4 VARCHAR(32);
  DECLARE oDevice VARCHAR(128);

  SET autocommit = 0;

  IF pFeaturesId IS NOT NULL THEN
    /* get the old variables */
    SELECT
        @oRemoteControlDoor := remote_control_door
        , @oLocalControlDoor := local_control_door
        , @oVoicemailPassword := voicemail_password
        , @oVoicemailExtension := voicemail_extension
        , @oPickup := pickup
        , @oExtra1 := extra1
        , @oExtra2 := extra2
        , @oExtra3 := extra3
        , @oExtra4 := extra4
        , @oDevice := device
    FROM
        features
    WHERE
        _id = pFeaturesId;

    /* set the variables with the new inputs */
    IF pRemoteControlDoor IS NULL THEN
        SET @nRemoteControlDoor = @oRemoteControlDoor;
    ELSEIF pRemoteControlDoor = '' THEN
        SET @nRemoteControlDoor = NULL;
    ELSE
        SET @nRemoteControlDoor = pRemoteControlDoor;
    END IF;
    
    IF pLocalControlDoor IS NULL THEN
        SET @nLocalControlDoor = @oLocalControlDoor;
    ELSEIF pLocalControlDoor = '' THEN
        SET @nLocalControlDoor = NULL;
    ELSE
        SET @nLocalControlDoor = pLocalControlDoor;
    END IF;
    
    IF pVoicemailPassword IS NULL THEN
        SET @nVoicemailPassword = @oVoicemailPassword;
    ELSEIF pVoicemailPassword = '' THEN
        SET @nVoicemailPassword = NULL;
    ELSE
        SET @nVoicemailPassword = pVoicemailPassword;
    END IF;
    
    IF pVoicemailExtension IS NULL THEN
        SET @nVoicemailExtension = @oVoicemailExtension;
    ELSEIF pVoicemailExtension = '' THEN
        SET @nVoicemailExtension = NULL;
    ELSE
        SET @nVoicemailExtension = pVoicemailExtension;
    END IF;
    
    IF pPickup IS NULL THEN
        SET @nPickup = @oPickup;
    ELSEIF pPickup = '' THEN
        SET @nPickup = NULL;
    ELSE
        SET @nPickup = pPickup;
    END IF;
    
    IF pExtra1 IS NULL THEN
        SET @nExtra1 = @oExtra1;
    ELSEIF pExtra1 = '' THEN
        SET @nExtra1 = NULL;
    ELSE
        SET @nExtra1 = pExtra1;
    END IF;
    
    IF pExtra2 IS NULL THEN
        SET @nExtra2 = @oExtra2;
    ELSEIF pExtra2 = '' THEN
        SET @nExtra2 = NULL;
    ELSE
        SET @nExtra2 = pExtra2;
    END IF;
    
    IF pExtra3 IS NULL THEN
        SET @nExtra3 = @oExtra3;
    ELSEIF pExtra3 = '' THEN
        SET @nExtra3 = NULL;
    ELSE
        SET @nExtra3 = pExtra3;
    END IF;
    
    IF pExtra4 IS NULL THEN
        SET @nExtra4 = @oExtra4;
    ELSEIF pExtra4 = '' THEN
        SET @nExtra4 = NULL;
    ELSE
        SET @nExtra4 = pExtra4;
    END IF;
    
    IF pDevice IS NULL THEN
        SET @nDevice = @oDevice;
    ELSEIF pDevice = '' THEN
        SET @nDevice = NULL;
    ELSE
        SET @nDevice = pDevice;
    END IF;

    UPDATE features
    SET
        remote_control_door = @nRemoteControlDoor
        , local_control_door = @nLocalControlDoor
        , voicemail_password = @nVoicemailPassword
        , voicemail_extension = @nVoicemail_extension
        , pickup = @nPickup
        , extra1 = @nExtra1
        , extra2 = @nExtra2
        , extra3 = @nExtra3
        , extra4 = @nExtra4
        , device = @nDevice
    WHERE _id = pFeaturesId;
    COMMIT;
  END IF;
END;