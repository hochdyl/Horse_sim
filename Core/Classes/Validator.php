<?php

namespace App\Core\Classes;

use JetBrains\PhpStorm\Pure;

/**
 * Class Validator
 * @package App\Core\Classes
 */
class Validator {
    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @var array
     */
    private array $customErrors;

    /**
     * @const Regular expression list
     */
    public const EMAIL_REGEX = '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}';
    public const URL_REGEX = '^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$';
    public const IP_REGEX = '(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])';
    public const FLOAT_REGEX = '[0-9\.,]+';
    public const INT_REGEX = '[0-9]+';
    public const TEL_REGEX = '^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$';
    public const ALPHA_REGEX = '[\p{L} ]+';
    public const ALPHA_NUM_REGEX = '[\p{L}0-9]+';

    /**
     * @var array[]
     */
    private array $patterns = array(
        'email' => [self::EMAIL_REGEX, FILTER_VALIDATE_EMAIL],
        'url' => [self::URL_REGEX, FILTER_VALIDATE_URL],
        'ip' => [self::IP_REGEX, FILTER_VALIDATE_IP],
        'int' => [self::INT_REGEX, FILTER_VALIDATE_INT],
        'float' => [self::FLOAT_REGEX, FILTER_VALIDATE_FLOAT],
        'alpha' => [self::ALPHA_REGEX],
        'alpha_num' => [self::ALPHA_NUM_REGEX]
    );

    /**
     * Validator constructor.
     * @param array $data
     */
    public function __construct(private array $data) {}

    /**
     * Call the correct method according to the rules given
     * @param array $rules
     */
    public function validate(array $rules) {
        foreach ($rules as $name => $rulesArray) {
            if (array_key_exists($name, $this->data)) {
                foreach ($rulesArray as $rule) {
                    switch ($rule) {
                        case 'empty':
                            $this->empty($name, $this->data[$name]);
                            break;
                        case 'required':
                            $this->required($name, $this->data[$name]);
                            break;
                        case 'tel':
                            $this->tel($name, $this->data[$name]);
                            break;
                        case str_starts_with($rule, 'min'):
                            $this->min($name, $this->data[$name], $rule);
                            break;
                        case str_starts_with($rule, 'max'):
                            $this->max($name, $this->data[$name], $rule);
                            break;
                        case str_starts_with($rule, 'equal'):
                            $this->equal($name, $this->data[$name], $rule);
                            break;
                        case str_starts_with($rule, 'between'):
                            $this->between($name, $this->data[$name], $rule);
                            break;
                        case str_starts_with($rule, 'pattern'):
                            $this->customPattern($name, $this->data[$name], $rule);
                            break;
                        case array_key_exists($rule, $this->patterns):
                            $this->pattern($name, $this->data[$name], $rule);
                            break;
                        // Add the rules here
                    }
                }
            }
        }
    }

    /**
     * Custom an error message
     * @param array $errorMessage
     */
    public function customErrors(array $errorMessage) {
        foreach ($errorMessage as $name => $message) {
            $arrayKey = str_replace(".", "", strstr($name, '.'));
            $name = strstr($name, '.', true);
            if (!empty($this->data) && !$this->isSuccess() && $this->customErrors[$name][$arrayKey]) $this->errors[$name][$arrayKey] = $message;
        }
    }

    /**
     * Check if the field is correct according to the model
     * @param string $name
     * @param string $value
     * @param string $rule
     */
    private function pattern(string $name, string $value, string $rule) {
        if (array_key_exists($rule, $this->patterns)) {
            if (count($this->patterns[$rule]) === 1) {
                if (!filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^({$this->patterns[$rule][0]})$/u")))) {
                    $this->errors[$name][] = "Le champ $name attend un $rule";
                    $this->customErrors[$name][] = true;
                } else {
                    $this->customErrors[$name][] = false;
                }
            } else {
                if (!preg_match("/^({$this->patterns[$rule][0]})$/u", $value) || !filter_var($value, $this->patterns[$rule][1])) {
                    $this->errors[$name][] = "Le champ $name attend un $rule";
                    $this->customErrors[$name][] = true;
                } else {
                    $this->customErrors[$name][] = false;
                }
            }
        }
    }

    /**
     * Check if the field matches to the custom REGEX
     * @param string $name
     * @param string $value
     * @param string $rule
     */
    private function customPattern(string $name, string $value, string $rule) {
        $regex = substr($rule, 8);

        if (!preg_match("/^({$regex})$/u", $value)) {
            $this->errors[$name][] = "$name doit contenir certain caractères";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if the field is empty
     * @param string $name
     * @param string $value
     */
    private function required(string $name, string $value) {
        if (!isset($value) || empty($value)) {
            $this->errors[$name][] = "Le champ $name est requis !";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if the field is filled
     * @param string $name
     * @param string $value
     */
    private function empty(string $name, string $value) {
        if (!empty($value)) {
            $this->errors[$name][] = "Ce champs ne peut pas être remplis !";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if it's a valid phone number
     * @param string $name
     * @param string $value
     */
    private function tel(string $name, string $value) {
        $value = str_replace(" ", "", $value);

        if (!strlen($value) === 10 || !is_numeric($value) || !preg_match("/^(".self::TEL_REGEX.")$/u", $value)) {
            $this->errors[$name][] = "$name doit être un numéro de téléphone";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if the minimum number of characters is respected
     * @param string $name
     * @param string $value
     * @param string $rule
     */
    private function min(string $name, string $value, string $rule) {
        preg_match_all('/(\d+)/', $rule, $matches);
        $limit = (int) $matches[0][0];

        if (strlen($value) < $limit) {
            $this->errors[$name][] = "$name doit comprendre un minimum de $limit caractères";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if the maximum number of characters is respected
     * @param string $name
     * @param string $value
     * @param string $rule
     */
    private function max(string $name, string $value, string $rule) {
        preg_match_all('/(\d+)/', $rule, $matches);
        $limit = (int) $matches[0][0];

        if (strlen($value) > $limit) {
            $this->errors[$name][] = "$name doit comprendre un maximum de $limit caractères";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if the minimum & maximum number of characters is respected
     * @param string $name
     * @param string $value
     * @param string $rule
     */
    private function between(string $name, string $value, string $rule) {
        $betweenArray = explode(":", substr($rule, 8));

        if (strlen($value) < (int) $betweenArray[0] || strlen($value) > (int) $betweenArray[1]) {
            $this->errors[$name][] = "$name doit comprendre un minimum de $betweenArray[0] caractères et un maximum de $betweenArray[1] caractères";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if the value of the field corresponds to the expected one
     * @param string $name
     * @param string $value
     * @param string $rule
     */
    private function equal(string $name, string $value, string $rule) {
        $equal = substr($rule, 6);

        if ($value !== $equal) {
            $this->errors[$name][] = "$name ne correspond pas à $equal";
            $this->customErrors[$name][] = true;
        } else {
            $this->customErrors[$name][] = false;
        }
    }

    /**
     * Check if the value of the field matches to the values in array
     * @param array $values
     * @return bool
     */
    public function matchValue(array $values): bool {
        $bool = [];

        foreach ($values as $key => $value) {
            if (array_key_exists($key, $this->data) && !is_null($value)) {
                $bool[] = $value === $this->data[$key];
            }
        }

        return in_array(false, $bool) || empty($bool) ? false : true;
    }

    /**
     * Validated fields
     * @return bool
     */
    public function isSuccess(): bool {
        return empty($this->errors) && !empty($this->data);
    }

    /**
     * Check if the submit button is submitted
     * @param string|null $submit
     * @return bool
     */
    public function isSubmitted(string $submit = null): bool {
        $submitButton = array_pop($this->data);

        if ($submit) {
            if (isset($_POST[$submit])) return true;
        } else {
            if (isset($submitButton)) return true;
        }

        return false;
    }

    /**
     * Display errors
     * @param array|null $message
     * @return array|null
     */
    public function displayErrors(array $message = null): ?array {
        if ($message) {
            foreach ($message as $key => $value) $this->errors['others'] = $message;
        }

        return $this->errors;
    }

    /**
     * Filter Inputs
     * @param string $data
     * @return string
     */
    #[Pure] public static function filter(string $data): string {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        $data = htmlspecialchars($data);

        return $data;
    }

}
