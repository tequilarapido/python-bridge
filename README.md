
# tequilrapido/python-bridge

```
 $output = (new PipePythonRunner)
            ->setPythonExecutable('/path/to/python/executable')
            ->setPipe("something to pipe to the script")
            ->setScript("realpath/to/the/python/script")
            ->run();
```