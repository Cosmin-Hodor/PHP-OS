<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/**
* Class AuthController
* @since 1.0 
*/

Class AccountController
{
	private static 
	$instance;

	protected 
	$db,

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

	public function update()
	{
		if(isset($_POST['update']))
		{
			$update = Tools::getValue('update');

			switch ($update)
			{
				case 'nume_nou':
					$validate = new Validate();

					$this->data = $validate->check($_POST, array(
						'nume_nou' => array(
							'required' => true,
							'min' => 3,
							'max' => 35,
							'unique' => 'users'
						)
					));

					$this->loadErrors();

					if (!$this->error)
					{
						$user = new Users;
						try
						{
							$user->update(array(
								'Username' => Tools::getValue('nume_nou')
							));

							Session::flash('succes', 'Numele a fost schimbat cu succes.');
							Tools::redirect('/cont');

						} catch (Exception $e)
						{
							die($e->getMessage);
						}
					} else
					{
							Session::flash('failed', implode(' & ', $this->errors));
							Tools::redirect('/cont');
					}

					$validate = null;
				break;

				case 'parola_noua':
					$validate = new Validate();

					$this->data = $validate->check($_POST, array(
						'parola_noua' => array(
							'required' => true,
							'min' => 6,
							'max' => 255
						)
					));

					$this->loadErrors();

					if (!$this->error)
					{
						$user = new Users;

						try
						{
							$user->update(array(
								'Password' => Tools::hash(Tools::getValue('parola_noua'))
							));

							Session::flash('succes', 'Parola a fost schimbat cu succes.');
							Tools::redirect('/cont');

						} catch (Exception $e)
						{
							die($e->getMessage);
						}
					} else
					{
							Session::flash('failed', implode(' & ', $this->errors));
							Tools::redirect('/cont');
					}

					$validate = null;
				break;

				case 'cuvinte_recuperare_noi':
					$validate = new Validate();

					$this->data = $validate->check($_POST, array(
						'cuvinte_recuperare_noi' => array(
							'required' => true,
							'min' => 6,
							'max' => 255
						)
					));

					$this->loadErrors();

					if (!$this->error)
					{
						$user = new Users;

						try
						{
							$user->update(array(
								'Recovery_String' => Tools::hash(Tools::getValue('cuvinte_recuperare_noi'))
							));

							Session::flash('succes', 'Cuvintele de recuperare au fost schimbate cu succes.');
							Tools::redirect('/cont');

						} catch (Exception $e)
						{
							die($e->getMessage);
						}
					} else
					{
							Session::flash('failed', implode(' & ', $this->errors));
							Tools::redirect('/cont');
					}

					$validate = null;
				break;

				case 'adresa_avatar':
					$validate = new Validate();

					$this->data = $validate->check($_POST, array(
						'adresa_avatar' => array(
							'required' => true,
							'max' => 60,
							'validate_url' => true
						)
					));

					$this->loadErrors();

					if (!$this->error)
					{
						$user = new Users;

						try
						{
							$user->update(array(
								'avatar' => Tools::getValue('adresa_avatar')
							));

							Session::flash('succes', 'Avatar actualizat cu succes.');
							Tools::redirect('/cont');
						} catch (Exception $e)
						{
							die($e->getMessage);
						}
					} else
					{
							Session::flash('failed', implode(' ', $this->errors));
							Tools::redirect('/cont');
					}

					$validate = null;
				break;
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
			self::$instance = new AccountController();
		}
		return self::$instance;
	}
}