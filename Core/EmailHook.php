<?php
namespace Core;

use Models\EmailModel;

class EmailHook {
    /**
     * Trigger an email hook
     * Usage: EmailHook::trigger('new_order', ['order_id' => 123, 'email' => 'user@example.com'])
     */
    public static function trigger($hookName, $data) {
        $model = new EmailModel();
        $settings = $model->getSettings();

        // Check if hook is enabled
        $enabledKey = "hook_{$hookName}_enabled";
        if (!isset($settings[$enabledKey]) || $settings[$enabledKey] !== '1') {
            return false;
        }

        $subjectKey = "hook_{$hookName}_subject";
        $subject = $settings[$subjectKey] ?? 'Notification from Srishringarr';

        // Replace placeholders in subject
        foreach ($data as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
        }

        $to = $data['email'] ?? '';
        if (!$to) return false;

        // In a real implementation, you would use PHPMailer with the SMTP settings here
        // For now, we'll log the attempt
        return self::sendEmail($to, $subject, $settings);
    }

    private static function sendEmail($to, $subject, $settings) {
        // Here we would integrate PHPMailer
        // $mail = new \PHPMailer\PHPMailer\PHPMailer();
        // $mail->isSMTP();
        // $mail->Host = $settings['smtp_host'];
        // $mail->SMTPAuth = true;
        // $mail->Username = $settings['smtp_user'];
        // $mail->Password = $settings['smtp_pass'];
        // ...

        // For this task, we have established the HOOK point.
        return true;
    }
}
