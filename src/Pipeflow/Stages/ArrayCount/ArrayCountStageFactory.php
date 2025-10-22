<?php
namespace Marcosiino\Pipeflow\Stages\ArrayCount;

use Marcosiino\Pipeflow\Interfaces\AbstractStageFactory;
use Marcosiino\Pipeflow\Utils;

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/ArrayCount/ArrayCountStage.php";

class ArrayCountStageFactory implements AbstractStageFactory
{
    public function getStageDescriptor(): StageDescriptor
    {
        $stageDescription = "Counts the items in the specified array context parameter.";

        // Setup Parameters
        $setupParams = array(
            "arrayParameterName" => "(required) The name of the context parameter which contains the array of which items will be counted.",
            "resultTo" => "(required) The output context parameter where the item count is saved",
        );

        // Context inputs
        $contextInputs = array();

        // Context outputs
        $contextOutputs = array(
            "" => "The array items count is saved into the context parameter specified in resultTo setting.",
        );

        return new StageDescriptor("ArrayCount", $stageDescription, $setupParams, $contextInputs, $contextOutputs);
    }

    /**
     * @throws StageConfigurationException
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        // TODO validate $configuration to check if it contains all the required fields
        return new ArrayCountStage($configuration);
    }
}