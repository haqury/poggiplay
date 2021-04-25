<?php

use Model\Login_model;
use Model\Post_model;
use Model\User_model;
use Library\LogicException;

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts =  Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id)
    {

        $post_id = intval($post_id);

        if (empty($post_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function comment()
    {
        try {
            $form = new \Model\Comment_form_model($this->input->post());
            $form->validate();
        } catch (\LogicException $exception) {
            return $this->response_error($exception->getMessage());
        }

        try
        {
            $post = new Post_model($form->post_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        $post->comment($form);
        $posts =  Post_model::preparation($post, 'full_info');

        return $this->response_success(['post' => $posts]);
    }


    public function login()
    {
        try {
            $login = new Login_model($this->input->post());
            $login->authentication();
        } catch (\LogicException $exception) {
            return $this->response_error($exception->getMessage());
        }

        return $this->response_success(['user' => $login->getUserId()]);
    }

    public function like()
    {
        try {
            $form = new \Model\Like_form_model($this->input->post());
            $form->validate();
            $form->like();
        } catch (\LogicException $exception) {
            return $this->response_error($exception->getMessage());
        }
    }


    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    public function add_money(){
        // todo: 4th task  add money to user logic
        return $this->response_success(['amount' => rand(1,55)]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

    public function buy_boosterpack(){
        // todo: 5th task add money to user logic
        return $this->response_success(['amount' => rand(1,55)]); // Колво лайков под постом \ комментарием чтобы обновить . Сейчас рандомная заглушка
    }

}
