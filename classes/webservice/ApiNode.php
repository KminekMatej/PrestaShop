<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class ApiNode
{
    public const TYPE_VALUE = 'value';
    public const TYPE_LANGUAGE = 'language';
    public const TYPE_PARENT = 'parent';
    public const TYPE_LIST = 'list';

    public static $languages;
    private $type;
    private $name;
    private $value;
    private $attributes;
    private $nodes;

    private function __construct($type, $name = null, $value = null, $attributes = [], $nodes = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->nodes = $nodes;
    }

    /**
     * Create new ApiNode instance of type "value"
     *
     * @param string $name
     * @param string $value
     *
     * @return \ApiNode
     */
    private static function value($name, $value = null)
    {
        return new ApiNode(self::TYPE_VALUE, $name, $value);
    }

    /**
     * Create new ApiNode instance of type "lang"
     * Lang ApiNode serves as ApiNode with name. All the translated values are supposed to be children of that Node.
     *
     * @param string $name
     *
     * @return \ApiNode
     */
    private static function lang($name)
    {
        return new ApiNode(self::TYPE_LANGUAGE, $name);
    }

    /**
     * Create new ApiNode instance of type "parent"
     * Parent ApiNode serves as ApiNode with name and array of child nodes (and potentionally array of attributes)
     * Its children nodes are meant to be rendered as associative arrays
     *
     * @param string $name
     * @param array $attributes
     *
     * @return \ApiNode
     */
    public static function parent($name = null, $attributes = [])
    {
        return new ApiNode(self::TYPE_PARENT, $name, null, $attributes);
    }

    /**
     * Create new ApiNode instance of type "list"
     * List ApiNode serves as ApiNode with name and array of child nodes.
     * Its children nodes are meant to be rendered as non-associative arrays
     *
     * @param string $name
     * @param array $attributes
     *
     * @return \ApiNode
     */
    public static function list($name = null, $attributes = [])
    {
        return new ApiNode(self::TYPE_LIST, $name, null, $attributes);
    }

    /** @return string */
    public function getType()
    {
        return $this->type;
    }

    /** @return string */
    public function getName()
    {
        return $this->name;
    }

    /** @return string */
    public function getValue()
    {
        return $this->value;
    }

    /** @return array|null */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /** @return array|null */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return self
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param array $nodes
     *
     * @return self
     */
    public function setNodes($nodes)
    {
        $this->nodes = $nodes;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Appends $node as child to current ApiNode
     *
     * @param ApiNode $node
     *
     * @return ApiNode self
     */
    public function addApiNode($node)
    {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * Create new ApiNode of type "value" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param mixed $value
     *
     * @return ApiNode Created child node
     */
    public function addNode($name, $value = null)
    {
        $newNode = self::value($name, $value);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Create new ApiNode of type "lang" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param array $values
     *
     * @return ApiNode Created child node
     */
    public function addLanguageNode($name, $values)
    {
        $newNode = self::lang($name, $values);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Create new ApiNode of type "parent" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param array|null $attributes
     *
     * @return ApiNode Created child node
     */
    public function addParentNode($name = null, $attributes = [])
    {
        $newNode = self::parent($name, $attributes);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Create new ApiNode of type "list" and appends it as child to current ApiNode
     *
     * @param string $name
     * @param array|null $attributes
     *
     * @return ApiNode Created child node
     */
    public function addListNode($name = null, $attributes = [])
    {
        $newNode = self::list($name, $attributes);
        $this->nodes[] = $newNode;

        return $newNode;
    }

    /**
     * Transform $field array into ApiNode and appends it as child to current node
     *
     * @param array $field
     *
     * @return ApiNode
     */
    public function addField($field)
    {
        $newNode = self::value($field['sqlId']);

        if (isset($field['encode'])) {
            $newNode->addAttribute('encode', $field['encode']);
        }

        if (!empty($field['synopsis_details']) && $this->schemaToDisplay !== 'blank') {
            foreach ($field['synopsis_details'] as $name => $detail) {
                $newNode->addAttribute($name, is_array($detail) ? implode(' ', $detail) : $detail);
            }
        }

        // display i18n fields
        if (isset($field['i18n']) && $field['i18n']) {
            foreach (self::$languages as $language) {
                $langAttributes = ['id' => $language];

                if (isset($field['synopsis_details']) || (isset($field['value']) && is_array($field['value']))) {
                    $langAttributes['xlink:href'] = WebserviceOutputBuilderCore::$wsUrl . 'languages/' . $language;
                    if (isset($field['synopsis_details']) && $this->schemaToDisplay != 'blank') {
                        $langAttributes['format'] = 'isUnsignedId';
                    }
                }

                $newNode->setType(self::TYPE_LANGUAGE);
                $newNode->addNode('language', $field['value'][$language] ?? '')
                    ->setAttributes($langAttributes);
            }
        } else {
            // display not i18n fields value
            if (array_key_exists('xlink_resource', $field) && $this->schemaToDisplay != 'blank') {
                if (!is_array($field['xlink_resource'])) {
                    $xlink = WebserviceOutputBuilderCore::$wsUrl . $field['xlink_resource'] . '/' . $field['value'];
                } else {
                    $xlink = WebserviceOutputBuilderCore::$wsUrl . $field['xlink_resource']['resourceName'] . '/';

                    if (isset($field['xlink_resource']['subResourceName'])) {
                        $xlink .= $field['xlink_resource']['subResourceName'] . '/' . $field['object_id'] . '/';
                    }

                    $xlink .= $field['value'];
                }
                $newNode->addAttribute('xlink:href', $xlink);
            }

            if (isset($field['getter']) && $this->schemaToDisplay != 'blank') {
                $newNode->addAttribute('notFilterable', 'true');
            }

            if (isset($field['setter']) && $field['setter'] == false && $this->schemaToDisplay == 'synopsis') {
                $newNode->addAttribute('read_only', 'true');
            }

            if (array_key_exists('value', $field)) {
                $newNode->setValue($field['value']);
            }
        }

        $this->nodes[] = $newNode;

        return $newNode;
    }
}
