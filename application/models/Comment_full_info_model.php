<?php


namespace Model;


use Model\Comment_model;
use Model\User_model;

class Comment_full_info_model
{
    /** @var int */
    public $user_id;
    /** @var int */
    public $assing_id;
    /** @var int */
    public $parent_comment_id;
    /** @var string */
    public $text;

    /** @var string */
    public $time_created;
    /** @var string */
    public $time_updated;

    // generated
    public $comments = [];
    public $likes;
    public $user;
    
    public function __construct(Comment_model $comment)
    {
        $this->id = $comment->get_id();
        $this->text = $comment->get_text();
        $this->parent_comment_id = $comment->get_parent_comment_id();

        $this->user = User_model::preparation($comment->get_user(),'main_page');

        $this->likes = rand(0, 25);

        $this->time_created = $comment->get_time_created();
        $this->time_updated = $comment->get_time_updated();
    }
}