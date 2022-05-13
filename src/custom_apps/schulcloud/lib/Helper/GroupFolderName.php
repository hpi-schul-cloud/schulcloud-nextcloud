<?php
namespace OCA\Schulcloud\Helper;

class GroupFolderName {

    /**
     * Creates a formatted string for the group folder names from the gid and display name
     * @param $groupId
     * @param $groupName
     * @return string
     */
    public static function make($groupId, $groupName) {
        $id = $groupId;

        if (preg_match("/(?<=-)(.*)$/", $groupId, $matches)) {
            $id = $matches[0];
        }
        
        return "$groupName ($id)";
    }
}