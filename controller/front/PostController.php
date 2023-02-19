<?php
/**
* 2020 C. Hodor - OS Private Community
*/

class PostController
{
	private static
	$instance;

	private
	$db,
	$user,
	$post_title,
	$post_content,
	$user_id,
	$flair;

	private function __construct()
	{
		$this->db = Connect::init();
		$this->user = new Users(Session::get(Session::setup('session/session_name')));
		$this->post_title = (isset($_POST['titlu_postare']) && !empty($_POST['titlu_postare'])) ? Tools::getValue('titlu_postare') : null;
		$this->post_content = (isset($_POST['content']) && !empty($_POST['content'])) ? Tools::getValue('content') : null;
		$this->user_id = Session::get(Session::setup('session/session_name'));
		// $this->flair = (isset($_POST['flair_post']) && !empty($_POST['flair_post'])) ? Tools::getValue('flair_post') : null;
	}

	public static function init()
	{
		if (!self::$instance)
		{
			self::$instance = new PostController();
		}

		return self::$instance;
	}

	public function post()
	{
		$db = $this->db;
		// print_r((Tools::isVideo($this->post_content)) ? Tools::youtubeID($this->post_content) : 'False');

		// if (Session::exists(Session::setup('session/session_name')))
		// {
		// 	$db->insert('posts', array(
		// 		'User_ID' => $this->user_id,
		// 		'Post_Title' => $this->post_title,
		// 		'Post_Content' => $this->post_content,
		// 	));
		// }

		$this->exit();
	}

	private function exit()
	{
		$this->user = null;
		$this->db = null;
	}
}