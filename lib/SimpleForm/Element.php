<?php

namespace SimpleForm;

class Element
{
	public $value;
	public $attributes;
	public $errors;

	public function GetValue ()
	{
		return $this->value;
	}

	public function GetId ()
	{
		return $this->attributes['id'];
	}

	public function GetName ()
	{
		return $this->attributes['name'];
	}

	public function Validate ($value)
	{
		foreach ($this->attributes['rules'] as $rule){
			if (! $rule->_Check($value)){
				$this->errors[] = $rule->GetFailureMessage();
			}
		}
		return empty($this->errors);
	}

	public function FormatRulesJS ()
	{
		$rules = Array();
		foreach ($this->attributes['rules'] as $rule){
			
			if ($rule instanceof Rules\Required){
				$rules['required'] = true;
			}
			
			if ($rule instanceof Rules\MinLength){
				$rules['minlength'] = $rule->getLength();
			}
			
			if ($rule instanceof Rules\RequiredDependency){
				$rules['required'] = $rule->FormatDependencyJS();
			}
			
			if ($rule instanceof Rules\Email){
				$rules['email'] = true;
			}
			
			if ($rule instanceof Rules\Compare){
				$rules['equalTo'] = '#' . $rule->getComparedField()->GetId();
			}
		}
		
		return $rules;
	}

	public function FormatMessagesJS ()
	{
		$messages = Array();
		foreach ($this->attributes['rules'] as $rule){
			
			if ($rule instanceof Rules\Required){
				$messages['required'] = $rule->GetFailureMessage();
			}
			
			if ($rule instanceof Rules\MinLength){
				$messages['minlength'] = $rule->GetFailureMessage();
			}
			
			if ($rule instanceof Rules\RequiredDependency){
				$messages['required'] = $rule->GetFailureMessage();
			}
			
			if ($rule instanceof Rules\Email){
				$messages['email'] = $rule->GetFailureMessage();
			}
			
			if ($rule instanceof Rules\Compare){
				$messages['equalTo'] = $rule->GetFailureMessage();
			}
		}
		return $messages;
	}
}