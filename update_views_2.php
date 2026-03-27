<?php

$dir = new RecursiveDirectoryIterator('c:\Users\akidi\Desktop\Smart Event & Venue Booking System\resources\views');
$iter = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iter, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($files as $file) {
    if (is_array($file))
        $file = $file[0];

    $content = file_get_contents($file);

    // Replace standalone `<img src="..." alt="...">` with Tailwind responsive classes where it makes sense
    $content = preg_replace('/<img (.*?)class="([^"]*?)"(.*?)>/', '<img $1class="$2 w-full h-auto object-cover"$3>', $content);
    $content = preg_replace('/<img (?!.*class=)(.*?)>/', '<img class="w-full h-auto object-cover" $1>', $content);

    // Convert generic form-control wrapper/margins if `d-flex` or something is used
    // Actually, buttons are the main issue. Add `w-full sm:w-auto` to most buttons EXCEPT inside nav/header
    // It's safer to just do a smart regex looking for btn-lg or default action buttons.
    $content = preg_replace('/class="([^"]*btn (?:btn-primary|btn-secondary)(?: btn-lg)?)(?!.*w-full)[^"]*"/', 'class="$1 w-full sm:w-auto"', $content);

    // Save
    file_put_contents($file, $content);
}
echo "Done replacing img and btn classes.";
