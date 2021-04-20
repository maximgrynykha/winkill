![Build Status](https://img.shields.io/github/workflow/status/ismaxim/terminator/Build?label=build&logo=github&logoColor=white&style=for-the-badge)

# __Terminator__

## ⚙️ Installation

To install this library - run the command below in your terminal:

```shell
composer require ismaxim/terminator
```

## 🧙 Usage  

### 🚀 Start

```php

$processes = new Processes;

// Get all active processes
print_r($processes->get());

// OR

// Update all active processes
print_r($processes->update());

/* 
    OUTPUT FORMAT: 

    [0] => Terminator\Kernel\Process Object
        (
            [process_name] => System Idle Process
            [process_id] => 0
            [session_name] => Services
            [session_number] => 0
            [consumed_memory] => 8
        )

    ... (list continues)
*/
```

### 📮 Retrieve processes

```php
$processes->where(Attributes::process_name(), "chrome")
    ->get();

$processes->where(Attributes::process_id(), 11455)
    ->get();

$processes->where(Attributes::session_name(), "console")
    ->get();

$processes->where(Attributes::session_number(), 1)
    ->get();

// Note consumed memory is estimates in Kb(kilobytes)
$processes->where(Attributes::consumed_memory(), 128920)
    ->get(); 
```

### 🧨 Terminate processes

```php
$processes->where(Attributes::process_name(), "chrome")
    ->terminate();

$processes->where(Attributes::process_id(), 11455)
    ->terminate();

$processes->where(Attributes::session_name(), "console")
    ->terminate();

$processes->where(Attributes::session_number(), 1)
    ->terminate();

// Note consumed memory is estimates in Kb(kilobytes)
$processes->where(Attributes::consumed_memory(), 128920)
    ->terminate(); 
```

## 🧱 Useful snippets

```php
/* 
    For processing processes by an array of names or ids 
    use native PHP functions array_map() either array_walk():
*/

// Terminate processes by an array of process names *(all names is example)

array_walk($processes_names = ["chrome", "firefox", "slack"], 
    fn($process_name) => $processes
        ->where(Attributes::process_name(), $process_name)
        ->terminate()
);

// Terminate processes by an array of process ids *(all ids is example)

array_walk($processes_ids = [1000, 5595, 17820], 
    fn($process_id) => $processes
        ->where(Attributes::process_id(), $process_id)
        ->terminate()
);
```

```php
// Sort processes on consumed memory by DESC

usort(
    $processes->where(Attributes::consumed_memory(), 3000, ">=")->get(), 
    function (
        \Terminator\Kernel\Process $process, 
        \Terminator\Kernel\Process $_process
    ) {
        return $process->consumed_memory > $_process->consumed_memory;
});

// Sort processes on consumed memory by DESC

usort(
    $processes->where(Attributes::consumed_memory(), 3000, ">=")->get(), 
    function (
        \Terminator\Kernel\Process $process, 
        \Terminator\Kernel\Process $_process
    ) {
        return $process->consumed_memory < $_process->consumed_memory;
});
```

## 🧪 Testing

_Actually, all tests already automatically passed within CI build._

To test this library - run the command below in your terminal.

```shell
composer test
```

## 🤝 Contributing

If you have a problem that cannot be solved using this library, please write your solution and if you want to help other developers who also use this library (or if you want to keep your solution working after a new version is released, which will go to package manager dependencies) - create a pull-request. We will be happy to add your excellent code to the library!

🐞 Report any bugs or issues you find on the [GitHub issues](https://github.com/ismaxim/urling/issues).

## 📎 Credits
- [Maintainer](https://github.com/ismaxim)

## 📃 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
