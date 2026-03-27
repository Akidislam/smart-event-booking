<?php

$dir = new RecursiveDirectoryIterator('c:\Users\akidi\Desktop\Smart Event & Venue Booking System\resources\views');
$iter = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iter, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($files as $file) {
    if (is_array($file))
        $file = $file[0];

    $content = file_get_contents($file);

    // Replace Container
    $content = str_replace('class="container"', 'class="container mx-auto px-4"', $content);
    $content = str_replace('class="container-sm"', 'class="container mx-auto px-4 max-w-3xl"', $content);
    $content = preg_replace('/class="([^"]*)container([^"]*)"/', 'class="$1container mx-auto px-4$2"', $content);

    // Clean double mx-auto
    $content = str_replace('mx-auto px-4 mx-auto px-4', 'mx-auto px-4', $content);

    // Replace Grids
    $content = preg_replace('/class="([^"]*)grid-2([^"]*)"/', 'class="$1grid grid-cols-1 md:grid-cols-2 gap-6$2"', $content);
    $content = preg_replace('/class="([^"]*)grid-3([^"]*)"/', 'class="$1grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6$2"', $content);
    $content = preg_replace('/class="([^"]*)grid-4([^"]*)"/', 'class="$1grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6$2"', $content);

    // Replace Button widths (for layout structure where appropriate)
    // Only if they are primary or secondary, add w-full sm:w-auto
    if (!str_contains($file, 'layouts') && !str_contains($file, 'nav')) {
        $content = preg_replace('/class="([^"]*btn (?:btn-primary|btn-secondary|btn-outline)[^"]*)"/', 'class="$1 w-full sm:w-auto"', $content);
        // Clean double
        $content = str_replace('w-full sm:w-auto w-full sm:w-auto', 'w-full sm:w-auto', $content);
    }

    // Replace standalone `<img src="..." alt="...">`
    $content = preg_replace('/<img (.*?)class="([^"]*?)"(.*?)>/', '<img $1class="$2 w-full h-auto object-cover"$3>', $content);
    $content = preg_replace('/<img (?!.*class=)(.*?)>/', '<img class="w-full h-auto object-cover" $1>', $content);

    // If it's the app layout, add Tailwind script and overflow-x-hidden
    if (str_contains(basename($file), 'app.blade.php')) {
        if (!str_contains($content, 'cdn.tailwindcss.com')) {
            $content = str_replace('</head>', "    <script src=\"https://cdn.tailwindcss.com\"></script>\n    <script>\n      tailwind.config = {\n        corePlugins: {\n          preflight: false,\n        }\n      }\n    </script>\n</head>", $content);
        }
        $content = str_replace('<body>', '<body class="overflow-x-hidden">', $content);
    }

    // Save
    file_put_contents($file, $content);
}
echo "Done.";
