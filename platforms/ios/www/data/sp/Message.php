<?php
    require('../db-connect.php');
    
    $action = ($_GET['action'] == undefined) ? "": mysql_real_escape_string($_GET['action']);
    
    if ($action == 'getMessageGroups') {
        $_profileId = ($_GET['_profileId'] == undefined) ? "": mysql_real_escape_string($_GET['_profileId']);
        $groupType = ($_GET['groupType'] == undefined) ? "": mysql_real_escape_string($_GET['groupType']);
        
        $result = mysql_query("CALL getMessageGroups('$_profileId', '$groupType');");
    }
    elseif ($action == 'getAllUserMessagesSummaryForUpdate') {
        $_profileId = ($_GET['_profileId'] == undefined) ? "": mysql_real_escape_string($_GET['_profileId']);
        
        $result = mysql_query("CALL getAllUserMessagesSummaryForUpdate('$_profileId');");
    }
    elseif ($action == 'getUnreadUserMessagesSummaryForUpdate') {
        $_profileId = ($_GET['_profileId'] == undefined) ? "": mysql_real_escape_string($_GET['_profileId']);
        
        $result = mysql_query("CALL getUnreadUserMessagesSummaryForUpdate('$_profileId');");
    }
    elseif ($action == 'getMessageGroupSummary') {
        $_usersProfileId = ($_GET['_usersProfileId'] == undefined) ? "": mysql_real_escape_string($_GET['_usersProfileId']);
        $_groupId = ($_GET['_groupId'] == undefined) ? "": mysql_real_escape_string($_GET['_groupId']);
        
        $result = mysql_query("CALL getMessageGroupSummary('$_usersProfileId','$_groupId');");
    }
    elseif ($action == 'getMessageGroup') {
        $_groupId = ($_GET['_groupId'] == undefined) ? "": mysql_real_escape_string($_GET['_groupId']);
        $_usersProfileId = ($_GET['_usersProfileId'] == undefined) ? "": mysql_real_escape_string($_GET['_usersProfileId']);
        $currentMessageIndex = ($_GET['currentMessageIndex'] == undefined) ? "": mysql_real_escape_string($_GET['currentMessageIndex']);
        
        $result = mysql_query("CALL getMessageGroup('$_groupId', '$_usersProfileId', '$currentMessageIndex');");
    }
    elseif ($action == 'getMessageRecipientProfileIds') {
        $_messageGroupId = ($_GET['_messageGroupId'] == undefined) ? "": mysql_real_escape_string($_GET['_messageGroupId']);
        $_senderProfileId = ($_GET['_senderProfileId'] == undefined) ? "": mysql_real_escape_string($_GET['_senderProfileId']);
        
        $result = mysql_query("CALL getMessageRecipientProfileIds('$_messageGroupId', '$_senderProfileId');");
    }
    elseif ($action == 'getMessageDetails') {
        $_id = ($_GET['_id'] == undefined) ? "": mysql_real_escape_string($_GET['_id']);
        
        $result = mysql_query("CALL getMessageDetails($_id);");
    }
    elseif ($action == 'addMessage') {
        $_groupId = ($_GET['_groupId'] == undefined) ? "": mysql_real_escape_string($_GET['_groupId']);
        $groupType = ($_GET['groupType'] == undefined) ? "": mysql_real_escape_string($_GET['groupType']);
        $_senderId = ($_GET['_senderId'] == undefined) ? "": mysql_real_escape_string($_GET['_senderId']);
        $messageText = ($_GET['messageText'] == undefined) ? "": mysql_real_escape_string($_GET['messageText']);
        $_relListingId = ($_GET['_relListingId'] == undefined) ? NULL: mysql_real_escape_string($_GET['_relListingId']);
        $relListingType = ($_GET['relListingType'] == undefined) ? NULL: mysql_real_escape_string($_GET['relListingType']);
        $profileIds = $_GET['_profileIds'];
        $profileIdString = implode(', ', $profileIds);
        
        if ($_groupId == "null") {
            $_groupId = mysql_query("INSERT INTO MessageGroup (name, dateTimeCreated, type) VALUES ('', NOW(), '$groupType');");
            $_groupId = mysql_insert_id();
            $resultPrepare = mysql_query("INSERT INTO MessageGroupParticipant (_messageGroupId, _participantProfileId)
                    SELECT '$_groupId', _id FROM Profile WHERE _id IN ($profileIdString)");
        }

        $addMessage = mysql_query("INSERT INTO Message (_messageGroupId, _messageSenderProfileId, text, datetimeSent, _relatedItemId, relatedItemType) VALUES ('$_groupId', '$_senderId', '$messageText', NOW(), $_relListingId, '$relListingType');");
        $_messageId = mysql_insert_id();
        
        $finalResultPrepare = mysql_query("INSERT INTO MessageReadReceipt (_messageId, _messageReadByProfileId) VALUES ($_messageId, $_senderId)");
        
        echo json_encode($_groupId);
    }
    elseif ($action == 'checkIfMessageGroupExists') {
        $profileIds = $_GET['_profiles'];
        $profileIdString = implode(', ', $profileIds);
        $profileCount = count($profileIds);
        
        $result = mysql_query("CALL checkIfMessageGroupExists('$profileIdString', $profileCount)");
        //print($result);
    }
    
    //print(json_encode($_usersId));
    if ($action != 'addMessage') {
        while($row = mysql_fetch_assoc($result))
            $output[] = $row;
            
            header('Content-Type: application/json');
            echo json_encode($output);
    }
mysql_close();
?>
