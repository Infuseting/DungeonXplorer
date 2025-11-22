<?php

/**
 * Render a view with a layout
 * 
 * @param string $view Path to the view file (relative to Views directory)
 * @param array $data Data to pass to the view
 * @param string $layout Layout to use (default: 'base')
 */
function view($view, $data = [], $layout = 'base') {
    // Extract data to variables
    extract($data);
    
    // Start output buffering for the view content
    ob_start();
    require __DIR__ . "/../Views/{$view}.php";
    $content = ob_get_clean();
    
    // Render with layout
    require __DIR__ . "/../Views/layouts/{$layout}.php";
}

/**
 * Include a component
 * 
 * @param string $component Component name
 * @param array $data Data to pass to the component
 */
function component($component, $data = []) {
    extract($data);
    require __DIR__ . "/../Views/components/{$component}.php";
}
