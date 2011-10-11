<?php

if (isset($error)) {
    echo '<p class="error">'.$error.'</p>';
}

echo $this->Form->create('User', array('type' => 'post', 'action' => 'login'));
echo $this->Form->hidden('OpenidUrl.openid', array('label' => false, 'value' => "http://steamcommunity.com/openid"));
echo $this->Form->end('Login');

?>