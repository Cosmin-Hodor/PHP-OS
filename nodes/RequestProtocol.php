<?php
/**
* 2020 C. Hodor - OS Private Community
*/

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Rectificam charsetul pentru Apache. (In unele cazuri e prea tarziu)
if (!headers_sent())
{
    header('Content-Type: text/html; charset=utf-8');
}

// Integram setarile & autoload.
require_once('../config/db_config.php');
require_once('../config/encrypt_keys.php');
require_once('../classes/Connections.php');

if (isset($_POST['id']) && isset($_POST['expeditor']) && isset($_POST['react_check']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$expeditor = Get::request('expeditor');
		$id = Get::request('id');

		$check_user_react = $db->query("SELECT Type FROM reacts WHERE User_ID = {$expeditor} AND Post_ID = {$id}")->results();

		if ($db->count())
		{
			echo $check_user_react[0]->Type;
		} else
		{
			echo 'None';
		}
	}
	exit;
}

if (isset($_POST['id']) && isset($_POST['expeditor']) && isset($_POST['react']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$expeditor = Get::request('expeditor');
		$react = Get::request('react');
		$id = Get::request('id');

		echo $react;

		$check_vote = $db->query("SELECT Type FROM reacts WHERE User_ID = {$expeditor} AND Post_ID = {$id}")->results();

		if (!$db->count())
		{
			$db->insert('reacts', array(
				'Post_ID' => $id,
				'User_ID' => $expeditor,
				'Type' => $react
			));
		} else 
		{	
			$type = $check_vote[0]->Type;

			if ($type != $react)
			{
				$db->query("UPDATE reacts SET Type = {$react} WHERE Post_ID = {$id} AND User_ID = {$expeditor}");
			} else
			{
				$db->query("DELETE FROM reacts WHERE Post_ID = {$id} AND User_ID = {$expeditor}");
			}
		}
	}
	exit;
}

if (isset($_POST['id']) && isset($_POST['expeditor']) && isset($_POST['tip']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$expeditor = Get::request('expeditor');
		$tip = Get::request('tip');
		$id = Get::request('id');

		$check_vote = $db->query("SELECT Type FROM votes WHERE User_ID = {$expeditor} AND Post_ID = {$id}")->results();

		if (!$db->count())
		{
			$db->insert('votes', array(
				'Post_ID' => $id,
				'User_ID' => $expeditor,
				'Type' => $tip
			));

			$db->vote($tip, $id);
		} else 
		{	
			$type = $check_vote[0]->Type;

			switch ($tip)
			{
				case 0:
					switch ($type)
					{
						case 0:
							$db->query("DELETE FROM votes WHERE Post_ID = {$id} AND User_ID = {$expeditor}");
							$db->vote(1, $id);
						break;

						case 1:
							$db->vote(-1, $id);
							$db->query("UPDATE votes SET Type = 0 WHERE Post_ID = {$id} AND User_ID = {$expeditor}");
						break;
					}
				break;

				case 1:
					switch ($type)
					{
						case 0:
							$db->vote(2, $id);
							$db->query("UPDATE votes SET Type = 1 WHERE Post_ID = {$id} AND User_ID = {$expeditor}");
						break;

						case 1:
							$db->query("DELETE FROM votes WHERE Post_ID = {$id} AND User_ID = {$expeditor}");
							$db->vote(0, $id);
						break;
					}
				break;
			}
		}
	}
	exit;
}

if (isset($_POST['generare_invitatii']))
{
	$ajax_key = Keys::get('pepper');

	if (isset($_POST['generare_invitatii']) && password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$permitted_chars = '0a!1b@2c#3d$4e%5f^6g&7h*8j(9i)0k?lAmBnCoEpFqGrHsItJuLvMwxyzWXYZ';

		$inviteNr = Get::request('generare_invitatii');

		$random_key = '';

		for ($i = 0; $i < $inviteNr; $i++)
		{
			$key = Get::Key();

			$db->insert('invitations',array(
				'Invitation_Code' => $key
			));
		}

		echo 'Chei generate cu succes!';
	}
	exit;
}

if (isset($_POST['baza']) && isset($_POST['interval']) && isset($_POST['expeditor_id']))
{
	$ajax_key = Keys::get('pepper');

	$base = Get::request('baza');
	$increment = Get::request('interval');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();
		$post_list = $db->query("SELECT * FROM posts ORDER BY ID DESC LIMIT {$base},{$increment}")->results();
		$user_id = Get::request('expeditor_id');

		foreach($post_list as $post)
		{
			$post_title = '';
			$type = null;

			// Data despre OP
			$user = $db->query("SELECT Username FROM users WHERE ID = {$post->User_ID}")->results();

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
						echo '<h3>' . Safe::output($value['data']['text']) . '</h3>';
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
							echo '<li>' . Safe::output($item) . '</li>';
						}
						echo '</ul>';
					break;

					case 'code':
						echo '<code>' . Safe::html($value['data']['code']) . '</code>';
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
			echo '
				</div>
				<div class="comentarii_postare">
					<a href="/postare/'. $content_id .'"><span>Comentarii<span>(0)</span></a>
				</div>
			</div>
			</article>
			';

			$username = null;
			$user = null;
		}
	};
	exit;
};

if (isset($_POST['sterge_utilizator']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$user = Get::request('sterge_utilizator');

		$db->delete('users',array(
			'Username', '=', $user
		));
	}
	exit;
}

if (isset($_POST['ban_utilizator']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$user = Get::request('ban_utilizator');

		$db->update('users', $user, array(
			'Is_Blocked' => '1'
		));

		echo 'Utilizatorul a fost blocat cu succes';
	}
	exit;
}

if (isset($_POST['unban_utilizator']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$user = Get::request('unban_utilizator');

		$db->update('users', $user, array(
			'Is_Blocked' => '0'
		));

		echo 'Utilizatorul a fost deblocat cu succes';
	}
	exit;
}

if (isset($_POST['cerere_prietenie']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$user = Get::request('cerere_prietenie');
		$user_from = Get::request('expeditor');

		$db->insert('friend_requests', array(
			'User_To' => $user,
			'User_From' => $user_from
		));
	}
	exit;
}

if (isset($_POST['post_request']))
{
	$ajax_key = Keys::get('pepper');

	if (password_verify($ajax_key, Get::request('token')))
	{
		$db = Connect::init();

		$user = Get::request('expeditor');

		$title = $_POST['post_title'];

		$content = $_POST['post_request'];

		$db->insert('posts', array(
			'User_ID' => $user,
			'Post_Title' => $title,
			'Post_Content' => $content
		));
	}
	exit;
}

class Get
{
    public static function Key()
    {
    	$random_string = '';

    	for ($i = 0; $i < 12; $i++)
    	{
    		$number = random_int(0, 36);
    		$character = base_convert($number, 10, 36);
    		$random_string .= $character;
    	}

    	return $random_string;
    }

    public static function getValueRaw($key, $defaultValue = false)
    {
        if (!isset($key) || empty($key) || !is_string($key))
        {
            return false;
        }

        return (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));
    }

	public static function request($key, $defaultValue = false)
    {
        $ret = static::getValueRaw($key, $defaultValue);

        if (is_string($ret))
        {
            return stripslashes(urldecode(preg_replace('/((\%5C0+|(\%00+)))/i', '', urlencode($ret))));
        }
        return $ret;
    }
}

class Safe
{
    public static function output($string, $html = false)
    {
        if (!$html)
        {
            $string = strip_tags($string);
        }

        return Safe::html($string, ENT_QUOTES);
    }

    public static function html($string, $type = ENT_QUOTES)
    {
        if (is_array($string)) {
            return array_map(['Safe', 'html'], $string);
        }

        return htmlentities((string) $string, $type, 'utf-8');
    }
}