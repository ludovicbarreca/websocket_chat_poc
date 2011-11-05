#!/php -q
<?php

include "websocket.class.php";

class Chat extends WebSocket {
    function process($user, $msg) {
        $this->say('< '.$msg);

        if (preg_match('#^/nickname#', $msg)) {
            $newnickname = substr($msg, 10);
            if (isset($user->nickname)) {
                $oldnickname = $user->nickname;
                $this->broadcast('update name', '<i>'.$oldnickname.' changed is nickname to '.$newnickname.'</i>');
            }
            else {
                $this->broadcast('joined', '<i>'.$newnickname.' joined !'.'</i>');
            }
            $user->nickname = $newnickname;
        }

        elseif (preg_match('#^/help#', $msg)) {
            if (preg_match('#^/help nickname#', $msg)) {
                $this->send($user->socket, '<i>NICKNAME USAGE : <b>/nickname __desired username__</b> will change your nickname</i>');
            }
            elseif (preg_match('#^/help help#', $msg)) {
                $this->send($user->socket, '<i>HELP USAGE : <b>/help __command__</b> will help you to know more about a command</i>');
            }
            else {
                $this->send($user->socket, '<i>Available commands :<br />/help, /nickname<br />(eg. /help help)</i>');
            }

            return;
        }

        else {
            if (!isset($user->nickname)) {
                $this->send($user->socket, '<b>You need to set your username first ! Please use the /nickname <name> syntax !</b>');
                return;
            }
            $this->broadcast('message', $user->nickname. ': '.$msg);
        }
    }

    function broadcast($type, $msg) {
        foreach ($this->users as $user) {
            $this->send($user->socket, $msg);
        }
    }
}

$master = new Chat('0.0.0.0', 45788);
