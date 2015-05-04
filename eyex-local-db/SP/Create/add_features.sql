DROP PROCEDURE IF EXISTS add_features;
CREATE PROCEDURE add_features(
  pFeaturesId VARCHAR(128)
  , pRemoteDoorControl VARCHAR(8)
  , pLocalDoorControl VARCHAR(8)
  , pVoicemailPassword VARCHAR(256)
  , pVoicemailExtension VARCHAR(128)
  , pPickup VARCHAR(32)
  , pExtra1 VARCHAR(32)
  , pExtra2 VARCHAR(32)
  , pExtra3 VARCHAR(32)
  , pExtra4 VARCHAR(32)
  , pDevice VARCHAR(128)
)
BEGIN
  INSERT INTO features (
    _id
    , remote_door_control
    , local_door_control
    , voicemail_password
    , voicemail_extension
    , pickup
    , extra1
    , extra2
    , extra3
    , extra4
    , device
  ) VALUES (
    pFeaturesId
    , pRemoteDoorControl
    , pLocalDoorControl
    , pVoicemailPassword
    , pVoicemailExtension
    , pPickup
    , pExtra1
    , pExtra2
    , pExtra3
    , pExtra4
    , pDevice
  );
  SELECT LAST_INSERT_ID();
END;