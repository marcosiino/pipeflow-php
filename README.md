# Pipeflow PHP

Pipeflow is a lightweight pipeline engine for PHP applications. It lets you describe complex automations as a sequence of small, reusable processing steps called stages. The real superpower is that the entire flow can be expressed in a clear XML format that is easy to read, visualise, and reason about—so even non-developers can review, maintain, and update automations without touching PHP code (but you can also configure the pipelines via hard coded php code). 
Each stage receives a shared context, performs a focused unit of work, and returns the enriched context to the next stage. By chaining stages together you can orchestrate complex jobs while keeping each piece easy to maintain and test.

In other words Pipeflow library gives you the instruments to instantiate one or more pipelines from an xml configuration, providing starting data in an initial context (optionally), and execute them when you want. What you will need to do is use these instruments in your web application to allow your actors to: edit the pipeline's configurations xml (via a text editor), save the pipeline xml configuration somewhere (e.g. your application db), and, when your application need to start a pipeline (manually or through a cron), just load the xml, feed it in the Pipeline class instance, and launch it.

## Table of Contents

- [Why Pipeflow matters](#why-pipeflow-matters)
- [Real Use Cases](#real-use-cases)
- [Other example use cases](#other-example-use-cases)
- [Installation and Documentation](#installation-and-documentation)
- [Quick introduction to pipelines](#quick-introduction-to-pipelines)
  - [Configuring with XML](#configuring-with-xml)
  - [Configuring programmatically via PHP](#configuring-programmatically-via-php)
- [Extending with custom stages](#extending-with-custom-stages)
- [Learn more](#learn-more)
- [Contribute to PipeFlow](#contribute-to-pipeflow)
- [License](#license)

## Why Pipeflow matters
- **Human-friendly configuration** – describe automations in an XML document that business users and developers alike can read, review, and edit safely.
- **Composable workflows** – build sophisticated automations by wiring together focused stages instead of writing one-off scripts.
- **Consistent execution model** – every stage works with the same `PipelineContext`, making it straightforward to pass data between steps.
- **Configurable runtime** – author pipelines either in PHP or in XML, choose the configuration style that best fits your team.
- **Extensible catalogue** – register your own custom stages to integrate third-party services, generative AI calls, or bespoke business logic.

## Real Use Cases
Here is some real use cases which leverages the power of PipeFlow

- [PagineDaColorare.it](https://paginedacolorare.it/): A wordpress website that automatically create and published coloring pages for children, daily, using the AI. This website uses two wordpress plugins I've developed (that maybe i will publish in future): one of them exposes pipeflow-php into wordpress, adding some custom stages to manage wordpress (creating posts, saving images, setting custom fields, category and tags) and allowing to modify the pipeline's xml from the wordpress admin panel (so that i can refine it, improve the content creation logic, change the logic on holidays, i.e. Christmas, and so on). The other plugins adds some more custom stages to pipeflow which allows to generate text and images with OpenAI apis. All these new custom stages is then used together to automatically run the coloring page content generation pipeline daily, with a cron.
The advantage is that anyone, even non-developers, can refine, mantain, edit the coloring page content generation pipeline logic, simply by changing the XML configuration in the wordpress admin panel.
The coloring page content generation pipeline configuration is now quite complex, but is very easy to read, understand and mantain: it combines many different stage types which randomizes coloring pages themes, subjects, actions, asking the supporting of the AI in different phases of the pipeline execution.

- [Fiaberello.it](https://fiaberello.it/): Similar to the website above, this is another website I've developed with the power of pipeflow. It automaticallys creates and publish fairy tales for children, with a cover image for each tale. This is more a test/example, it's pipeline is more simple and refined than the previous one, so it can be even improved. 

## Other example use cases

- **Editorial automation for any CMS** – Create a plugin for your CMS which leverage pipeflow to build custom workflows which can be easily edited and refined by any actor in your team, even non developers: fetch posts, transform content, and trigger publication flows from scheduled pipelines, with editors able to tweak behaviour directly in XML, allowing any actor in your team (including non-developers) to mantain, refine, change the workflow.

- **Back-office data processing** – build nightly ETL jobs that consume feeds, clean data, and sync results to downstream services without redeploying code.

- **Marketing and CRM orchestration** – enrich leads, call external APIs, and keep SaaS tools in sync while letting stakeholders adjust logic themselves.

- **AI-assisted content workflows** – combine prompt generation, randomisation, and templating stages to automate creative tasks, like i did on the websites above.

- **Any automation/workflow you can image, easily mantained by even non-developers** - By allowing to create custom stages, you can encapsulate your custom business logic in new custom stages, which then can be used in your pipelines. These pipelines can then be edited, mantained or refined by any actor in your team, easily and visually by an easy to reason and read XML configuration.

## Installation and Documentation
The full reference, including installation instructions, quick start, and detailed stage catalogue, lives in [DOCUMENTATION.md](DOCUMENTATION.md).

## Quick introduction to pipelines
A pipeline describes the ordered stages that should run and the data they exchange. Typical stages read or write values from the `PipelineContext` which is a container which contains parameters and their values, transform data, or control the flow of execution (loops, conditionals, etc.). The pipeline starts with an empty context, but you can feed a starting context from code if you want to provide the pipeline with prepared data to be available to the stages.

Each stage can reads and write data (parameters) to the context and perform operations (even custom operations by implementing custom stages, like calling apis, performing custom business logic operations, etc...), which can then be read and manipulated by the subsequent stages, until the pipelines finish the execution. At that point, the manipulated context is returned by the pipeline (with all the parameters written by the stages that has been executed).

### Configuring with XML
Pipelines can be declared in XML so they can be edited without touching PHP code. A minimal XML pipeline looks like the following.

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

### Configuring programmatically via PHP
Since XML is the easier way to configure pipelines "visually" and allows also non-developers to edit and mantain them by enabling any actor to manage automations in your web applications, you can also configure the pipelines programmatically in your php code. This may have sense for example for those business logic automations that doesn't need to be edited/mantained from your application administration panels, are fixed (doesn't change often), or doesn't need to be mantained by non-developers actors. More info in the [DOCUMENTATION.md](DOCUMENTATION.md)

## Extending with custom stages
Pipeflow ships with a catalogue of built-in stages, but you can register your own custom stages to integrate APIs, internal systems, or platform-specific behaviour. Once registered, custom stages become available to both PHP and XML pipelines, letting you reuse them across projects.

## Learn more
The full reference, including installation instructions, control-flow stages, and detailed stage catalogue, lives in [DOCUMENTATION.md](DOCUMENTATION.md).

## Contribute to Pipeflow
Pipeflow thrives on community input and it surely needs improvements and features: Whether you want to improve the core engine, add new features, add new built-in stages, fix bugs, or share feedback from real-world deployments, we would love to collaborate. 
Check out [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on reporting issues, proposing ideas, and submitting pull requests.

## License
Pipeflow is distributed under the permissive [BSD 3-Clause License](LICENSE), which keeps the project friendly for both open-source and commercial use while encouraging community contributions.