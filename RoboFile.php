<?php

class RoboFile extends \Robo\Tasks
{
    /**
     * Code generation from events.yml
     */
    public function generateEvents()
    {
        $loader = new \EventSauce\EventSourcing\CodeGeneration\YamlDefinitionLoader();
        $dumper = new \EventSauce\EventSourcing\CodeGeneration\CodeDumper();
        $phpCode = $dumper->dump($loader->load(__DIR__ . '/config/events.yml'));
        file_put_contents(__DIR__ . '/generated/events.php', $phpCode);

    }
}