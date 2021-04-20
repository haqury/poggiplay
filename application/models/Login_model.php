<?php

namespace Model;
use App;
use Library\LogicException;

class Login_model extends \CI_Emerald_Model
{

    const ATTRIBUTE_LOGIN = 'email';

    const ATTRIBUTE_USER = 'user';

    const SYSTEM_ATTRIBUTES = [self::ATTRIBUTE_USER];

    private $login = '';
    private $password = '';

    /** @var User_model */
    private $user;

    /**
     * Login_model constructor.
     * @param array $data: date from form['email' => '...', 'password' => '...']
     */
    public function __construct(array $data)
    {
        parent::__construct();
        $this->setAttributes($data);
    }

    public static function logout()
    {
        App::get_ci()->session->unset_userdata('id');
    }

    public static function start_session(User_model $user)
    {
        // если перенедан пользователь
        $user->is_loaded(TRUE);

        App::get_ci()->session->set_userdata('id', $user->get_id());
    }

    /**
     * @return bool
     * @throws LogicException
     */
    public function authentication(): bool
    {
        if (!$this->validate()) return false;
        $this->user = User_model::getByLoginPassword($this->login, $this->password);
        self::start_session($this->user);

        return true;
    }

    public function validate(): bool
    {
        if (empty($this->login) || empty($this->password)) throw new LogicException(\CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        return true;
    }

    public function getUserId()
    {
        return $this->user->id;
    }

    private function setAttributes($data): void
    {
        foreach ($data as $attr => $item) {
            if ($attr == self::ATTRIBUTE_LOGIN) $this->login = $item;
            if (in_array($attr, self::SYSTEM_ATTRIBUTES)) continue;
            if (!isset($this->$attr)) continue;
            $this->$attr = $item;
        }
    }
}
