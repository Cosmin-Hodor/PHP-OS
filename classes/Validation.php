<?php
/**
* 2020 C. Hodor - OS Private Community
*/

/**
* Class Validate
* @since 1.0 
*/

class Validate 
{

	private $passed = false,
			$errors = array(),
			$db = null;

	public function __construct()
	{
		$this->db = Connect::init();
	}

	public function check($source, $items = array())
	{
		foreach($items as $item => $rules)
		{
			foreach($rules as $rule => $rule_value)
			{
				$value = $source[$item];

				if ($rule === 'required' && empty($value))
				{
					$this->addError($this->normalizeField($item) . " - Campul nu poate fi gol.");
				} else if (!empty($value))
				{
					switch($rule)
					{
						case 'min':

							if (strlen($value) < $rule_value)
							{
								$this->addError($this->normalizeField($item) . " - min. {$rule_value} caractere.");
							}

						break;

						case 'max':

							if (strlen($value) > $rule_value)
							{
								$this->addError($this->normalizeField($item) . " - max. {$rule_value} caractere.");
							}

						break;

						case 'unique':
						
							$this->db->get("Username", "users", array("Username", "=", $value));

							if ($this->db->count())
							{
								$this->addError("Numele de utilizator este deja folosit de un alt utilizator.");
							}

						break;

						case 'unique_invite':

							$this->db->get("Invitation_Code", "invitations", array("Invitation_Code", "=", $value));
							if (!$this->db->count())
							{
								$this->addError("Codul de invitare nu este valid.");
							}

						break;

						case 'validate_url':
							$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webl'];
							$url_inspect = pathinfo($value, PATHINFO_EXTENSION);

							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $value);
							curl_setopt($ch, CURLOPT_NOBODY, 1);
							curl_setopt($ch, CURLOPT_FAILONERROR, 1);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							$valid = (curl_exec($ch)) ? false : true;
							curl_close($ch);

							if (!in_array($url_inspect, $allowed_ext) || !$valid)
							{
								$this->addError("Adresa pentru avatar nu este valida.");
							}
						break;
					}
				}
			}
		}

		if (empty($this->errors()))
		{
			$this->passed = true;
		}
		return $this;
	}

	private function normalizeField($field)
	{
		switch ($field)
		{
			case 'username':
				return 'Numele de utilizator';
			break;

			case 'password':
				return 'Parola';
			break;

			case 'nume_nou':
				return 'Nume';
			break;

			case 'parola_noua':
				return 'Parola';
			break;

			case 'adresa_avatar':
				return 'Adresa pentru avatar';
			break;

			case 'recovery_string':
				return 'Cuvantul de recuperare';
			break;

			case 'cuvinte_recuperare_noi':
				return 'Cuvinte de Recuperare';
			break;

			case 'cod_invitare':
				return 'Codul de invitare';
			break;
		}
	}

	private function addError($error)
	{
		$this->errors[] = $error;
	}

	public function errors()
	{
		return $this->errors;
	}

	public function passed()
	{
		return $this->passed;
	}
}