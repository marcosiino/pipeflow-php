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
            // ✅ Use DOMXPath to select only the top-level <stage> elements
            $xpath = new \DOMXPath($document);
            $stages = $xpath->query('/pipeline/stages/stage');

            foreach ($stages as $stageNode) {
                $this->processStage($stageNode, $document);
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
     * Parses and configures a single <stage> element
     *
     * @throws StageConfigurationException
     */
    private function processStage(\DOMElement $stage, \DOMDocument $document): void
    {
        $stageConfiguration = new StageConfiguration();
        $stageType = $stage->getAttribute("type");

        // ✅ Use XPath relative to this stage node
        $xpath = new \DOMXPath($document);
        $params = $xpath->query('./settings/param', $stage);

        foreach ($params as $param) {
            $paramName = $param->getAttribute("name");
            $subItems = $xpath->query('./item', $param);

            // Reference parameter (contextReference)
            if ($contextReferenceType = $param->getAttribute("contextReference")) {
                if ($subItems->length > 0) {
                    throw new StageConfigurationException(
                        "Reference parameters (param with contextReference attribute) cannot have <item></item> sub elements"
                    );
                }

                $keypath = $param->getAttribute("keypath");
                $type = $this->getReferenceTypeFromTypeAttribute($contextReferenceType);

                $stageConfiguration->addSetting(
                    new ReferenceStageSetting($type, $paramName, trim($param->nodeValue), $keypath)
                );
            }
            // Fixed value parameter
            else {
                if ($subItems->length > 0) {
                    // Parameter is an array
                    $paramArray = [];
                    foreach ($subItems as $item) {
                        $paramArray[] = trim($item->nodeValue);
                    }
                    $stageConfiguration->addSetting(new StageSetting($paramName, $paramArray));
                } else {
                    // Single value parameter
                    $stageConfiguration->addSetting(new StageSetting($paramName, trim($param->nodeValue)));
                }
            }
        }

        // Instantiate and add stage to pipeline
        $stageInstance = StageFactory::instantiateStageOfType($stageType, $stageConfiguration);
        $this->pipeline->addStage($stageInstance);
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