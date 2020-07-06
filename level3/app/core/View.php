<?php


namespace app\core;

/**
 * Manages view templates and data rendering.
 */
class View {
    private $templateName;

    /**
     * Constructs and gets required template name.
     */
    public function __construct($path) {
        $this->templateName = $path['defaultAction'];
    }

    /**
     * Loads required template and extracts received data to render.
     * @param $data
     */
    public function render($data) {
        $receivedData = $data;
        ob_start();
        extract($receivedData);
        require '../views/templates/' . $this->templateName . '.php';
        $content = ob_get_clean();
        require '../views/default.php';
    }
}