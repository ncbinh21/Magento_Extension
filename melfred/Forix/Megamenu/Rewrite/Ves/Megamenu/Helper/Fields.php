<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: html
 */
namespace Forix\Megamenu\Rewrite\Ves\Megamenu\Helper;

class Fields extends \Magento\Framework\App\Helper\AbstractHelper {
	public function getFields(){
		return [
			'label1' => [
				'type' => 'fieldset'
			],

			'name' => [
				'type' => 'text'
			],

			'classes' => [
				'type' => 'text'
			],

			'link_type' => [
				'type' => 'select'
			],

			'link' => [
				'type' => 'text'
			],

			'category' => [
				'type' => 'select'
			],

			'target' => [
				'type' => 'select'
			],

			'show_icon' => [
				'type' => 'select'
			],

			'icon' => [
				'type' => 'image'
			],

			'hover_icon' => [
				'type' => 'image'
			],

			'icon_position' => [
				'type' => 'select'
			],

			'icon_classes' => [
				'type' => 'text'
			],

			'is_group' => [
				'type' => 'select'
			],

			'disable_bellow' => [
				'type' => 'text'
			],

			'status' => [
				'type' => 'select'
			],

			'label8' => [
				'type' => 'fieldset'
			],

			'sub_width' => [
				'type' => 'text'
			],

			'animation_in' => [
				'type' => 'select'
			],

			'animation_time' => [
				'type' => 'text'
			],

			'align' => [
				'type' => 'select'
			],

			'dropdown_bgcolor' => [
				'type' => 'color'
			],

			'dropdown_bgimage' => [
				'type' => 'image'
			],

			'dropdown_bgimagerepeat' => [
				'type' => 'select'
			],

			'dropdown_bgpositionx' => [
				'type' => 'text'
			],

			'dropdown_bgpositiony' => [
				'type' => 'text'
			],

			'dropdown_inlinecss' => [
				'type' => 'textarea'
			],

			'label2' => [
				'type' => 'fieldset'
			],

			'show_header' => [
				'type' => 'select'
			],

			'header_html' => [
				'type' => 'editor'
			],

			'label3' => [
				'type' => 'fieldset'
			],

			'show_left_sidebar' => [
				'type' => 'select'
			],

			'left_sidebar_width' => [
				'type' => 'text'
			],

			'left_sidebar_html' => [
				'type' => 'editor'
			],

			'label4' => [
				'type' => 'fieldset'
			],

			'show_content' => [
				'type' => 'select'
			],

			'content_width' => [
				'type' => 'text'
			],

			'content_type' => [
				'type' => 'select'
			],

			'parentcat' => [
				'type' => 'select'
			],

			'child_col' => [
				'type' => 'select'
			],

			'content_html' => [
				'type' => 'editor'
			],

			'label5' => [
				'type' => 'fieldset'
			],

			'show_right_sidebar' => [
				'type' => 'select'
			],

			'right_sidebar_width' => [
				'type' => 'text'
			],

			'right_sidebar_html' => [
				'type' => 'editor'
			],

			'label6' => [
				'type' => 'fieldset'
			],

			'show_footer' => [
				'type' => 'select'
			],

			'footer_html' => [
				'type' => 'editor'
			],

			'menu_id' => [
				'type' => 'text'
			],

			'item_id' => [
				'type' => 'text'
			],

			'label7' => [
				'type' => 'fieldset'
			],

			'color' => [
				'type' => 'color'
			],

			'hover_color' => [
				'type' => 'color'
			],

			'bg_color' => [
				'type' => 'color'
			],

			'bg_hover_color' => [
				'type' => 'color'
			],

			'inline_css' => [
				'type' => 'textarea'
			],

			/*--- Custom Fields Added By Forix ---*/
			'attribute_code' => [
				'type' => 'select'
			],

			'attribute_value' => [
				'type' => 'select'
			]
			/*------------------------------------*/
		];
	}
}