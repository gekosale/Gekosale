<?php

namespace SimpleForm;
use Gekosale;
use Gekosale\App as App;

class Form
{

    protected $Data;

    public function AddChild ($child)
    {
        $this->form['children'][$child->GetName()] = $child;
        $this->form['children'][$child->GetName()]->attributes['id'] = $this->form['name'] . '_' . $child->GetName();
        return $child;
    }

    public function __construct (Array $Data)
    {
        $Data['submit_name'] = $Data['name'] . '_submitted';
        $this->form = $Data;
        $this->container = App::getContainer();
        
        $this->AddChild(new Elements\TextField(array(
            'name' => '__csrf',
            'rules' => array(
                new Rules\Required($this->container->get('translation')->trans('ERR_CSRF')),
                new Rules\Custom($this->container->get('translation')->trans('ERR_CSRF'), array(
                    App::getModel('csrfprotection'),
                    'isValid'
                ))
            )
        )));
        
        $this->Populate(array(
            '__csrf' => App::getModel('csrfprotection')->getCode()
        ));
    }

    public function getForm ()
    {
        $this->form['javascript'] = $this->getJavascript();
        return $this->form;
    }

    public function isSubmitted ()
    {
        return isset($_POST[$this->form['submit_name']]) && $_POST[$this->form['submit_name']] != '';
    }

    public function getSubmitValues ()
    {
        $values = array();
        foreach ($this->form['children'] as $child){
            $values[$child->GetName()] = isset($_POST[$child->GetName()]) ? $_POST[$child->GetName()] : "";
        }
        
        $values = App::getModel('formprotection')->filterArray($values);
        
        return $values;
    }

    public function Populate ($values)
    {
        foreach ($this->form['children'] as $child){
            if (isset($values[$child->GetName()])){
                $child->value = $values[$child->GetName()];
            }
        }
    }

    public function Validate ()
    {
        if (! $this->isSubmitted()){
            return false;
        }
        
        $values = $this->getSubmitValues(false);
        
        $this->Populate($values);
        
        $valid = true;
        foreach ($this->form['children'] as $child){
            if (! $child->Validate($values[$child->GetName()])){
                $valid = false;
            }
        }
        
        return $valid;
    }

    protected function getJavascript ()
    {
        $render = "
		<script type=\"text/javascript\">
			GCore.OnLoad(function() {
				$('#{$this->form['name']}').validate({
					errorElement: 'span',
					errorClass: 'error',
					wrapClass: 'help-block',
					rules: {$this->renderRules()},
					messages: {$this->renderMessages()},
					highlight: function(label) {
						$(label).addClass('invalid').closest('.control-group').addClass('error');
					},
					success: function(label) {
						label.addClass('valid').closest('.control-group').removeClass('error');
					}
				});
			});
		</script>
		";
        
        return $render;
    }

    protected function renderRules ()
    {
        $rules = Array();
        foreach ($this->form['children'] as $child){
            $rule = $child->FormatRulesJS();
            if (! empty($rule)){
                $rules[$child->GetName()] = $rule;
            }
        }
        return json_encode($rules);
    }

    protected function renderMessages ()
    {
        $messages = Array();
        foreach ($this->form['children'] as $child){
            $message = $child->FormatMessagesJS();
            if (! empty($message)){
                $messages[$child->GetName()] = $message;
            }
        }
        return json_encode($messages);
    }
}