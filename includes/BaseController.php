<?php

class BaseController 
{
    /**
     * Render view với layout
     */
    public function render($view, $data = []) 
    {
        // Extract data để sử dụng trong view
        extract($data);
        
        // Bắt đầu output buffering
        ob_start();
        
        // Include view file
        $viewFile = "app/views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View file not found: {$viewFile}";
        }
        
        // Lấy nội dung view
        $content = ob_get_clean();
        
        // Include layout mới với content
        include 'app/views/layouts/layout.php';
    }
    
    /**
     * Render view không có layout
     */
    public function renderPartial($view, $data = []) 
    {
        extract($data);
        $viewFile = "app/views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View file not found: {$viewFile}";
        }
    }
    
    /**
     * Redirect đến URL
     */
    public function redirect($url) 
    {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Trả về JSON response
     */
    public function json($data) 
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}