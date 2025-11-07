# Pipeflow PHP

Pipeflow is a lightweight pipeline engine for PHP applications. It lets you describe complex automations as a sequence of small, reusable processing steps called stages. The real superpower is that the entire flow can be expressed in a clear XML format that is easy to read, visualise, and reason about—so even non-developers can review, maintain, and update automations without touching PHP code. Each stage receives a shared context, performs a focused unit of work, and returns the enriched context to the next stage. By chaining stages together you can orchestrate complex jobs while keeping each piece easy to maintain and test.

## Why Pipeflow matters
- **Human-friendly configuration** – describe automations in an XML document that business users and developers alike can read, review, and edit safely.
- **Composable workflows** – build sophisticated automations by wiring together focused stages instead of writing one-off scripts.
- **Consistent execution model** – every stage works with the same `PipelineContext`, making it straightforward to pass data between steps.
- **Configurable runtime** – author pipelines either in PHP or in XML, choose the configuration style that best fits your team.
- **Extensible catalogue** – register your own custom stages to integrate third-party services, generative AI calls, or bespoke business logic.

## Installation and Documentation
The full reference, including installation instructions, quick start, and detailed stage catalogue, lives in [DOCUMENTATION.md](DOCUMENTATION.md).

## Quick introduction to pipelines
A pipeline describes the ordered stages that should run and the data they exchange. Typical stages read or write values from the `PipelineContext` which is a container which contains parameters and their values, transform data, or control the flow of execution (loops, conditionals, etc.). The pipeline starts with an empty context, but you can feed a starting context from code if you want to provide the pipeline with prepared data to be available to the stages.

Each stage can reads and write data (parameters) to the context and perform operations (even custom operations by implementing custom stages, like calling apis, performing custom business logic operations, etc...), which can then be read and manipulated by the subsequent stages, until the pipelines finish the execution. At that point, the manipulated context is returned by the pipeline (with all the parameters written by the stages that has been executed).

### Configuring with XML
Pipelines can be declared in XML so they can be edited without touching PHP code. A minimal XML pipeline looks like this:

```xml
<?xml version="1.0" encoding="utf-8"?>
<pipeline id="hello_world">
  <stages>
    <stage type="SetValue">
      <settings>
        <param name="parameterName">message</param>
        <param name="parameterValue">Hello Pipeflow!</param>
      </settings>
    </stage>
    <stage type="Echo">
      <settings>
        <param name="text">%%message%%</param>
      </settings>
    </stage>
  </stages>
</pipeline>
```

Your application tells pipeflow to load the XML configuration, pipeflow will automatically configure the pipeline and prepares it for execution. Your application can then launch the pipeline when it's needed simply by calling the execute() method on the pipeline instance (on demand or even via a cron). 
If you want, you can also pass a pre-defined starting context (if you want to feed, for example, some data from code into the pipeline execution, that can be used by the stages).

Because the pipeline definition is data-driven, you can adjust parameters or reorder stages without redeploying code.

### Extending with custom stages
Pipeflow ships with a catalogue of built-in stages, but you can register your own custom stages to integrate APIs, internal systems, or platform-specific behaviour. Once registered, custom stages become available to both PHP and XML pipelines, letting you reuse them across projects.

## Example use cases
- **Editorial automation for WordPress** – fetch posts, transform content, and trigger publication flows from scheduled pipelines, with editors able to tweak behaviour directly in XML.
- **Back-office data processing** – build nightly ETL jobs that consume feeds, clean data, and sync results to downstream services without redeploying code.
- **Marketing and CRM orchestration** – enrich leads, call external APIs, and keep SaaS tools in sync while letting stakeholders adjust logic themselves.
- **AI-assisted content workflows** – combine prompt generation, randomisation, and templating stages to automate creative tasks.

## Contribute to Pipeflow
Pipeflow thrives on community input. Whether you want to improve the core engine, add new built-in stages, or share feedback from real-world deployments, we would love to collaborate. Check out [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on reporting issues, proposing ideas, and submitting pull requests.

