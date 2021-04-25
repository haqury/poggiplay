<?php


namespace Model;


class Like_form_model
{
    public $post_id;
    public $comment_id;

    /** @var User_model */
    public $user;

    public function __construct($data)
    {
        $this->post_id = intval($data['post_id']) ?? null;
        $this->comment_id = intval($data['comment_id']) ?? null;
        $this->setUser($data);
    }

    public function validate(): bool
    {
        if (!$this->user){
            throw new \LogicException(\CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        if (empty($this->post_id) && empty($this->message)){
            throw new \LogicException(\CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }
        return true;
    }

    public function like()
    {
        $this->likeTransaction();
        if ($this->user->getLike() < 0) {
            \App::get_ci()->db->trans_rollback();
            throw new \LogicException(\CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        } else {
            \App::get_ci()->db->trans_commit();
            return true;
        }
    }

    private function likeTransaction()
    {
        \App::get_ci()->db->trans_start();
        $model = $this->getModelForLike();
            $this->user->reload();
        if ($this->user->getLike() < 0) throw new \LogicException(\CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        \App::get_ci()->db->update($this->user::CLASS_TABLE, ['like' => $this->user->getLike() - 1], ['id' => $this->post_id]);
        \App::get_ci()->db->update($model::CLASS_TABLE, ['like' => $model->get_likes() + 1], ['id' => $this->post_id]);
        \App::get_ci()->db->trans_complete();
    }

    private function getModelForLike(): Like_interface
    {
        if ($this->post_id) {
            return (new Post_model($this->post_id));
        } elseif ($this->comment_id) {
            return (new Comment_model($this->comment_id));
        } else {
            throw new \LogicException(\CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }
    }

    private function setUser($data)
    {
        $this->user = User_model::getById(intval($data['user_id'])) ?? null;
    }
}