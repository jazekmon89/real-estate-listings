<?php

namespace App\Providers\Services;

use Collective\Html\HtmlFacade as Html;
use Carbon\Carbon;
use Illuminate\Support\HtmlString as HtmlString;
use Illuminate\Support\Facades\View;

class Document {

	protected static $title = "";

	protected static $page_title = "";

	protected static $blocks = [];

	protected static $body_class = [];

	protected static $toolbars = [];

	public function getPageLayout() {
		return in_array('layout-box', static::$body_class) ? 'box' : 'grid';
	}

	public function iLink($link, $title, $attr=[], $escape=true) {
		if ($title) {
			return Html::link($link, $title, $attr, null, $escape);
		}
		return $title;
	}

	public function dateFormat($date, $format="m/d/Y") {
		if (!$date) {
            return "";
        }
        if (is_string($date))   {
            $date = Carbon::parse($date);
        }
        if ($date InstanceOf Carbon === false) 
            return "";
        return $date->format($format);
	}

	public function addBodyClass($class) {
		$class = explode(' ', $class);
		foreach($class as $cls) {
			if (!in_array($cls, static::$body_class)) {
				static::$body_class[] = $cls;
			}	
		}
		return $this;
	}

	public function isEmptyGroupBlock($group)
	{
		return !array_has(static::$blocks, $group . 'block');
	}

	public function hasToolbar($group) {
		return (bool)array_get(static::$toolbars, $group);
	}

	public function addToolbar($group, $name, $output="") {
		array_set(static::$toolbars, "$group.$name", $output);
		return $this;
	}

	public function removeToolbar($group, $name) {
		return array_pull(static::$toolbars, "$group.$name");
	}

	public function printToolbars($groupName, $classes="") {
		if ($group = array_get(static::$toolbars, $groupName)) {
			$output = "<ul class='$classes toolbars-$groupName'>";
			foreach($group as $name => $toolbar) {
				$output .= "<li class='toolbar toolbar-$name'>". $toolbar . "</li>";
			}
			print $output .= "</ul>";
		}
	}

	public function getBodyClass($print=true) {
		return $print ? implode(' ', static::$body_class) : static::$body_class;	
	}

	public function setPageTitle($title) {
		static::$page_title = $title;
		return $this;
	}

	public function getPageTitle() {
		return new HtmlString(static::$page_title);
	}

	public function setTitle($title) {
		static::$title = $title;
		return $this;
	}

	// TODO: we should filter html here!!!
	public function getTitle() {
		return static::$title;
	}

	public function addAsset($type, $asset) {
		extract($asset);

		$assets =& static::$blocks[$type];
		$alias = $alias ?: class_basename($file);

		// asset already added
		if (arr_lfind((array)$assets, "alias", $alias) ||
			arr_lfind((array)array_get($assets, 'dependents'), "alias", $alias))
			return $this;

		if ($required) {
			$missing = [];
			foreach((array)$required as $_alias)
				if (false === $key = arr_lkey($assets, 'alias', $_alias)) 
					$missing[] = $_alias;
			#if ($missing) 
				#throw new \Exception("$type(s) ". implode(",", $missing) ." are missing");
			if (!$missing) $assets['dependents'][] = (object)$asset;
		} 
		else $assets[] = (object)$asset;
		return $this;
	}

	public function resetAssets() {
		static::$blocks = [];
	}

	public function addJS($file, $alias="", $required=[], $priority=0) {
		$this->addAsset('js', compact('alias', 'file', 'priority', 'required'));
		return $this;
	}

	public function addCSS($file, $alias="", $required=[], $priority=0) {
		$this->addAsset('css', compact('alias', 'file', 'priority', 'required'));
		return $this;
	}

	public function getAssets($type=null) {

		if (!$type) return static::$blocks;

		$assets = array_get(static::$blocks, $type, []);
		$dependents = array_pull($assets, 'dependents', []);

		arr_usort($assets, 'priority', true);

		if ($dependents) {
			arr_usort($dependents, 'priority', true);
		}

		return array_merge($assets, $dependents);
	}

	public function deleteAsset($type, $alias=null, $recurse=false) {
		if (!$alias) {

			if (isset(static::$blocks[$type]))
				unset(static::$blocks[$type]);

			return $this;
		}

		$assets =& static::$blocks[$type];

		if (false !== $key = arr_lkey($assets, 'alias' , $alias))
			unset($assets[$key]);

		if ($recurse && isset($assets['dependents'])) {
			foreach ($assets['dependents'] as $key => $asset)	{
				if (in_array($alias, (array)$asset->required)) {
					unset($assets['dependents'][$key]);
				}
			}
		}
		return $this;
	}

	public function deleteBlock($group,$alias=null, $recurse=false) {
		
		return $this->deleteAsset($group . "block", $alias, $recurse);
	}

	/**
	* @param tmpl view file
	*/
	public function addBlock($type, $tmpl, $args=[], $alias="", $required=[], $priority=0) {
		if ($tmpl == strip_tags($tmpl)) {
			try {
				$file = View::exists($tmpl) ? view($tmpl)->with($args) : "";
			} catch (Exception $e) {}
		}
		else $file = $tmpl;

		$alias 	= $alias ?: $tmpl;

		$this->addAsset("{$type}block", compact('file', 'alias', 'required', 'priority'));
		return $this;
	}

	public function addCSSBlock($tmpl, $alias="", $args=[], $required=[], $priority=0) {
		return $this->addBlock('css', $tmpl, $args, $alias, $required, $priority);
	}

	public function addJSBlock($tmpl, $alias="", $args=[], $required=[], $priority=0) {
		return $this->addBlock('js', $tmpl, $args, $alias, $required, $priority);
	}

	public function addGroupBlock($group, $tmpl, $alias="", $args=[], $required=[], $priority=0) {
		return $this->addBlock($group, $tmpl, $args, $alias, $required, $priority);
	}

	public function printAssets($type) {
		if (method_exists($this, "print".strtoupper($type)."Assets")) 
			return call_user_func([$this, "print".strtoupper($type)."Assets"]);

		$type .= substr($type, -5, 5) != 'block' ? 'block' : '';

		if($blocks = $this->getAssets($type))
			foreach($blocks as $block)
				$assets[] = $block->file;

		return isset($assets) ? implode("\r", $assets) : "";
	}

	public function printCSSAssets() {
		foreach($this->getAssets('css') as $asset) 
			$assets[] = Html::style($asset->file, ['id' => $asset->alias]);

		return isset($assets) ? implode("\r", $assets) : "";
	}

	public function printJSAssets() {
		foreach($this->getAssets('js') as $asset) 
			$assets[] = Html::script($asset->file, ['id' => $asset->alias]);

		return isset($assets) ? implode("\r", $assets) : "";
	}
}
