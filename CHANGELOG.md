# Changelog

## 0.9.0

Pipeflow is now a standalone PHP library (it was born as a wordpress automation plugin, which I took apart and splitted from the generic core pipeline logic and stages from which pipeflow-php is born). 

Also, new important stages has been introduced in this release, which allows to introduce control flow in pipeflow's pipelines:

### New Stages 

- If: allows to implement if-else control flow in pipelines, by executing different sub-stages paths depending on conditions.

- ForEach: allows to implement for-each loops in pipelines to iterate through collections and execute sub-stages for each item of the collection.

- For: allows to implement for loops in pipelines, by executing sub-stages for each iteration.