<?php
namespace Marcosiino\Pipeflow\Stages\ForEach;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;

class ForEachStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;
    
    protected array $allowedSubStagesBlocks = ['do'];

    public function __construct($stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    public function execute(PipelineContext $context): PipelineContext
    {
        //Inputs
        $collection = $this->stageConfiguration->getSettingValue("collection", $context, true);
        if(!is_array($collection)) {
            throw new \Marcosiino\Pipeflow\Exceptions\PipelineExecutionException("The 'collection' setting in ForEach Stage must be an array");
        }

        $doBlockStages = $this->subStagesBlocks['do'];

        if(!isset($doBlockStages) || !is_array($doBlockStages) || count($doBlockStages) === 0) {   
            throw new \Marcosiino\Pipeflow\Exceptions\PipelineExecutionException("ForEach stage must contain at least a stage in the 'do' block");
        }

        foreach($collection as $index => $item) {
            // Set context parameters for current item and index
            $context->setParameter("currentItem", $item);
            $context->setParameter("currentItem_index", $index);

            foreach ($doBlockStages as $stage) {
               // Execute each stage in the 'then' block
                $context = $stage->execute($context);
            }
        }
        //Output
        return $context;
    }
}