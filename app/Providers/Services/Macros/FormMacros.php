<?php

namespace App\Providers\Services\Macros;

use Collective\Html\FormBuilder as FormBuilder;
use Collective\Html\HtmlBuilder as HtmlBuilder;
use Illuminate\Support\HtmlString as HtmlString;
use Illuminate\Support\MessageBag as MessageBag;

function _closure($macro) {
	return (function() use ($macro) {
		return call_user_func_array('App\Providers\Services\Macros\FormMacros::' . $macro, func_get_args());
	});
}

class FormMacros {
	protected static $form = null;
	protected static $html = null;
	protected static $error_bag = null;
	protected static $errors = null;
	protected static $request_data = [];
	protected static $session_data = [];
	protected static $macros = [
		'jLabel', 'jInput', 'jBoolean', 'ErrorBag', 'showInputError', 'getInputError', 'selectMulti', 
		'countIncrements', 'getInputValue', 'request', 'refresh'
	];

	public static function setHtmlBuilder(HtmlBuilder &$html) {
		static::$html =& $html;
	}

	public static function setFormBuilder(FormBuilder &$form) {
		static::$form =& $form;
	}

	public static function register(FormBuilder &$form, HtmlBuilder &$html) {
		static::setFormBuilder($form);
		static::setHtmlBuilder($html);
		foreach(static::$macros as $macro) {
			static::$form->macro($macro,  _closure($macro));
		}
	}

	public static function ErrorBag(MessageBag $bag) {
		static::$error_bag = $bag;
	}
	public static function jLabel($name, $label, $attrs=[], $escape=true) {
		$name = static::transformKey($name);
		$label = call_user_func_array([static::$form, 'label'], [$name, trans($label), $attrs, $escape]);
		if (!$info = array_get($attrs, 'info')) return $label;
		return new HtmlString($label->toHtml() . "<i>".trans($info)."</i>");
	}
	public static function transformKey($key) {
		return str_replace(['.', '[]', '[', ']'], '-', $key);
	}
	public static function jBoolean($name, $options=[], $def=null, $attrs=[]) {
		$options = $options ?: ['Y' => 'Yes', 'N' => 'No'];
		$html 	 = array_pull($attrs, 'before', "<div class='bool-wrapper'>");
		$lbl_cls = array_pull($attrs, 'label', "");
		$rtl 	 = array_pull($attrs, 'rtl');
		$old 	 = static::getInputValue($name, $def);
		$name    = static::generateInputName($name);

		foreach($options as $value => $title) {
			// we must always make sure we generate unique ID here
			// for the label and input to function
			$attrs['id'] = $id = static::transformKey($name.".$value");

			// ? do we need to make this dynamic also ?
			$html .= "<div class='option disp-inline-block h-space-3x'>";

			// generate label & check if previously selected or as default
			$label = static::jLabel($id, $title, ['class' => $lbl_cls ." ".snake_case($title)]);
			$checked = ($old && $old === $value) || (!$old && $def === $value);

			// generate input 
			$input = call_user_func_array([static::$form, 'radio'], [$name, $value, $checked, $attrs]);

			if ($rtl) $html .= $label->toHtml() . $input->toHtml() . "</div>";
			else $html .= $input->toHtml() . $label->toHtml() . "</div>";
		}
		$html .= static::showInputError($name);
		$html .= array_pull($attrs, 'after', "</div>");

		return new HtmlString($html);
	}

	public static function selectMulti($name, $options, $def=[], $attrs=[]) {
		$html 	 = array_pull($attrs, 'before', "<div class='wrapper'>");
		$lbl_cls = array_pull($attrs, 'label', "");
		$rtl 	 = array_pull($attrs, 'rtl');
		$old 	 = (array)static::getInputValue($name);
		$html 	.= static::showInputError($name);

		foreach($options as $value => $title) {
			$iname  = static::generateInputName($name.".{$value}");
			// we must always make sure we generate unique ID here
			// for the label and input to function
			$attrs['id'] = $id = static::transformKey($iname);
			// ? do we need to make this dynamic also ?
			$html 	.= "<div class='option'>";
			$label 	 = static::jLabel($id, $title, ['class' => $lbl_cls ." ".snake_case($title)]);
			$checked = ($old && in_array($value, $old)) || (!$old && in_array($value, $def));
			$input 	 = call_user_func_array([static::$form, 'checkbox'], [$iname, $value, $checked, $attrs]);

			if ($rtl) $html .= $label->toHtml() . $input->toHtml() . "</div>";
			else $html .= $input->toHtml() . $label->toHtml() . "</div>";
		}
		$html .= array_pull($attrs, 'after', "</div>");
		return new HtmlString($html);
	}

	public static function jInput() {
		$args = func_get_args();
		$type = array_shift($args);
		if (in_array($type, ['submit', 'reset', 'file'])) 
			return call_user_func_array([static::$form, $type], $args);
		$valueIndex = 2;$attr_index = 2;
		switch($type) {
			case 'select': $valueIndex = 3; $attr_index = 3; break;
			case 'selectRange':
			case 'selectYear': $valueIndex = 4; $attr_index = 3; break;
			case 'checkbox':
			case 'radio': $attr_index = 3;break;
			case 'file': $attr_index = 1; break;
		}
		// we need to retain dot annotated name to get the 
		// index of the error from the error message bag

		$name  	 = $args[0];
		$args[0] = static::generateInputName($name);

		if (!array_get($args, "{$attr_index}.id"))
			array_set($args, "{$attr_index}.id", static::transformKey($name));		

		$default = array_get($args, $valueIndex - 1);
		if (in_array($type, ['radio', 'checkbox']) 
			&& !empty($args[$valueIndex - 1])
			&& $args[$valueIndex - 1] == static::getInputValue($name)) {
			// check if it was checked or not
			$args[$valueIndex] = true;
		} else if ($type != 'password') $args[$valueIndex - 1] = static::getInputValue($name);

		if (!array_get($args, $valueIndex-1))
			$args[$valueIndex - 1] = $default;
		
		ksort($args);
		// retrieve generated HtmlString
		// we get the string output so that we can append the error message
		// if there's an error we add error class to input
		if ($error = static::showInputError($name)->toHtml()) {
			$cls = array_get($args, "{$attr_index}.class", '');
			array_set($args, "{$attr_index}.class", $cls . ' has-error');					
		}
		$html  = call_user_func_array([static::$form, $type], $args);
		$html  = $html->toHtml();
		$html .= $error;
		return new HtmlString($html);
	}

	public static function showInputError($name) {
		if ($error = static::getInputError($name)) {
			if (is_array($error)) 
				$error = head($error);
			return new HtmlString("<span class='col-md-12 text-left label label-alert'>$error</span>");
		}
		return new HtmlString("");
	}

	public static function session() {
		return session();
	}

	public static function request() {
		return request();
	}

	public static function refresh()
	{
		static::$errors = static::$session_data = static::$request_data = null;
	}

	public static function getRequestData() {
		return static::$request_data = static::$request_data ?: static::request()->all();
	}

	public static function getSessionData() {
		return static::$session_data = static::$session_data ?: static::session()->all();
	}

	public static function countIncrements($key, $empty=1) {
		$data = count((array)static::getInputValue($key));

		return $data ?: $empty;
	}

	public static function getInputValue($name, $default=null) {
		if (!$val = array_get(static::getRequestData(), $name)) 
			$val = array_get(static::getSessionData(), $name);
		// make sure null so that Collective will understand 
		// we didn't set default value
		return $val ?: $default;
	}	

	public static function generateInputName($str) {
		$depths = explode('.', $str);
		$str = array_shift($depths);
		foreach($depths as $depth) $str .= '['. $depth . ']';
		return $str;
	}

	public static function generateInputNameMultiple($name) {
		return static::generateInputName($name) . '[]';
	}

	public static function getMessageBag() {
		if (static::$error_bag && static::$error_bag InstanceOf MessageBag)
			return static::$error_bag;
		return static::$error_bag = new MessageBag();
	}

	public static function getErrors() {
		return static::$errors = static::$errors ?: (array)static::getMessageBag()->toArray();
	}

	public static function getInputError($name) {
		return array_get(static::getErrors(), (array_has(static::getErrors(), 'errors') ? 'errors.' . $name : $name));
	} 
}