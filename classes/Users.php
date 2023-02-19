<?php
/**
* 2020 C. Hodor - Open Source Community
*/

/**
 * Register Ofera functionalitatea de a inregistra utilizatori in baza noastra de date.
 * @since 1.0
 */

Class Users 
{

	private
	$db,
	$user,
	$data,
	$cookie,
	$session,
	$password,
	$username,
	$connected,
	$recovery_string;

	public function __construct($user = null) 
	{
		$this->db = Connect::init();
		$this->session = Session::setup('session/session_name');
		$this->cookie = Session::setup('remember/cookie_name');

		if (!$user) 
		{
			if (Session::exists($this->session)) 
			{
				$user = Session::get($this->session);
				try
				{
					if ($this->find($user)) 
					{
						$this->connected = true;
					}
				} catch (PDOException $e) 
				{

				}
			}
		} else 
		{
			$this->find($user);
		}
	}

	public function create($fields = array()) 
	{
		if (!$this->db->insert('users', $fields)) 
		{
			throw new Exception('Ooops, momentan nu putem inregistra utilizatori noi!');
		}
	}

	public function cookie_login() 
	{
		Session::put($this->session, $this->data()->ID);
		$this->db->seen($this->data()->ID);
	}

	public function logout() 
	{
		if (Cookie::exists($this->cookie)) 
		{
			$this->db->delete('session', array('User_ID', '=', Session::get($this->session)));
			Cookie::delete($this->cookie);
		}

		Session::delete($this->session);

		$this->connected = false;
	}

	public function login($username = null, $password = null, $remember = false) 
	{
		$this->user = $this->find(Tools::replaceAccentedChars($username));

		if ($this->user) {
			if (Tools::verify_hash($password, $this->data()->Password)) 
			{
				if ($this->data()->Is_Blocked !== '1') 
				{
					Session::put($this->session, $this->data()->ID);
					$this->db->seen($this->data()->ID);

					if ($remember) {
						$hashCheck = $this->db->get('*', 'session', array('User_ID', '=', $this->data()->ID));
						$hash = Tools::unique();

						if (!$this->db->count()) 
						{
							$this->db->insert('session', array(
								'User_ID' => $this->data()->ID,
								'Hash' => $hash,
							));
						} else 
						{
							$hash = $hashCheck->first()->Hash;
						}
						Cookie::put($this->cookie, $hash, Session::setup('remember/cookie_expiry'));
					}
					$this->connected = true;
					return true;
				}
			}
		}

		return false;
	}

	public function reset_password($username = null, $recovery_string = null, $remember = false)
	{
		$this->user = $this->find(Tools::replaceAccentedChars($username));

		if ($this->user)
		{
			if (Tools::verify_hash($recovery_string, $this->data()->Recovery_String))
			{
				if (!$this->data()->Is_Blocked) 
				{
					Session::put($this->session, $this->data()->ID);
					if ($remember) 
					{
						$hashCheck = $this->db->get('*', 'session', array('User_ID', '=', $this->data()->ID));
						$hash = Tools::unique();

						if (!$this->db->count()) {
							$this->db->insert('session', array(
								'User_ID' => $this->data()->ID,
								'Hash' => $hash,
							));
						} else {
							$hash = $hashCheck->first()->Hash;
						}
						Cookie::put($this->cookie, $hash, Session::setup('remember/cookie_expiry'));
					}

					$this->connected = true;
					return true;
				}
			}
		}

	}

	public function update($fields = array(), $id = null) 
	{
		if (!$id && $this->connected) 
		{
			$id = $this->data()->ID;
		}

		if (!$this->db->update('users', $id, $fields)) 
		{
			throw new Exception('Actualizarea a esuat!');
			return false;
		}

		return true;
	}

	public function delete_invite()
	{
		$delete = $this->db->delete('invitations', array('Invitation_Code', '=', $this->data()->Invitation_Code));
		
		return (!$delete) ? false : true;
	}

	public function find($user = null) 
	{
		if ($user) 
		{
			$field = (is_numeric($user)) ? 'ID' : 'Username';
			$data = $this->db->get('*', 'users', array($field, '=', $user));

			if ($this->db->count()) 
			{
				$this->data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function user_list()
    {
        return $this->db->select('*', 'Users')->results();
    }

    public function code_list()
    {
        return $this->db->select('Invitation_Code', 'invitations')->results();
    }

    public function banned()
    {
    	return $this->data()->Is_Blocked;
    }

    public function username()
    {
    	return $this->data()->Username;
    }

	public function user($detail, $type = null)
    {
        switch ($detail)
        {
            case 'username':
                if (!$type)
                { 
                	echo Tools::safeOutput($this->data()->Username);
                } elseif ($type == 1)
                {
                	return Tools::safeOutput($this->data()->Username);
                };
            break;

            case 'avatar':
                echo Tools::safeOutput($this->data()->Avatar);
            break;

            case 'created':
                    $time = Tools::safeOutput($this->data()->Date_Created);
                    $date = strtotime($time);
                    echo date("d/m/y g:i A", $date);
            break;

            case 'id':
                if (!$type)
                { 
                	echo Tools::safeOutput($this->data()->ID);
                } elseif ($type == 1)
                {
                	return Tools::safeOutput($this->data()->ID);                	
                };
            break;

            case 'reported':
                if (Tools::safeOutput($this->data()->Is_Reported) == '0')
                {
                    echo 'Neraportat.';
                } else
                {
                    echo 'Raportat.';
                }
            break;

            case 'banned':
                switch (Tools::safeOutput($this->data()->Is_Blocked) == '1')
                {
                    case '0':
                        echo 'Activ';
                    break;

                    case '1':
                        echo 'Inactiv';
                    break;
                } 
            break;

            case 'role':
                switch (Tools::safeOutput($this->data()->Role))
                {
                    case '1':
                        echo 'Membru';
                    break;

                    case '2':
                        echo 'Moderator';
                    break;

                    case '3':
                        echo 'Administrator';
                    break;
                }
            break;
        }
    }

	public function isAdmin()
	{
		if ($this->data()->Role != '3')
		{
			return false;
		}

		return true;
	}

	public function exists()
	{
		return (!empty($this->data) ? true : false);
	}

	public function connected() {
		return $this->connected;
	}

	public function data() {
		return $this->data;
	}
}