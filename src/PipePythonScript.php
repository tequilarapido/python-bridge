<?php

namespace Tequilarapido\PythonBridge;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PipePythonScript
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
     * Set python executable/binary path
     *
     * @param $pythonExecutable
     * @return $this
     */
    public function setPythonExecutable($pythonExecutable)
    {
        $this->pythonExecutable = $pythonExecutable;

        return $this;
    }

    /**
     * Sets the command to pipe to the script.
     *
     * @param strin $pipe
     *
     * @return Response
     * @throws PythonException
     */
    public function pipe($pipe)
    {
        $this->pipe = $pipe;

        return $this->run();
    }

    /**
     * Sets echo pipe. Passing simple string to the python script
     * ie. echo 'a simple string' | python script.py
     *
     * @param string $string
     *
     * @return Response
     * @throws PythonException
     */
    public function echoPipe($string)
    {
        $escaped = str_replace("'", "\'", $string);

        return $this->pipe("echo '" . $escaped . "'");
    }

    /**
     * Run the script and return output.
     *
     * @return Response
     * @throws PythonException
     */
    public function run()
    {
        $this->beforeRun();

        $process = new Process($this->getCommand());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new PythonException(
                (new ProcessFailedException($process))->getMessage()
            );
        }

        return new Response($process->getOutput());
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
     * Hook to be run stuff before the script execution.
     */
    protected function beforeRun()
    {
        $this->setUtf8Context();
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
