<?php
namespace Marcosiino\Pipeflow\Stages\If;

use Marcosiino\Pipeflow\Core\PipelineContext;
use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\StageDescriptor;

class IfStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Allows to implement an if condition in the pipeline flow. Must contain at least a <then></then> block with the stage to execute when the condition is met. Optionally, it can also contain an <else></else> block with the stages to execute when the condition is not met.";

        // Setup Parameters
        $setupParams = array(
            "leftOperand" => "The left operand of the condition. Can be a fixed value or a context parameter reference.",
            "operator" => "The operator to use for the condition. Possible values: `equal`,`notEqual`, `greater`, `less`, `greaterOrEqual`, `lessOrEqual`, `contains`, `notContains`, `caseInsensitiveContains`, `caseInsensitiveNotContains`.",
            "rightOperand" => "The right operand of the condition. Can be a fixed value or a context parameter reference.",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
        );

        return new StageDescriptor("If", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        $operator = $configuration->getSettingValue("operator", new PipelineContext(), true);
        switch($operator) {
            case "equal":
            case "notEqual":
            case "greater":
            case "less":
            case "greaterOrEqual":
            case "lessOrEqual":
            case "contains":
            case "notContains":
            case "caseInsensitiveContains":
            case "caseInsensitiveNotContains":
                //valid operator
                break;
            default:
                throw new \Marcosiino\Pipeflow\Exceptions\StageConfigurationException("Invalid operator '$operator' in If Stage configuration");
        }
        return new IfStage($configuration);
    }
}