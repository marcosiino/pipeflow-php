<?php
namespace Marcosiino\Pipeflow\Stages\For;

use Marcosiino\Pipeflow\Interfaces\AbstractPipelineStage;
use Marcosiino\Pipeflow\Core\StageConfiguration\StageConfiguration;
use Marcosiino\Pipeflow\Core\PipelineContext;

class ForStage extends AbstractPipelineStage
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
        $from = $this->stageConfiguration->getSettingValue("from", $context, true);
        $to = $this->stageConfiguration->getSettingValue("to", $context, true);
        $step = $this->stageConfiguration->getSettingValue("step", $context, false, 1);
        $indexParameterName = $this->stageConfiguration->getSettingValue("indexParameterName", $context, false, "currentIndex");

        $doBlockStages = $this->subStagesBlocks['do'];

        if(!isset($doBlockStages) || !is_array($doBlockStages) || count($doBlockStages) === 0) {   
            throw new \Marcosiino\Pipeflow\Exceptions\PipelineExecutionException("For stage must contain at least a stage in the 'do' block");
        }

        if($step == 0) {
            throw new \Marcosiino\Pipeflow\Exceptions\PipelineExecutionException("The 'step' setting in For Stage must be different than zero");
        }

        // Handle wrong direction loops
        if($step > 0 && $from >= $to) {
            return $context; // No iterations
        }
        else if ($step < 0 && $from <= $to) {
            return $context; // No iterations
        }

        // Loop
        for($i = $from; ($step > 0) ? $i < $to : $i > $to; $i += $step) {
            // Set context parameter for current index
            $context->setParameter($indexParameterName, $i);

            foreach ($doBlockStages as $stage) {
               // Execute each stage in the 'then' block
                $context = $stage->execute($context);
            }
        }
        //Output
        return $context;
    }
}