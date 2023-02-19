<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/**
* Class AuthController
* @since 1.0 
*/

Class AuthController
{
	private static $instance;

	protected $db,

	$username,
	$password,
	$recovery_string,

	$errors = array(),
	$error = false,

	$data,
	$connected;

	public function __construct ()
	{
		$this->db = Connect::init();
	}

	public function auth()
	{
		if (isset($_POST['Inregistrare']) && Token::check(Tools::getValue('token')) && empty(Tools::getValue('email')))
		{
			$validate = new Validate();

			$this->data = $validate->check($_POST, array(
				'username' => array(
					'field' => 'Nume Utilizator',
					'required' => true,
					'min' => 3,
					'max' => 35,
					'unique' => 'users'
				),

				'password' => array(
					'field' => 'Parola',
					'required' => true,
					'min' => 6,
					'max' => 255
				),

				'recovery_string' => array(
					'field' => 'Cuvantul cheie',
					'required' => true,
					'min' => 6,
					'max' => 255
				),

				'cod_invitare' => array(
					'field' => 'Cod de invitare',
					'unique_invite' => true,
					'required' => true,
					'min' => 12,
					'max' => 12
				)
			));

			$this->loadErrors();

			if (!$this->error)
			{
				$user = new Users;
				try
				{
					$user->create(array(
						'Username' => Tools::getValue('username'),
						'Password' => Tools::hash(Tools::getValue('password')),
						'Recovery_String' => Tools::hash(Tools::getValue('recovery_string')),
						'Is_Active' => '1',
						'Is_Reported' => '0',
						'Is_Blocked' => '0',
						'Role' => '1'
					));

					$delete = $this->db->delete('invitations', array('Invitation_Code', '=', Tools::getValue('cod_invitare')));

					if ($delete)
					{
						Session::flash('succes', 'Ai fost inregistrat cu succes.');
						Tools::redirect('/conectare');
					} else
					{
						Session::flash('failed', 'Codul de invitare este problematic.');
						Tools::redirect('/');
					}

				} catch (Exception $e)
				{
					die($e->getMessage);
				}
			}

			$validate = null;
		} else if (isset($_POST['Conectare']) && !empty($_POST['fir_recuperare']) && Token::check(Tools::getValue('token')))
		{
			$validate = new Validate();

			$this->data = $validate->check($_POST, array(
				'username' => array(
					'field' => 'Nume Utilizator',
					'required' => true
				),

				'fir_recuperare' => array(
					'field' => 'Cuvantul cheie',
					'required' => true,
					'min' => 6,
					'max' => 255
				)
			));

			$this->loadErrors();

			if (!$this->error)
			{
				$user = new Users;

				$remember = (Tools::getValue('remember_me') === 'on') ? true : false;

				$login = $user->reset_password(Tools::getValue('username'), Tools::getValue('fir_recuperare'), $remember);

				if ($login)
				{
					$this->connected = true;
                    Tools::redirect('/cont#parola_noua');
				} else
				{
					$this->errors[] = '<span class="lista_erori">Cuvintele de recuperare sau numele nu sunt valide.</span><br>';
				}
			}
			$validate = null;
		} else if (isset($_POST['Conectare']) && Token::check(Tools::getValue('token')))
		{
			$validate = new Validate();

			$this->data = $validate->check($_POST, array(
				'username' => array(
					'field' => 'Nume Utilizator',
					'required' => true
				),

				'password' => array(
					'field' => 'Parola',
					'required' => true
				)
			));

			$this->loadErrors();

			if (!$this->error)
			{
				$user = new Users;

				$remember = (Tools::getValue('remember_me') === 'on') ? true : false;

				$login = $user->login(Tools::getValue('username'), Tools::getValue('password'), $remember);

				if ($login)
				{
					$this->connected = true;
                    Tools::redirect('/');
				} else
				{
					$this->errors[] = '<span class="lista_erori">Nume sau Parola gresita.</span><br>';
				}
			}
			$validate = null;
		}

		if (Cookie::exists(Session::setup('remember/cookie_name')) && !Session::exists(Session::setup('session/session_name')))
    	{
            $hash = Cookie::get(Session::setup('remember/cookie_name'));
            $hashCheck = $this->db->get('*','session', array('Hash', '=', $hash));
            
            if ($this->db->count())
            {
            	$this->connected = true;
            	$user = new Users($hashCheck->first()->User_ID);
            	$user->cookie_login();
            	Tools::redirect('/');
            }
        }
	}

	public function connected()
	{
		return $this->connected;
	}

	public function getErrors()
	{
		if ($this->error)
		{
			foreach ($this->errors as $error)
			{
				echo $error;
			}
		}
	}

	public function loadErrors()
	{
		if (!$this->data->passed())
		{
			foreach($this->data->errors() as $error)
			{
				$this->errors[] = '<span class="lista_erori">' . $error . '</span><br>';
			}
			$this->error = true;
		} 
		else 
		{
			$this->error = false;
		}
	}

	public static function init()
	{
		if (!self::$instance)
		{
			self::$instance = new AuthController();
		}
		return self::$instance;
	}
}