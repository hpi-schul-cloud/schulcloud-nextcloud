<?php
namespace OCA\Schulcloud\Helper;

class GroupFolderName {

    public static function make($groupId, $groupName) {
        $id = $groupId;

        if (preg_match("/(?<=-)(.*)$/", $groupId, $matches)) {
            $id = $matches[0];
        }
        
        return "$groupName ($id)";
    }
}