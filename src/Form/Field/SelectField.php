<?php

namespace Catalyst\Form\Field;

use \Catalyst\Form\Form;

/**
 * Represents a select (dropdown) field
 */
class SelectField extends AbstractField {
	use LabelTrait, SupportsAutocompleteAttributeTrait, SupportsPrefilledValueTrait;
	/**
	 * Options, [value => label]
	 * 
	 * @var string[]
	 */
	protected $options = [];

	/**
	 * Get the current options
	 * 
	 * @return string[] Current option array
	 */
	public function getOptions() : array {
		return $this->options;
	}

	/**
	 * Set the current option set
	 * 
	 * @param string[] $options New options
	 */
	public function setOptions(array $options) : void {
		$this->options = $options;
	}

	/**
	 * Return the field's HTML input
	 * 
	 * @return string The HTML to display
	 */
	public function getHtml() : string {
		$str = '';
		
		$str .= '<div';
		$str .= ' class="input-field col s12">';

		$str .= '<select';
		$str .= ' autocomplete="'.htmlspecialchars($this->getAutocompleteAttribute()).'"';
		$str .= ' id="'.htmlspecialchars($this->getId()).'"';
		$str .= ' data-option-keys="'.htmlspecialchars(json_encode(array_keys($this->getOptions()))).'"';

		if ($this->isRequired()) {
			$str .= ' required="required"';
		}
		
		$str .= ' class="form-field"';
		$str .= ' data-field-type="'.htmlspecialchars(self::class).'"';
		$str .= '>';

		$str .= '<option';
		$str .= ' value=""';
		if (!$this->isFieldPrefilled()) {
			$str .= ' selected="selected"';
		}
		$str .= '>';
		$str .= "Choose an option";
		$str .= '</option>';

		foreach ($this->getOptions() as $val => $text) {
			$str .= '<option';
			if ($this->isFieldPrefilled()) {
				if ($this->getPrefilledValue() == $val) {
					$str .= ' selected="selected"';
				}
			}
			$str .= ' value="'.htmlspecialchars($val).'"';
			$str .= '>';
			$str .= htmlspecialchars($text);
			$str .= '</option>';
		}

		$str .= '</select>';

		$str .= $this->getLabelHtml();

		$str .= '</div>';
		
		return $str;
	}

	/**
	 * Full JS validation code, including if statement and all
	 * 
	 * @return string The JS to validate the field
	 */
	public function getJsValidator() : string {
		return 'if (!(new window.formInputHandlers['.json_encode(self::class).'](document.getElementById('.json_encode($this->getId()).')).verify())) { return; }';
	}

	/**
	 * Return JS code to store the field's value in $formDataName
	 * 
	 * @param string $formDataName The name of the FormData variable
	 * @return string Code to use to store field in $formDataName
	 */
	public function getJsAggregator(string $formDataName) : string {
		return $formDataName.'.append('.json_encode($this->getDistinguisher()).', (new window.formInputHandlers['.json_encode(self::class).'](document.getElementById('.json_encode($this->getId()).')).getAggregationValue()));';
	}

	/**
	 * Check the field's forms on the servers side
	 * 
	 * @param array $requestArr Array to find the form data in
	 */
	public function checkServerSide(?array &$requestArr=null) : void {
		if (is_null($requestArr)) {
			if ($this->getForm()->getMethod() == Form::POST) {
				$requestArr = &$_POST;
			} else {
				$requestArr = &$_GET;
			}
		}
		if (!array_key_exists($this->getDistinguisher(), $requestArr)) {
			$this->throwMissingError();
		}
		if (empty($requestArr[$this->getDistinguisher()])) {
			if ($this->isRequired()) {
				$this->throwMissingError();
			} else {
				return;
			}
		}
		if (!in_array($requestArr[$this->getDistinguisher()], array_keys($this->getOptions()))) {
			$this->throwInvalidError();
		}
	}

	/**
	 * Get the default autocomplete attribute value
	 *
	 * @return string
	 */
	public static function getDefaultAutocompleteAttribute() : string {
		return AutocompleteValues::ON;
	}
}
