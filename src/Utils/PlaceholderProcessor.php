<?php
namespace marcosiino\pipeflow\Utils\Helpers;

use marcosiino\pipeflow\Core\PipelineContext;
use marcosiino\pipeflow\Utils\Parser\InputParser

/**
 * Process a string by replacing the placeholders with the context parameters values
 */

class PlaceholderProcessor
{
    private PipelineContext $context;

    public function __construct(PipelineContext $context) {
        $this->context = $context;
    }

    public function process(string $text): String
    {
        $placeholders = InputParser::extractElements($text);
        do {
            foreach ($placeholders as $placeholder) {
                if ($placeholder->elementType == ParsedElementType::placeholder) {
                    $value = $this->getValueForPlaceholder($placeholder);
                    $text = str_replace($placeholder->fullElementMatch, $value, $text);
                }
            }
            $placeholders = InputParser::extractElements($text);
        } while(count($placeholders) > 0); //Repeat until no more placeholder to replace

        return $text;
    }

    private function getValueForPlaceholder(ParsedElement $placeholder): string {
        switch($placeholder->elementSubType) {
            case ParsedElementSubType::plain:
                return (string)$this->context->getParameter($placeholder->elementName);
            case ParsedElementSubType::indexed:
                $param = $this->context->getParameter($placeholder->elementName);
                if(!is_array($param)) {
                    return "";
                }
                $index = $placeholder->index;
                if($index >= 0 && $index < count($param)) {
                    return (string)$param[$index];
                }
                else {
                    return "";
                }
        }
        return "";
    }
}