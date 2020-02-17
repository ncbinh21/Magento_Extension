<?php
namespace Forix\Megamenu\Rewrite\Ves\Megamenu\Helper;

class Editor extends \Ves\Megamenu\Helper\Editor{

    public function prepareFields() {
        $categoryList = $this->storeCategories->getCategoryList();

        $this->addField("label1", [
            'label' => __('General Information'),
            'type'  => 'fieldset'
        ]);

        $this->addField("name", [
            'label' => __('Name'),
            'type'  => 'text'
        ]);

        $this->addField("classes", [
            'label' => __('Classes'),
            'type'  => 'text'
        ]);

        $this->addField("link_type", [
            'label'  => __('Link Type'),
            'type'   => 'select',
            'values' => $this->_linkType
        ]);

        $this->addField("link", [
            'label'  => __('Custom Link'),
            'type'   => 'text',
            'note'   => __('Enter hash (#) to make this item not clickable in the top main menu. '),
            'depend' => [
                'field' => 'link_type',
                'value' => 'custom_link'
            ]
        ]);

        $this->addField("category", [
            'label'  => __('Category'),
            'type'   => 'select',
            'values' => $categoryList,
            'depend' => [
                'field' => 'link_type',
                'value' => 'category_link,attribute_link'
            ]
        ]);
        $this->addField("attribute_code", [
            'label'  => __('Atribute'),
            'type'   => 'select',
            'depend' => [
                'field' => 'link_type',
                'value' => 'attribute_link'
            ]
        ]);
        $this->addField("attribute_value", [
            'label'  => __('Options'),
            'type'   => 'select',
            //'values' => array(),
            'depend' => [
                'field' => 'link_type',
                'value' => 'attribute_link'
            ]
        ]);

        $this->addField("target", [
            'label'  => __('Link Target'),
            'type'   => 'select',
            'value'  => '_self',
            'values' => $this->_linkTarget,
            'depend' => [
                'field' => 'link_type',
                'value' => 'category_link,custom_link'
            ]
        ]);

        $this->addField("show_icon", [
            'label'  => __('Show Icon'),
            'type'   => 'select',
            'value'  => 0,
            'values' => $this->_yesno
        ]);

        $this->addField("icon", [
            'label'  => __('Icon'),
            'type'   => 'image',
            'depend' => [
                'field' => 'show_icon',
                'value' => 1
            ]
        ]);

        $this->addField("hover_icon", [
            'label'  => __('Hover Icon'),
            'type'   => 'image',
            'depend' => [
                'field' => 'show_icon',
                'value' => 1
            ]
        ]);

        $this->addField("icon_position", [
            'label'  => __('Icon Position'),
            'type'   => 'select',
            'values' => $this->_iconPosition,
            'depend' => [
                'field' => 'show_icon',
                'value' => 1
            ]
        ]);

        $this->addField("icon_classes", [
            'label'  => __('Icon Classes'),
            'type'   => 'text',
            'depend' => [
                'field' => 'show_icon',
                'value' => 1
            ]
        ]);

        $this->addField("status", [
            'label'  => __('Status'),
            'type'   => 'select',
            'values' => $this->_yesno
        ]);

        $this->addField('image_hover',[
			'label'  => __('Image Hover'),
			'type'   => 'image',
		]);

        $this->addField('short_desc',[
			'label' => __('Short Description'),
			'type'  => 'editor'
		]);

        /*
		$this->addField("label3", [
			'label' => __('Left Block'),
			'type'  => 'fieldset'
		]);

		$this->addField("show_left_sidebar", [
			'label'  => __('Enabled'),
			'type'   => 'select',
			'value'  => 0,
			'values' => $this->_yesno
		]);

		$this->addField("left_sidebar_width", [
			'label' => __('Width'),
			'type'  => 'text',
			'depend' => [
				'field' => 'show_left_sidebar',
				'value' => 1
			]
		]);

		$this->addField("left_sidebar_html", [
			'label' => __('HTML'),
			'type'  => 'editor',
			'depend' => [
				'field' => 'show_left_sidebar',
				'value' => 1
			]
		]);
        */


        $this->addField("label5", [
            'label' => __('Right Block'),
            'type'  => 'fieldset'
        ]);


        $this->addField("show_right_sidebar", [
            'label'  => __('Enabled'),
            'value'  => 0,
            'type'   => 'select',
            'values' => $this->_yesno
        ]);

        $this->addField("right_sidebar_width", [
            'label' => __('Width'),
            'type'  => 'text',
            'depend' => [
                'field' => 'show_right_sidebar',
                'value' => 1
            ]
        ]);

        $this->addField("right_sidebar_html", [
            'label' => __('HTML'),
            'type'  => 'editor',
            'depend' => [
                'field' => 'show_right_sidebar',
                'value' => 1
            ]
        ]);
    }

	public function renderCellTemplate($fieldName){
		$fields = $this->getFields();
		$inputName = $this->_getCellInputElementName($fieldName);
		$field = $fields[$fieldName];
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$id = 'option-'.str_replace("_", "-", $fieldName);
		$classes = 'ves-'.$id;

		if(isset($field['type'])){
			$html = '';
			switch ($field['type']) {
				case 'textarea':
					$html = '<textarea id="'.$id.'" class="'.$classes.'"  data-bind="value: '.$fieldName.'"></textarea>';
					$html .= '<div class="field-cm">'.(isset($field['note'])?$field['note']:'').'</div>';
					break;
				case 'text':
					$html = '<input type="text" id="'.$id.'" class="'.$classes.'" data-bind="value: '.$fieldName.'"/>';
					$html .= '<div class="field-cm">'.(isset($field['note'])?$field['note']:'').'</div>';
					break;
				case 'select':
					if($fieldName == 'link_type'){
						$dataBind = "selectedOptions: selectedLinkType,
                                     value: {$fieldName}";
					}elseif($fieldName == 'attribute_code'){
						$dataBind = "options: attributes,
                                     optionsText: 'name',
                                     optionsValue: 'value',
                                     value: {$fieldName}";
					}elseif($fieldName == 'attribute_value'){
						$dataBind = "options: options,
                                     optionsText: 'name',
                                     optionsValue: 'value',   
                                     value: {$fieldName}";
					}else{
						$dataBind = 'value: '.$fieldName;
					}
					$html = '<select id="'.$id.'" class="'.$classes.'" data-bind="'.$dataBind.'">';
					if(isset($field['values'])){
						foreach ($field['values'] as $option) {
							$html .= $this->_optionToHtml($option);
						}
					}
					$html .= '</select>';
					$html .= '<div class="field-cm">'.(isset($field['note'])?$field['note']:'').'</div>';
					break;
				case 'image':
					$editorId = 'editor'.time().rand();
					$html = '<div class="preview-image">';
					$html .= '<img data-bind="attr:{src: '.$fieldName.'}" />';
					$html .= '</div>';
					$html .= '<div class="input-media">';
					$html .= '<input data-bind="{value: '.$fieldName.'}" class="'.$classes.'" id="'.$editorId.'" type="text"/>';

					$html .= $this->_layout->createBlock(
						'Magento\Backend\Block\Widget\Button',
						'',
						[
							'data' => [
								'label' => __('Insert Image'),
								'type' => 'button',
								'class' => 'action-wysiwyg',
								'onclick' => "VesMediabrowserUtility.openDialog('" . $this->_backendData->getUrl('cms/wysiwyg_images/index',
										[
											'target_element_id'=>$editorId,
											'as_is' => 'ves'
										]
									) . "', null, null,'" . $this->escaper->escapeQuote(
										__('Upload Image'),
										true
									) . "', '" . '' . "');",
							]
						]
					)->toHtml();
					$html .= '</div>';
					$html .= '<div class="field-cm">'.(isset($field['note'])?$field['note']:'').'</div>';
					break;
				case 'editor':
					$tinyMCEConfig = json_encode($this->_wysiwygConfig->getConfig());
					$editorId = 'editor'.time().rand();
					$html = '<textarea id="'.$editorId.'" data-key=' . $fieldName . ' class="'.$classes.' ves-editor" style="height:400px;"  data-bind="{value: '.$fieldName.', if: status==1}" data-ui-id="product-tabs-attributes-tab-fieldset-element-textarea-'.$editorId.' aria-hidden="true"></textarea>';
					$html .= $this->_layout->createBlock(
						'Magento\Backend\Block\Widget\Button',
						'',
						[
							'data' => [
								'label' => __('WYSIWYG Editor'),
								'type' => 'button',
								'class' => 'action-wysiwyg',
								'style' => 'margin-top: 10px;',
								'onclick' => 'megamenuWysiwygEditor.open(\'' . $this->_backendData->getUrl(
										'vesmegamenu/product/wysiwyg'
									) . '\', \''.$editorId.'\' , ' . json_encode($tinyMCEConfig) . ')',
							]
						]
					)->toHtml();
					$html .= '<div class="field-cm">'.(isset($field['note'])?$field['note']:'').'</div>';
					break;
				case 'separator':
					$html = '<div class="separator"></div>';
					$html .= '<div class="field-cm">'.(isset($field['note'])?$field['note']:'').'</div>';
					break;
				case 'color':
					$id = 'option-'.time().rand();
					$html = '<input class="ip-color" type="text" class="'.$classes.'" id="'.$id.'"  data-bind="value: '.$fieldName.'"/>';
					$mcPath = $mediaUrl.'ves/megamenu';
					$html .= '<script>
                require([
                "jquery",
                "Ves_Megamenu/js/mcolorpicker/mcolorpicker.min"
                ], function ($) {
                    jQuery(document).ready(function($){
                        var folderImageUrl = "'.$mcPath.'/images";
                        jQuery.noConflict();
                        jQuery.fn.mColorPicker.init.replace = false;
                        jQuery.fn.mColorPicker.defaults.imageFolder = "'. $mcPath .'/images/";
                        jQuery.fn.mColorPicker.init.allowTransparency = true;
                        jQuery.fn.mColorPicker.init.showLogo = false;
                        jQuery("#' . $id . '").attr("data-hex", true).width("250px").mColorPicker().change(function(){  });
                        jQuery("#mColorPickerImg").css("background-image","url('.$mcPath.'/images/picker.png)");
                        jQuery("#mColorPickerFooter").css("background-image","url('.$mcPath.'/images/grid.gif)");
                        jQuery("#mColorPickerFooter img").attr({"src":"url('.$mcPath.'/images/meta100.png)"});
                        jQuery(document).on("click", "#'.$id.'", function(){
                            jQuery("#icp_'. $id .' img").trigger("click");
                        });
                        
                        jQuery(document).on("change", "#'.$id.'", function(){
                            var value = jQuery(this).val();
                            if(value == "transparent"){
                                jQuery(this).css("color", "#000");
                            }
                        }).change();
                    });
                });</script>';
					$html .= '<div class="field-cm">'.(isset($field['note'])?$field['note']:'').'</div>';
					break;
			}
			if($html) return $html;
		}
		return '<input type="text"  id="'.$this->_getCellInputElementId('<%- _id %>', $fieldName).'" name="'.$inputName.'" class="' .(isset($field['class']) ? $field['class'] : 'input-text') . '" ' . (isset($field['style']) ? ' style="' . $field['style'] . '"' : '') . ' />';
	}
}