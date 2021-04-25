<?php


namespace Model;


class Comment_form_model
{
    public $post_id;
    public $message;
    public $comment_id;

    /** @var User_model */
    public $user;

    public function __construct($data)
    {
        $this->post_id = intval($data['post_id']) ?? null;
        $this->setUser($data);
        $this->comment_id = intval($data['comment_id']) ?? null;
        $this->message = $data['message'] ?? null;
    }

    public function validate(): bool
    {
        if (!$this->user){
            throw new \LogicException(\CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        if (empty($this->post_id) || empty($this->message)){
            throw new \LogicException(\CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }
        return true;
    }

    private function setUser($data)
    {
        $this->user = User_model::getById(intval($data['user_id'])) ?? null;
    }
}