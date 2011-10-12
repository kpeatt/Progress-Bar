<h1>Progress Bar</h1>

<p class="desc">Progress Bar is an online game sorting and progress tracking app for your Steam games.</p>

<?php

if (isset($error)) {
    echo '<p class="error">'.$error.'</p>';
}

echo $this->Form->create('User', array('type' => 'post', 'action' => 'login'));
echo $this->Form->hidden('OpenidUrl.openid', array('label' => false, 'value' => "http://steamcommunity.com/openid"));
echo $this->Form->end('Login');

?>

