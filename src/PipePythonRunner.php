<?php

namespace Tequilarapido\PythonBridge;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PipePythonRunner
{
    /**
     * Full path to python script file.
     *
     * @var string the python script to run
     */
    protected $script;

    /**
     * The command to pipe to the python script.
     *
     * @var string the command to pipe to the script ($ <pipe command> | python)
     */
    protected $pipe;

    /**
     * Python executable path.
     * By default we will use `python3`
     *
     * @var string
     */
    protected $pythonExecutable = 'python3';

    /**
     * Sets the python script full path to run.
     *
     * @param $script
     * @return $this
     */
    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * Sets the command to pipe to the script.
     *
     * @param $pipe
     * @return $this
     */
    public function setPipe($pipe)
    {
        $this->pipe = $pipe;

        return $this;
    }

    public function setPythonExecutable($pythonExecutable)
    {
        $this->pythonExecutable = $pythonExecutable;

        return $this;
    }

    /**
     * Run the script and return output.
     *
     * @return string output
     * @throws PythonException
     */
    public function run()
    {
        $this->setUtf8Context();
        $process = new Process($this->getCommand());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new PythonException(
                (new ProcessFailedException($process))->getMessage()
            );
        }

        return $process->getOutput();
    }

    /**
     * Build the command to execute.
     *
     * @return string
     */
    public function getCommand()
    {
        return "{$this->pipe} | {$this->pythonExecutable} " . $this->script;
    }

    /**
     * Fix issues where when python get executed by
     * the php process it will defaults to ascii and tries to
     * read strings in ascii, and mess everythings up.
     *
     * @see https://stackoverflow.com/a/13969829/146253
     */
    protected function setUtf8Context()
    {
        $locale = 'en_US.UTF-8';
        setlocale(LC_ALL, $locale);
        putenv('LC_ALL=' . $locale);
    }
}
