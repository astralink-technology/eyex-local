<?php
/*
 * Copyright Chilli Panda
 * Created on 05-03-2013
 * Created by Shi Wei Eamon
 */

/*
 * A helper on getting user's registered products
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/Helpers/user_helper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Model/Dao/productRegistrationDao.php';

class cp_productRegistration_helper{
    public function getUserRegisteredProductName(){
        $userHelper = new cp_user_helper();
        $productRegistrationDb = new cp_product_registration_dao();
        $entityId = $userHelper->getCurrentEntityId();
        $productName = "";
        if ($entityId != null){
            $productRegistrationRes = $productRegistrationDb->getProductEntityRegistrationDetails(
                null
                , null
                , null
                , null
                , $entityId
            );
            if ($productRegistrationRes['Error'] == false && $productRegistrationRes['TotalRowsAvailable'] > 0){
                $productName = $productRegistrationRes['Data'][0]->productName;
            }
        }
        return $productName;
    }

    public function getUserRegisteredProductId(){
        $userHelper = new cp_user_helper();
        $productRegistrationDb = new cp_product_registration_dao();
        $entityId = $userHelper->getCurrentEntityId();
        $productName = "";
        if ($entityId != null){
            $productRegistrationRes = $productRegistrationDb->getProductEntityRegistrationDetails(
                null
                , null
                , null
                , null
                , $entityId
            );
            if ($productRegistrationRes['Error'] == false && $productRegistrationRes['TotalRowsAvailable'] > 0){
                $productId = $productRegistrationRes['Data'][0]->productId;
            }
        }
        return $productId;
    }
}
?>
