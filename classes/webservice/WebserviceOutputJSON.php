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
class WebserviceOutputJSONCore implements WebserviceOutputInterface
{
    public function getContentType()
    {
        return 'application/json';
    }

    /**
     * Main function used to render node in desired format
     *
     * @param ApiNode $apiNode
     * @param int $type_of_view Use constants WebserviceOutputBuilderCore::VIEW_DETAILS / WebserviceOutputBuilderCore::VIEW_LIST
     *
     * @return string json-encoded string
     */
    public function renderNode($apiNode)
    {
        if ($apiNode->getType() == ApiNode::TYPE_LIST) {
            $jsonArray = [$apiNode->getName() => $this->toJsonArray($apiNode)];
        } else {
            $jsonArray = $this->toJsonArray($apiNode);
        }

        return json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Transform tree structure of desired node to array, suitable for JSON output.
     * JSON output completely ignores attributes - those are used just in XML output.
     *
     * @param ApiNode $apiNode
     *
     * @return array|string Node type ApiNode::TYPE_NODE returns just value as string.
     *                      Node type ApiNode::TYPE_PARENT returns recursive array of underlying nodes in the form of [Name => [... subnodes ...]]
     *                      Node type ApiNode::TYPE_LIST returns recursive array of underlying nodes in the form of [[... subnodes ...]]
     */
    private function toJsonArray($apiNode)
    {
        switch ($apiNode->getType()) {
            case ApiNode::TYPE_VALUE:
                return $this->getValueWithAttributes($apiNode);
            case ApiNode::TYPE_LANGUAGE:
                $out = [];
                foreach ($apiNode->getNodes() as $node) {
                    /* @var $node ApiNode */
                    $langId = $node->getAttributes()['id'];
                    $out[] = ['id' => $langId, 'value' => $node->getValue()];
                }

                return $out;
            case ApiNode::TYPE_LIST:
                $out = [];
                foreach ($apiNode->getNodes() as $node) {
                    $out[] = $this->toJsonArray($node);
                }
                $this->injectAttributesIntoJson($out, $apiNode->getAttributes());

                return $out;
            case ApiNode::TYPE_PARENT:
                $out = [];
                foreach ($apiNode->getNodes() as $node) {
                    $out[$node->getName()] = $this->toJsonArray($node);
                }

                $this->injectAttributesIntoJson($out, $apiNode->getAttributes());

                return $out;
        }
    }

    /**
     * Inject attributes into output array
     *
     * @param array $out
     * @param array $attributes
     *
     * @return void
     */
    private function injectAttributesIntoJson(&$out, $attributes)
    {
        if (empty($attributes) || !is_array($attributes)) {
            return;
        }

        foreach ($attributes as $name => $value) {
            $name = $name == 'xlink:href' ? 'href' : $name; //remove namespace from common attribute xlink:href
            if (array_key_exists($name, $out)) {//if output array coincidentally already contains this key, avoid rewriting its value - attributes has lesser priority
                continue;
            }

            $out[$name] = $value;
        }
    }

    /**
     * Get value from node with injected attributes.
     * If $apiNode value is null, output is changed from simple value into array with attributes.
     *
     * @param ApiNode $apiNode
     *
     * @return mixed
     */
    private function getValueWithAttributes($apiNode)
    {
        if (!empty($apiNode->getValue()) || empty($apiNode->getAttributes())) {   //if ApiNode contains any value or there is no attribute to output instead, just return the value itself
            return $apiNode->getValue();
        }

        $out = [];  //in situations, where there are attributes, but no values, return rather array of attributes to have at least some output
        $this->injectAttributesIntoJson($out, $apiNode->getAttributes());

        return $out;
    }
}
