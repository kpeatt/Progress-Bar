<?php
// app/Model/User.php
class User extends AppModel {
    public $name = 'User';
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => 'A password is required'
            )
        ),
        'steam_id' => array(
        ),
        'steam_name' => array(
        ),
        'steam_realname' => array(
        ),
        'steam_customurl' => array(
        ),
        'steam_avatar' => array(
        ),
        'steam_avatar_med' => array(
        ),
        'steam_avatar_full' => array(
        ),
        'steam_state' => array(
        ),
        'steam_loc_country' => array(
        ),
        'steam_loc_state' => array(
        ),
        'steam_loc_cityid' => array(
        ),
        'steam_lastlogoff' => array(
        ),
        'steam_membersince' => array(
        )
    );
}