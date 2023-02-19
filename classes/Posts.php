<?php
/**
* 2020 C. Hodor - Open Source Community
*/

/**
 * Register Ofera functionalitatea de a inregistra utilizatori in baza noastra de date.
 * @since 1.0
 */

Class Posts
{

	private static
	$instance;

	private
	$db,
	$id,
	$user;

	public function __construct()
	{
		$this->db = Connect::init();
		$this->user = new Users(Session::get(Session::setup('session/session_name')));
		$this->id = $this->user->data()->ID;
	}

	public static function init()
	{
		if (!self::$instance)
		{
			self::$instance = new Posts();
		}

		return self::$instance;
	}

	public function getPosts()
	{
		return $this->db->select('*', 'posts', 5)->results();
	}

	public function single($id)
	{
		$db = Connect::init();
		$post_list = $db->query("SELECT * FROM posts WHERE ID = {$id}")->results();
		$check_vote = null;
		$react_type = null;
		$post_title = '';
		$type = null;

		foreach($post_list as $post)
		{
			// Data despre OP
			$user = $db->query("SELECT Username FROM users WHERE ID = {$post->User_ID}")->results();

			$username = $user[0]->Username;

			$content = json_decode($post->Post_Content, true);
			$blocks = $content['blocks'];
			$content_id = $post->ID;
			$user_id = $this->id;
			$votes = $post->Votes;

			if (!empty($post->Post_Title) && !ctype_space($post->Post_Title))
			{
				$post_title = "<h2>{$post->Post_Title}</h2>";
			}

			echo
			'
			<article class="postare">
			<div class="antet_postare">
	            '. $post_title .'
	            <span class="post_meta">'. date("d/m/y g:i A", strtotime($post->Date_Created)) .' - Autor: <span class="post_autor"><a href="/profil/'. $username .'">'. $username .'</a></span></span>
	    	</div>
			';

			foreach($blocks as $value) 
			{
				switch ($value['type'])
				{
					case 'header':
						echo '<h3>' . Tools::safeOutput($value['data']['text']) . '</h3>';
					break;

					case 'paragraph':
						echo '<p class="continut_postare">';
						echo stripcslashes($value['data']['text']);
						echo '</p>';
					break;

					case 'embed':
						echo 
						'
						<object type="video/mp4" data="'. $value['data']['embed'] .'" width="100%" height="360"></object>
						';

						if(!empty($value['data']['caption']))
						{
							echo '<span class="embed_caption">&#8212; ' . $value['data']['caption'] . '</span>';
						}
					break;

					case 'image':
						echo
						'
						<img src="'. $value['data']['url'] .'" alt="'.(!empty($value['data']['caption']) ? $value['data']['caption'] : 'Nu detine titlu').'" no-referrer style="width: 100%">
						';
					break;

					case 'list':
						echo '<ul class="continut_lista">';
						foreach($value['data']['items'] as $item)
						{
							echo '<li>' . Tools::safeOutput($item) . '</li>';
						}
						echo '</ul>';
					break;

					case 'code':
						echo '<code>' . Tools::htmlentitiesUTF8($value['data']['code']) . '</code>';
					break;
				}
			}

			$check_vote = $db->query("SELECT Type FROM votes WHERE User_ID = {$user_id} AND Post_ID = {$content_id}")->results();

			if ($db->count())
			{
				$type = $check_vote[0]->Type;
			};

			echo
			'
			<div class="subsol_postare">
				<div class="reactii_postare">
					<span id="numar_voturi" alt="Numarul total de voturi">'. $votes .'</span>';
					switch ($type)
					{
						case '0':
					echo'
					<button class="vot pro" data-postare="'. $content_id .'" alt="Buton Upvote"></button>
					<button class="vot contra rosu" data-postare="'. $content_id .'" alt="Buton Downvote"></button>
					';
						break;

						case '1':
					echo'
					<button class="vot pro verde" data-postare="'. $content_id .'" alt="Buton Upvote"></button>
					<button class="vot contra" data-postare="'. $content_id .'" alt="Buton Downvote"></button>
					';
						break;

						case null:
					echo'
					<button class="vot pro" data-postare="'. $content_id .'" alt="Buton Upvote"></button>
					<button class="vot contra" data-postare="'. $content_id .'" alt="Buton Downvote"></button>
					';
						break;
					}

					$react_list = $db->query("SELECT  SUM(CASE WHEN Type = 0 THEN 1 END) AS smile,
													  SUM(CASE WHEN Type = 1 THEN 1 END) AS lmao,
													  SUM(CASE WHEN Type = 2 THEN 1 END) AS love,
													  SUM(CASE WHEN Type = 3 THEN 1 END) AS msef
											   FROM reacts WHERE Post_ID = {$content_id}")->results();

					if ($db->count())
					{
						$smile_total = $react_list[0]->smile;
						$lmao_total = $react_list[0]->lmao;
						$love_total = $react_list[0]->love;
						$msef_total = $react_list[0]->msef;
					}


					$check_react = $db->query("SELECT Type FROM reacts WHERE User_ID = {$user_id} AND Post_ID = {$content_id}")->results();

					if ($db->count())
					{
						$react_type = $check_react[0]->Type;
					}

				echo '
					<div class="reactie_hug">
						<button class="confirm smile">&#128513;</button>

						<button class="confirm lmao">&#128514;</button>

						<button class="confirm love">&#128525;</button>

						<button class="confirm msef">&#128526;</button>
					</div>';

					switch ($react_type)
					{
						case '0':
						echo'
					<button class="reactie">&#128513;</button>
					<span id="smile" class="numar_voturi activ">'. $smile_total .'</span>

					<button class="reactie">&#128514;</button>
					<span id="lmao" class="numar_voturi">'. $lmao_total .'</span>

					<button class="reactie">&#128525;</button>
					<span id="love" class="numar_voturi">'. $love_total .'</span>

					<button class="reactie">&#128526;</button>
					<span id="msef" class="numar_voturi">'. $msef_total .'</span>';
						break;

						case '1':
						echo'
					<button class="reactie">&#128513;</button>
					<span id="smile" class="numar_voturi">'. $smile_total .'</span>

					<button class="reactie">&#128514;</button>
					<span id="lmao" class="numar_voturi activ">'. $lmao_total .'</span>

					<button class="reactie">&#128525;</button>
					<span id="love" class="numar_voturi">'. $love_total .'</span>

					<button class="reactie">&#128526;</button>
					<span id="msef" class="numar_voturi">'. $msef_total .'</span>';
						break;

						case '2':
						echo'
					<button class="reactie">&#128513;</button>
					<span id="smile" class="numar_voturi">'. $smile_total .'</span>

					<button class="reactie">&#128514;</button>
					<span id="lmao" class="numar_voturi">'. $lmao_total .'</span>

					<button class="reactie">&#128525;</button>
					<span id="love" class="numar_voturi activ">'. $love_total .'</span>

					<button class="reactie">&#128526;</button>
					<span id="msef" class="numar_voturi">'. $msef_total .'</span>';
						break;

						case '3':
						echo'
					<button class="reactie">&#128513;</button>
					<span id="smile" class="numar_voturi">'. $smile_total .'</span>

					<button class="reactie">&#128514;</button>
					<span id="lmao" class="numar_voturi">'. $lmao_total .'</span>

					<button class="reactie">&#128525;</button>
					<span id="love" class="numar_voturi">'. $love_total .'</span>

					<button class="reactie">&#128526;</button>
					<span id="msef" class="numar_voturi activ">'. $msef_total .'</span>';
						break;

						case null:
						echo'
					<button class="reactie">&#128513;</button>
					<span id="smile" class="numar_voturi">'. $smile_total .'</span>

					<button class="reactie">&#128514;</button>
					<span id="lmao" class="numar_voturi">'. $lmao_total .'</span>

					<button class="reactie">&#128525;</button>
					<span id="love" class="numar_voturi">'. $love_total .'</span>

					<button class="reactie">&#128526;</button>
					<span id="msef" class="numar_voturi">'. $msef_total .'</span>';
						break;
					}
				echo '
				</div>
			</div>
			</article>
			';
			
			$username = null;
			$user = null;
		}
	}

	public function load()
	{
		$post_list = $this->getPosts();

		foreach($post_list as $post)
		{
			$post_title = '';
			$type = null;

			// Data despre OP
			$user = $this->db->query("SELECT Username FROM users WHERE ID = {$post->User_ID}")->results();
			$username = $user[0]->Username;

			$content = json_decode($post->Post_Content, true);
			$blocks = $content['blocks'];
			$content_id = $post->ID;
			$votes = $post->Votes;

			if (!empty($post->Post_Title) && !ctype_space($post->Post_Title))
			{
				$post_title = "<a href='/postare/". $content_id ."'><h2>{$post->Post_Title}</h2></a>";
			}

			echo
			'
			<article class="postare">
			<div class="antet_postare">
	            '. $post_title .'
	            <span class="post_meta">'. date("d/m/y g:i A", strtotime($post->Date_Created)) .' - Autor: <span class="post_autor"><a href="/profil/'. $username .'">'. $username .'</a></span></span>
	    	</div>
			';

			foreach($blocks as $value) 
			{
				switch ($value['type'])
				{
					case 'header':
						echo '<h3>' . Tools::safeOutput($value['data']['text']) . '</h3>';
					break;

					case 'paragraph':
						echo '<p class="continut_postare">';
						echo stripcslashes($value['data']['text']);
						echo '</p>';
					break;

					case 'embed':
						echo 
						'
						<object type="video/mp4" data="'. $value['data']['embed'] .'" width="100%" height="360"></object>
						';

						if(!empty($value['data']['caption']))
						{
							echo '<span class="embed_caption">&#8212; ' . $value['data']['caption'] . '</span>';
						}
					break;

					case 'image':
						echo
						'
						<img src="'. $value['data']['url'] .'" alt="'.(!empty($value['data']['caption']) ? $value['data']['caption'] : 'Nu detine titlu').'" no-referrer style="width: 100%">
						';
					break;

					case 'list':
						echo '<ul class="continut_lista">';
						foreach($value['data']['items'] as $item)
						{
							echo '<li>' . Tools::safeOutput($item) . '</li>';
						}
						echo '</ul>';
					break;

					case 'code':
						echo '<code>' . Tools::htmlentitiesUTF8($value['data']['code']) . '</code>';
					break;
				}
			}

			$check_vote = $this->db->query("SELECT Type FROM votes WHERE User_ID = {$this->id} AND Post_ID = {$content_id}")->results();

			if ($this->db->count())
			{
				$type = $check_vote[0]->Type;
			};

			echo 
			'
			<div class="subsol_postare">
				<div class="reactii_postare">
					<span id="numar_voturi" alt="Numarul total de voturi">'. $votes .'</span>';
					switch ($type)
					{
						case '0':
					echo'
					<button class="vot pro" data-postare="'. $content_id .'" alt="Buton Upvote"></button>
					<button class="vot contra rosu" data-postare="'. $content_id .'" alt="Buton Downvote"></button>
					';
						break;

						case '1':
					echo'
					<button class="vot pro verde" data-postare="'. $content_id .'" alt="Buton Upvote"></button>
					<button class="vot contra" data-postare="'. $content_id .'" alt="Buton Downvote"></button>
					';
						break;

						case null:
					echo'
					<button class="vot pro" data-postare="'. $content_id .'" alt="Buton Upvote"></button>
					<button class="vot contra" data-postare="'. $content_id .'" alt="Buton Downvote"></button>
					';
						break;
					}
			echo '
				</div>
				<div class="comentarii_postare">
					<a href="/postare/'. $content_id .'"><span>Comentarii(0)</span></a>
				</div>
			</div>
			</article>
			';

			$username = null;
			$user = null;
		}
	}
}