<?php


class AppController {
    protected function render(string $filename = null, array $variables = []) {
        $templatePath = 'public/views/'. $filename.'.html';
        $templatePath404 = 'public/views/404.html';
        $output = "";
                 
        if(file_exists($templatePath)){
            extract($variables);
            
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        } else {
            ob_start();
            include $templatePath404;
            $output = ob_get_clean();
        }

        echo $output;
    }

}