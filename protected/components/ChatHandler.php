<?php

class ChatHandler extends CComponent implements IYiiChat {

    /**
     * Get user identity
     * @return string
     */
    protected function getIdentityName() {
        return substr(!UserWeb::instance()->isGuest ? UserWeb::instance()->user()->username : 'Guest', 0, 20);
    }

    /**
     * post a message into your database.
     * @param integer $relatedID
     * @param integer $userID
     * @param string $message
     * @param mixed $data
     * @return mixed Model otherwise false
     */
    public function yiichat_post($relatedID, $userID, $message, $data) {
        if (!empty($message)) {
            $chat = new Chat;
            $chat->relatedID = $relatedID;
            $chat->userID = $userID;
            $chat->userLabel = $this->getIdentityName();
            $chat->text = $message;
            $chat->data = serialize($data);
            if ($chat->save()) {
                return $this->restructureChat($chat);
            }
        }
        return false;
    }

    /**
     * 
     * @param integer $relatedID
     * @param integer $userID
     * @param integer $lastID
     * @param mixed $data
     * @return Chat[]
     */
    public function yiichat_list_posts($relatedID, $userID, $lastID, $data) {
        $limit = 3;
        $rows = array();

        if ($lastID == -1) {
            $chats = Chat::model()->findAll(array(
                'order' => 'datetime ASC',
                'condition' => 'relatedID = :relatedID',
                'params' => array(':relatedID' => $relatedID)
            ));
            foreach ($chats as $key => $chat) {
                $rows[$key] = $this->restructureChat($chat);
            }
        } else {
            $chats = Chat::model()->findAll(array(
                'condition' => 'userID != :userID AND relatedID = :relatedID',
                'order' => 'datetime DESC',
                'params' => array(':userID' => $userID, ':relatedID' => $relatedID)
            ));
            $lastChats = $this->getLastPosts($chats, $limit, $lastID);
            foreach ($lastChats as $key => $lastChat) {
                $rows[$key] = $this->restructureChat($lastChat);
            }
        }
        return $rows;
    }

    public function restructureChat($chat) {
        return array(
            'id' => $chat->ID,
            'chat_id' => $chat->relatedID,
            'post_identity' => $chat->userID,
            'owner' => $chat->userLabel,
            'created' => $chat->datetime,
            'text' => $chat->text,
            'data' => $chat->data,
            'time' => $chat->datetime,
        );
    }

    /**
     * retrieve the last posts since the last_id, must be used
     * only when the records has been filtered (case timer).
     * @param Chat[] $chats
     * @param integer $limit
     * @param integer $lastID
     * @return string[]
     */
    private function getLastPosts($chats, $limit, $lastID) {
        if (count($chats) == 0) {
            return array();
        }

        $n = -1;
        foreach ($chats as $index => $chat) {
            if ($chat->ID == $lastID) {
                $n = $index;
                break;
            }
        }

        if (empty($lastID)) {
            if ($n == -1) {
                $n = $index - 1;
            }
            if ($n == 0) {
                // TEST CASE: 7
                return $chats;
            } else {
                // TEST CASES: 6 and 8
                $reservedChats = array_chunk($chats, $limit);
                return array_reverse($reservedChats[0]);
            }
        }

        if ($n > 0) {
            $selectedChats = array_chunk($chats, $n);
            $reservedChats = array_chunk($selectedChats[0], $limit);
            return array_reverse($reservedChats[0]);
        } else {
            return array();
        }
    }

}

?>