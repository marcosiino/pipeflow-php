<?php
namespace Marcosiino\Pipeflow\Utils;

use Marcosiino\Pipeflow\Core\Pipeline;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageSetting;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSetting;
use Marcosiino\Pipeflow\Exceptions\StageConfigurationException;
use Marcosiino\Pipeflow\Core\StageFactory;
use Marcosiino\Pipeflow\Core\StageConfiguration\ReferenceStageSettingType;

/**
 *
 */
class PipelineXMLConfigurator
{
    /**
     * @var Pipeline
     */
    private Pipeline $pipeline;

    /**
     * @param Pipeline $pipeline
     */
    public function __construct(Pipeline $pipeline) {
        $this->pipeline = $pipeline;
    }

    /**
     * @param $xmlConfiguration
     * @return bool
     * @throws StageConfigurationException
     */
    public function configure($xmlConfiguration): bool {
        $document = new \DOMDocument();
        $document->loadXML($xmlConfiguration);

        //Validates the xml configuration
        $errors = array();
        if($this->validateXMLConfiguration($document, $errors) === true) {
            $allStages = $document->documentElement->getElementsByTagName("stage");
            foreach($allStages as $stage) {
                $this->processStage($stage, $document);
            }
        } else {
            //TODO: don't print the errors, propagate them someway (exception?)
            print("XML Configuration Validation errors:");
            print_r($errors);
            return false;
        }
        return true;
    }

    /**
     * @throws StageConfigurationException
     */
    private function processStage(\DOMElement $stage, \DOMDocument $document): void
    {
        $stageConfiguration = new StageConfiguration();

        $stageType = $stage->getAttribute("type");
        $params = $stage->getElementsByTagName("param");
        foreach ($params as $param) {
            $paramName = $param->getAttribute("name");
            $subItems = $param->getElementsByTagName("item");

            // Setting Parameter which references to the value of a Context Parameter.
            if($contextReferenceType = $param->getAttribute("contextReference"))
            {
                if(count($subItems) > 0) {
                    throw new StageConfigurationException("reference parameters (param with contextReference attribute) cannot have <item></item> sub elements");
                }
                $keypath = $param->getAttribute("keypath");
                $type = $this->getReferenceTypeFromTypeAttribute($contextReferenceType);

                $stageConfiguration->addSetting(new ReferenceStageSetting($type, $paramName, $param->nodeValue, $keypath));
            }
            // Fixed Setting Parameter
            else {
                //If the param element has <item> sub elements, it means it is an array parameter
                if(count($subItems) > 0) {
                    $paramArray = array();
                    foreach ($subItems as $item) {
                        $paramArray[] = $item->nodeValue;
                    }
                    $stageConfiguration->addSetting(new StageSetting($paramName, $paramArray));
                }
                // Otherwise it is a single value parameter
                else {
                    $stageConfiguration->addSetting(new StageSetting($paramName, $param->nodeValue));
                }
            }
        }

        $stage = StageFactory::instantiateStageOfType($stageType, $stageConfiguration);
        $this->pipeline->addStage($stage);
    }

    /**
     * Validates the loaded document xml content against the custom XML Schema Definition
     *
     * @param DOMDocument $document - The document, with the pipeline xml configuration loaded previously
     * @param array $validationErrors output if validation errors occurs
     * @return bool - Returns true if the document validates successfully, false otherwise
     */
    private function validateXMLConfiguration(\DOMDocument $document, array &$validationErrors): bool {
        libxml_use_internal_errors(true);
        $result = $document->schemaValidate(__DIR__ .  '/pipeline_schema_definition.xsd');
        if($result === false) {
            $validationErrors = array();
            $errors = libxml_get_errors();
            foreach($errors as $error) {
                $validationErrors[] = $error->message;
            }
        }
        libxml_use_internal_errors(false);

        return $result;
    }

    /**
     * Returns the ReferenceStageSettingType associated with the specified type passed as argument or `plain` if there isn't a ReferenceStageSettingType which matches the given argument.
     *
     * @param string $typeAttributeValue - The type attribute value of a referenced param in the xml configuration
     * @return ReferenceStageSettingType
     */
    private function getReferenceTypeFromTypeAttribute(string $typeAttributeValue): ReferenceStageSettingType {
        foreach (ReferenceStageSettingType::cases() as $case) {
            if($case->value == $typeAttributeValue) {
                return $case;
            }
        }

        return ReferenceStageSettingType::plain;
    }
}