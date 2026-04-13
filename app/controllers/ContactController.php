<?php

class ContactController extends BaseController 
{
    public function index() 
    {
        // Check for a success message in the session
        $message = $_SESSION['contact_form_success'] ?? null;
        // Unset the session variable so it doesn't show again on refresh
        unset($_SESSION['contact_form_success']);

        // Pass the message to the view
        $this->render('contact/index', ['message' => $message]);
    }

    public function send() 
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Here you would normally process the form data,
            // like sending an email or saving to a database.
            // For now, we'll just simulate a successful submission.
            $name = htmlspecialchars($_POST['name']);
            // Set a success message in the session
            $_SESSION['contact_form_success'] = "Cảm ơn, $name! Tin nhắn của bạn đã được gửi thành công. Chúng tôi sẽ liên hệ lại với bạn sớm nhất có thể.";

            // Redirect back to the contact page
            header("Location: ?page=contact");
            exit();
        } else {
            // If not a POST request, just redirect to the contact page
            header("Location: ?page=contact");
            exit();
        }
    }
}